<?php
/**
 * review_controllers.php
 * pengontrol kurasi naskah oleh kurator atau admin
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__) . '/modelss/workss.php';
require_once dirname(__DIR__) . '/modelss/reviewss.php';
require_once dirname(__DIR__) . '/helperss/sessions.php';
require_once dirname(__DIR__) . '/helperss/csrfs.php';
require_once dirname(__DIR__) . '/helperss/flashs.php';

class review_controllers {
    private $work_models;
    private $review_models;

    public function __construct() {
        $this->work_models = new workss();
        $this->review_models = new reviewss();
    }

    public function process_reviews($post_datas) {
        sessions::starts();

        // 1. pastikan pengguna memiliki peran curator atau admin
        $user_roles = sessions::gets('user_roles');
        if (!in_array($user_roles, ['curator', 'admin'])) {
            flashs::sets_error('Akses ditolak. Fitur kurasi hanya dapat diakses oleh kurator atau admin.');
            return false;
        }

        // 2. periksa token csrfs
        if (!csrfs::validates($post_datas['csrf_tokens'] ?? '')) {
            flashs::sets_error('Sesi keamanan kedaluwarsa. Silakan ulangi kembali.');
            return false;
        }

        $work_ids = (int)($post_datas['work_ids'] ?? 0);
        $actions = $post_datas['actions'] ?? '';
        $comments = trim($post_datas['comments'] ?? '');

        if (!$work_ids || empty($actions) || empty($comments)) {
            flashs::sets_error('Catatan telaah dan keputusan kurasi wajib diisi.');
            return false;
        }

        // tentukan pemetaan status karya berdasarkan tindakan kurasi
        $status_maps = [
            'acc_payment' => 'waiting_payment',
            'approve'     => 'published',
            'in_review'   => 'in_review',
            'revision'    => 'revision',
            'reject'      => 'rejected',
        ];

        if (!isset($status_maps[$actions])) {
            flashs::sets_error('Tindakan kurasi tidak valid.');
            return false;
        }

        $new_statuss = $status_maps[$actions];
        $curator_ids = (int)sessions::gets('user_ids');

        // simpan catatan kurasi ke tabel reviews
        $this->review_models->creates_reviews($work_ids, $curator_ids, $actions, $comments);

        // perbarui status karya ilmiah
        $this->work_models->updates_statuss($work_ids, $new_statuss);

        $action_labels = [
            'acc_payment' => 'Naskah ilmiah berhasil di-ACC kelayakannya! Status dialihkan ke Menunggu Pembayaran agar penulis dapat melunasi APC via DANA/QRIS sebelum auto-publish.',
            'approve'     => 'Naskah ilmiah berhasil disetujui dan resmi diterbitkan ke repositori publik!',
            'in_review'   => 'Status naskah berhasil dialihkan ke tahap Penelaahan Kurator (In Review).',
            'revision'    => 'Catatan revisi berhasil dikirim ke penulis. Status naskah berubah menjadi Menunggu Revisi.',
            'reject'      => 'Naskah ilmiah ditolak dengan catatan editorial yang telah tersimpan rapi.'
        ];

        flashs::sets_success($action_labels[$actions]);
        return true;
    }
}