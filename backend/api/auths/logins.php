<?php
/**
 * logins.php
 * REST API Endpoint: Autentikasi Pengguna & Penerbitan Token Mobile
 * Method: POST
 */

require_once dirname(__DIR__, 2) . '/configs/apps.php';
require_once dirname(__DIR__, 2) . '/configs/databases.php';
require_once dirname(__DIR__, 2) . '/apps/modelss/users.php';
require_once dirname(__DIR__) . '/helperss/api_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan POST.', 405);
}

$inputs = api_helpers::get_json_inputs();
if (empty($inputs)) {
    $inputs = $_POST;
}

$identifiers = trim($inputs['identifiers'] ?? $inputs['username'] ?? $inputs['email'] ?? $inputs['nim'] ?? '');
$passwords   = (string)($inputs['passwords'] ?? $inputs['password'] ?? '');

if (empty($identifiers) || empty($passwords)) {
    api_helpers::responses_error('Identitas pengguna (Email/Username/NIM) dan kata sandi wajib diisi.', 422);
}

try {
    $user_models = new users();
    $users = $user_models->find_by_identifiers($identifiers);

    if (!$users) {
        api_helpers::responses_error('Identitas pengguna atau kata sandi tidak sesuai.', 401);
    }

    if (!password_verify($passwords, $users['password'])) {
        api_helpers::responses_error('Identitas pengguna atau kata sandi tidak sesuai.', 401);
    }

    if (isset($users['is_active']) && (int)$users['is_active'] === 0) {
        api_helpers::responses_error('Akun Anda dinonaktifkan oleh Administrator.', 403);
    }

    // Buat token autentikasi mobile HMAC-SHA256
    $tokens = api_helpers::generate_tokens($users);

    // Siapkan data pengguna tanpa informasi hash sandi sensitif
    $user_profiles = [
        'id'           => (int)$users['id'],
        'name'         => $users['name'],
        'email'        => $users['email'],
        'username'     => $users['username'],
        'role'         => $users['role'],
        'nim_nip'      => $users['nim_nip'] ?? '',
        'prodi_id'     => (int)($users['prodi_id'] ?? 0),
        'created_at'   => $users['created_at'] ?? ''
    ];

    api_helpers::responses_success([
        'token' => $tokens,
        'user'  => $user_profiles
    ], 'Autentikasi berhasil. Selamat datang di Lentera.');

} catch (Exception $exceptions) {
    error_log("API Login Error: " . $exceptions->getMessage());
    api_helpers::responses_error('Terjadi gangguan saat memproses autentikasi.', 500);
}
