<?php
/**
 * paymentss.php
 * model pengelolaan transaksi biaya publikasi (article processing charge - apc)
 * mendukung verifikasi pembayaran oleh admin kampus & pemisahan peran
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
            SELECT p.*, w.title AS work_title, w.status AS work_status, u.name AS author_name, u.email AS author_email, u.nim_nip AS author_nim_nip
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

    public function get_by_ids($payment_ids) {
        $querys = "
            SELECT p.*, w.title AS work_title, w.status AS work_status, u.name AS author_name, u.email AS author_email, u.nim_nip AS author_nim_nip
            FROM paymentss p
            JOIN works w ON p.work_id = w.id
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :payment_ids
            LIMIT 1
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':payment_ids' => $payment_ids]);
        return $statements->fetch();
    }

    /**
     * Penulis mengunggah bukti transfer, status invoice tetap 'pending' menunggu verifikasi admin
     */
    public function submit_payment_proof($payment_ids, $datas) {
        $querys = "
            UPDATE paymentss 
            SET sender_name = :sender_names,
                reference_number = :reference_numbers,
                proof_file = :proof_files,
                status = 'pending',
                admin_notes = NULL,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :payment_ids
        ";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([
            ':sender_names'      => $datas['sender_names'] ?? '',
            ':reference_numbers' => $datas['reference_numbers'] ?? '',
            ':proof_files'       => $datas['proof_files'] ?? null,
            ':payment_ids'       => $payment_ids,
        ]);
    }

    /**
     * Admin repositori memverifikasi pembayaran dan menerbitkan karya ilmiah
     */
    public function verify_and_publish($payment_ids, $admin_id = 1) {
        $payment = $this->get_by_ids($payment_ids);
        if (!$payment) return false;

        $work_ids = (int)$payment['work_id'];

        try {
            $this->db_conns->beginTransaction();

            // 1. Update status pembayaran menjadi completed
            $query_pay = "
                UPDATE paymentss 
                SET status = 'completed',
                    paid_at = CURRENT_TIMESTAMP,
                    verified_by = :admin_id,
                    admin_notes = 'Pembayaran terverifikasi valid oleh Admin Repositori.',
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :payment_ids
            ";
            $stmt_pay = $this->db_conns->prepare($query_pay);
            $stmt_pay->execute([
                ':admin_id'    => $admin_id,
                ':payment_ids' => $payment_ids
            ]);

            // 2. Publikasikan naskah resmi
            $query_work = "
                UPDATE works 
                SET status = 'published',
                    published_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :work_ids
            ";
            $stmt_work = $this->db_conns->prepare($query_work);
            $stmt_work->execute([':work_ids' => $work_ids]);

            // 3. Catat di tabel reviews sebagai rekam jejak persetujuan resmi
            $query_rev = "
                INSERT INTO reviews (work_id, curator_id, action, comment, created_at)
                VALUES (:work_ids, :curator_id, 'approve', :comments, CURRENT_TIMESTAMP)
            ";
            $stmt_rev = $this->db_conns->prepare($query_rev);
            $stmt_rev->execute([
                ':work_ids'    => $work_ids,
                ':curator_id'  => $admin_id,
                ':comments'    => 'Pembayaran APC resmi diverifikasi. Karya ilmiah diterbitkan ke repositori publik LENTERA Universitas Madura.'
            ]);

            $this->db_conns->commit();
            return true;
        } catch (Exception $exceptions) {
            $this->db_conns->rollBack();
            error_log("Gagal verifikasi pembayaran: " . $exceptions->getMessage());
            return false;
        }
    }

    /**
     * Admin menolak bukti transfer karena tidak sah atau dana tidak masuk
     */
    public function reject_payment_proof($payment_ids, $reason, $admin_id = 1) {
        $querys = "
            UPDATE paymentss 
            SET status = 'rejected',
                verified_by = :admin_id,
                admin_notes = :reason,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :payment_ids
        ";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([
            ':admin_id'    => $admin_id,
            ':reason'      => $reason,
            ':payment_ids' => $payment_ids
        ]);
    }

    /**
     * Mengambil daftar transaksi yang membutuhkan verifikasi Admin
     */
    public function get_pending_verifications() {
        $querys = "
            SELECT p.*, w.title AS work_title, w.status AS work_status, 
                   u.name AS author_name, u.email AS author_email, u.nim_nip AS author_nim_nip,
                   sp.name AS prodi_name, f.name AS faculty_name, f.code AS faculty_code
            FROM paymentss p
            JOIN works w ON p.work_id = w.id
            JOIN users u ON p.user_id = u.id
            LEFT JOIN study_programs sp ON w.prodi_id = sp.id
            LEFT JOIN faculties f ON sp.faculty_id = f.id
            ORDER BY CASE WHEN p.status = 'pending' AND p.proof_file IS NOT NULL THEN 1 
                          WHEN p.status = 'pending' THEN 2 
                          WHEN p.status = 'rejected' THEN 3 
                          ELSE 4 END, p.created_at DESC
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute();
        return $statements->fetchAll();
    }

    /**
     * Hitung total transaksi yang menunggu tindakan verifikasi admin
     */
    public function get_pending_count() {
        $querys = "SELECT COUNT(*) FROM paymentss WHERE status = 'pending' AND proof_file IS NOT NULL";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute();
        return (int)$statements->fetchColumn();
    }

    /**
     * Metode auto-publish instan (tersedia sebagai opsi fallback)
     */
    public function complete_and_auto_publish($payment_ids, $work_ids, $datas) {
        try {
            $this->db_conns->beginTransaction();

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

            $query_work = "
                UPDATE works 
                SET status = 'published',
                    published_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :work_ids
            ";
            $stmt_work = $this->db_conns->prepare($query_work);
            $stmt_work->execute([':work_ids' => $work_ids]);

            $query_review = "
                INSERT INTO reviews (work_id, curator_id, action, comment, created_at)
                VALUES (:work_ids, :curator_ids, 'approve', :comments, CURRENT_TIMESTAMP)
            ";
            $stmt_review = $this->db_conns->prepare($query_review);
            $stmt_review->execute([
                ':work_ids'    => $work_ids,
                ':curator_ids' => 1,
                ':comments'    => 'Biaya publikasi (APC) berhasil diverifikasi. Karya diterbitkan ke repositori publik.'
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
