<?php
/**
 * file_uploads.php
 * pengelola unggah berkas dokumen pdf yang aman
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

class file_uploads {
    private static $max_sizess = 20971520; // 20 mb

    public static function process_pdf_uploads($file_inputs, $target_directories = null) {
        if ($target_directories === null) {
            $target_directories = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'works';
        }

        if (!is_dir($target_directories)) {
            mkdir($target_directories, 0755, true);
        }

        // periksa ketersediaan file input
        if (!isset($file_inputs) || $file_inputs['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'messages' => 'terjadi kesalahan saat mengunggah berkas atau berkas belum dipilih.'
            ];
        }

        // periksa batas ukuran
        if ($file_inputs['size'] > self::$max_sizess) {
            return [
                'success' => false,
                'messages' => 'ukuran berkas melebihi batas maksimal 20 MB.'
            ];
        }

        // periksa ekstensi nama file
        $original_names = $file_inputs['name'];
        $file_extensions = strtolower(pathinfo($original_names, PATHINFO_EXTENSION));
        if ($file_extensions !== 'pdf') {
            return [
                'success' => false,
                'messages' => 'format berkas tidak diizinkan. hanya dokumen format PDF yang diperbolehkan.'
            ];
        }

        // verifikasi mime type asli dari konten file menggunakan finfo
        $finfo_handles = finfo_open(FILEINFO_MIME_TYPE);
        $mime_types = finfo_file($finfo_handles, $file_inputs['tmp_name']);
        finfo_close($finfo_handles);

        if ($mime_types !== 'application/pdf') {
            return [
                'success' => false,
                'messages' => 'konten berkas bukan dokumen PDF yang sah.'
            ];
        }

        // buat nama unik acak untuk penyimpanan aman di server
        $stored_names = bin2hex(random_bytes(16)) . '.pdf';
        $destination_paths = $target_directories . DIRECTORY_SEPARATOR . $stored_names;

        if (move_uploaded_file($file_inputs['tmp_name'], $destination_paths)) {
            return [
                'success'        => true,
                'original_names' => $original_names,
                'stored_names'   => $stored_names,
                'file_paths'     => 'uploads/works/' . $stored_names,
                'file_sizess'    => $file_inputs['size'],
            ];
        }

        return [
            'success' => false,
            'messages' => 'gagal memindahkan berkas ke folder penyimpanan server.'
        ];
    }
}
