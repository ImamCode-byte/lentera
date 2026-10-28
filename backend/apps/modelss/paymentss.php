<?php
/**
 * paymentss.php
 * model pengelolaan transaksi biaya publikasi (article processing charge - apc)
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class paymentss {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function get_or_creates_invoices($work_ids, $user_ids, $amounts = 150000.00) {
        // periksa apakah sudah ada invoice untuk naskah ini
        $query_check = "SELECT * FROM paymentss WHERE work_id = :work_ids LIMIT 1";
        $stmt_check = $this->db_conns->prepare($query_check);
        $stmt_check->execute([':work_ids' => $work_ids]);
        $existing = $stmt_check->fetch();

        if ($existing) {
            return $existing;
        }

        // buat nomor invoice unik berbasis timestamp dan id karya
        $invoice_numbers = 'INV-LTR-' . date('Ymd') . '-' . str_pad($work_ids, 4, '0', STR_PAD_LEFT);

        $query_insert = "
            INSERT INTO paymentss (work_id, user_id, invoice_number, amount, payment_method, destination_account, status, created_at)
            VALUES (:work_ids, :user_ids, :invoice_numbers, :amounts, 'QRIS Dhes Mart / DANA', '087849264452 an. Imam (Dhes Mart)', 'pending', CURRENT_TIMESTAMP)
        ";
        $stmt_insert = $this->db_conns->prepare($query_insert);
        $stmt_insert->execute([
            ':work_ids'         => $work_ids,
            ':user_ids'         => $user_ids,
            ':invoice_numbers'  => $invoice_numbers,
            ':amounts'          => $amounts
        ]);

        return $this->get_by_work_ids($work_ids);
    }

    public function get_by_work_ids($work_ids) {
        $querys = "
            SELECT p.*, w.title AS work_title, w.status AS work_status, u.name AS author_name, u.email AS author_email
            FROM paymentss p
            JOIN works w ON p.work_id = w.id
            JOIN users u ON p.user_id = u.id
            WHERE p.work_id = :work_ids
            LIMIT 1
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':work_ids' => $work_ids]);
        return $statements->fetch();
    }

    public function complete_and_auto_publish($payment_ids, $work_ids, $datas) {
        try {
            $this->db_conns->beginTransaction();

            // 1. update data pembayaran menjadi completed
            $query_pay = "
                UPDATE paymentss 
                SET sender_name = :sender_names,
                    reference_number = :reference_numbers,
                    proof_file = :proof_files,
                    status = 'completed',
                    paid_at = CURRENT_TIMESTAMP
                WHERE id = :payment_ids
            ";
            $stmt_pay = $this->db_conns->prepare($query_pay);
            $stmt_pay->execute([
                ':sender_names'       => $datas['sender_names'] ?? '',
                ':reference_numbers'  => $datas['reference_numbers'] ?? '',
                ':proof_files'        => $datas['proof_files'] ?? null,
                ':payment_ids'        => $payment_ids,
            ]);

            // 2. AUTO PUBLISH KARYA SECARA OTOMATIS KE ETALASE PUBLIK
            $query_work = "
                UPDATE works 
                SET status = 'published',
                    published_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :work_ids
            ";
            $stmt_work = $this->db_conns->prepare($query_work);
            $stmt_work->execute([':work_ids' => $work_ids]);

            // 3. catat riwayat kurasi otomatis
            $query_review = "
                INSERT INTO reviews (work_id, curator_id, action, comment, created_at)
                VALUES (:work_ids, :curator_ids, 'approve', :comments, CURRENT_TIMESTAMP)
            ";
            $stmt_review = $this->db_conns->prepare($query_review);
            $stmt_review->execute([
                ':work_ids'    => $work_ids,
                ':curator_ids' => 1, // Sistem Repositori LENTERA
                ':comments'    => 'Biaya publikasi (APC) berhasil dibayar via QRIS Dhes Mart / DANA (087849264452 an. Imam). Sistem secara otomatis menerbitkan karya ke repositori publik.'
            ]);

            $this->db_conns->commit();
            return true;
        } catch (Exception $exceptions) {
            $this->db_conns->rollBack();
            error_log("Gagal memproses pembayaran dan auto-publish: " . $exceptions->getMessage());
            return false;
        }
    }
}