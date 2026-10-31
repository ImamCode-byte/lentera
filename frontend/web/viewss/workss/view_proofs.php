<?php
/**
 * view_proofs.php
 * penampil berkas bukti pembayaran apc secara aman dengan validasi hak akses
 * hanya dapat diakses oleh admin, kurator, atau penulis pemilik naskah
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/paymentss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';

sessions::starts();

if (!sessions::hass('user_ids')) {
    http_response_code(401);
    die("Akses ditolak: Silakan masuk terlebih dahulu.");
}

$payment_ids = (int)($_GET['ids'] ?? 0);
$payment_models = new paymentss();
$payment = $payment_models->get_by_ids($payment_ids);

if (!$payment || empty($payment['proof_file'])) {
    http_response_code(404);
    die("Berkas bukti pembayaran tidak ditemukan.");
}

$current_user_ids = (int)sessions::gets('user_ids');
$current_user_roles = sessions::gets('user_roles');
$is_owner = ($current_user_ids === (int)$payment['user_id']);
$is_privileged = in_array($current_user_roles, ['admin', 'curator'], true);

if (!$is_owner && !$is_privileged) {
    http_response_code(403);
    die("Akses ditolak: Anda tidak memiliki hak akses untuk melihat berkas bukti transaksi ini.");
}

$base_paths = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR;
$full_file_paths = $base_paths . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $payment['proof_file']);

if (!file_exists($full_file_paths)) {
    http_response_code(404);
    die("Berkas fisik bukti pembayaran tidak ditemukan di server.");
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $full_file_paths);
finfo_close($finfo);

if (!in_array($mime_type, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])) {
    $mime_type = 'application/octet-stream';
}

header('Content-Type: ' . $mime_type);
header('Content-Disposition: inline; filename="' . basename($full_file_paths) . '"');
header('Content-Length: ' . filesize($full_file_paths));
header('Cache-Control: private, max-age=3600');

readfile($full_file_paths);
exit;
