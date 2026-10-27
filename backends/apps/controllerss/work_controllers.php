<?php
/**
 * work_controllers.php
 * pengontrol pengunggahan, revisi, dan pengelolaan karya ilmiah oleh penulis
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__) . '/modelss/workss.php';
require_once dirname(__DIR__) . '/helperss/file_uploads.php';
require_once dirname(__DIR__) . '/helperss/sessions.php';
require_once dirname(__DIR__) . '/helperss/csrfs.php';
require_once dirname(__DIR__) . '/helperss/validators.php';
require_once dirname(__DIR__) . '/helperss/flashs.php';

class work_controllers {
    private $work_models;

    public function __construct() {
        $this->work_models = new workss();
    }

    public function stores($post_datas, $file_inputs) {
        sessions::starts();

        // pastikan user terautentikasi
        if (!sessions::hass('user_ids')) {
            flashs::sets_error('silakan masuk terlebih dahulu untuk mengunggah karya.');
            return false;
        }

        // verifikasi token csrfs
        if (!csrfs::validates($post_datas['csrf_tokens'] ?? '')) {
            flashs::sets_error('sesi keamanan kedaluwarsa. silakan ulangi proses unggah.');
            return false;
        }

        // validasi input metadata
        $validators = new validators();
        $is_valids = $validators->validates($post_datas, [
            'titles'        => 'required|min:10|max:500',
            'abstracts'     => 'required|min:30',
            'work_type_ids' => 'required|numeric',
            'prodi_ids'     => 'required|numeric',
            'years'         => 'required|numeric',
        ]);

        if (!$is_valids) {
            $error_itemss = $validators->get_errorss();
            $first_errors = reset($error_itemss)[0] ?? 'data karya belum lengkap.';
            flashs::sets_error($first_errors);
            return false;
        }

        // proses upload berkas dokumen pdf
        $upload_results = file_uploads::process_pdf_uploads($file_inputs['document_pdfs'] ?? null);
        if (!$upload_results['success']) {
            flashs::sets_error($upload_results['messages']);
            return false;
        }

        // pisahkan kata kunci (comma separated)
        $keyword_raws = $post_datas['keywordss'] ?? '';
        $keyword_arrays = array_filter(array_map('trim', explode(',', $keyword_raws)));

        // simpan ke database
        $author_ids = (int)sessions::gets('user_ids');
        $work_ids = $this->work_models->creates_works([
            'titles'        => trim($post_datas['titles']),
            'abstracts'     => trim($post_datas['abstracts']),
            'author_ids'    => $author_ids,
            'prodi_ids'     => (int)$post_datas['prodi_ids'],
            'work_type_ids' => (int)$post_datas['work_type_ids'],
            'statuss'       => 'submitted',
            'years'         => (int)$post_datas['years'],
            'supervisors'   => trim($post_datas['supervisors'] ?? ''),
        ], $keyword_arrays, $upload_results);

        if ($work_ids) {
            flashs::sets_success('karya ilmiah berhasil diunggah dan sedang dalam antrean kurasi!');
            return $work_ids;
        }

        flashs::sets_error('gagal menyimpan data karya ilmiah ke basis data.');
        return false;
    }

    public function process_revisions($work_ids, $post_datas, $file_inputs) {
        sessions::starts();

        if (!sessions::hass('user_ids')) {
            flashs::sets_error('silakan masuk terlebih dahulu.');
            return false;
        }

        if (!csrfs::validates($post_datas['csrf_tokens'] ?? '')) {
            flashs::sets_error('sesi keamanan kedaluwarsa.');
            return false;
        }

        // verifikasi kepemilikan karya
        $works = $this->work_models->find_by_ids($work_ids);
        if (!$works || $works['author_id'] != sessions::gets('user_ids')) {
            flashs::sets_error('anda tidak memiliki izin untuk merevisi karya ini.');
            return false;
        }

        // upload file versi baru
        $upload_results = file_uploads::process_pdf_uploads($file_inputs['document_pdfs'] ?? null);
        if (!$upload_results['success']) {
            flashs::sets_error($upload_results['messages']);
            return false;
        }

        $successs = $this->work_models->adds_revisions($work_ids, $upload_results, $post_datas['revision_notess'] ?? '');
        if ($successs) {
            flashs::sets_success('dokumen revisi berhasil diunggah! karya dikembalikan ke antrean kurator.');
            return true;
        }

        flashs::sets_error('gagal memperbarui dokumen revisi.');
        return false;
    }
}
