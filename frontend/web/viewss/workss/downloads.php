<?php
/**
 * downloads.php
 * pengelola unduhan berkas naskah karya ilmiah secara aman disertai pencatatan audit log
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';

sessions::starts();

$work_ids = (int)($_GET['ids'] ?? 0);
$work_models = new workss();
$works = $work_models->find_by_ids($work_ids);

if (!$works || empty($works['file_path'])) {
    http_response_code(404);
    die("berkas karya ilmiah tidak ditemukan.");
}

// validasi otorisasi status publikasi karya
$work_statuss = $works['status'] ?? '';
$current_user_ids = sessions::gets('user_ids');
$current_user_roles = sessions::gets('user_roles');
$is_authors = $current_user_ids && ((int)$current_user_ids === (int)($works['author_id'] ?? 0));
$is_curator_or_admins = in_array($current_user_roles, ['curator', 'admin'], true);

if ($work_statuss !== 'published' && !$is_authors && !$is_curator_or_admins) {
    http_response_code(403);
    die("akses ditolak: berkas karya ilmiah hanya dapat diunduh jika karya telah resmi berstatus terbit (published).");
}

// pastikan file fisik ada di server
$base_storage_paths = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR;
$full_file_paths = $base_storage_paths . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $works['file_path']);

if (!file_exists($full_file_paths)) {
    die("berkas fisik dokumen belum tersedia di server.");
}

// catat log unduhan untuk audit statistik akreditasi
$current_user_ids = sessions::gets('user_ids');
$work_models->record_downloads($work_ids, $current_user_ids);

// kirim header download http
$clean_download_names = str_replace(['"', "'", "\r", "\n"], '', $works['original_name'] ?? 'dokumen_karya.pdf');
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $clean_download_names . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($full_file_paths));

readfile($full_file_paths);
exit;
