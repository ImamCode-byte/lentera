<?php
/**
 * api_helpers.php
 * Helper utilitas respons JSON terstandar dan verifikasi autentikasi token HMAC-SHA256 untuk Mobile API Lentera
 */

// Aktifkan header CORS dan JSON untuk seluruh endpoint API jika belum terkirim
if (!headers_sent()) {
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
}

// Tangani preflight OPTIONS request
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

class api_helpers {

    private static $token_secrets = 'lentera_mobile_secret_token_key_unira_2026';

    /**
     * Mengirimkan respons sukses JSON terstandar
     */
    public static function responses_success($datas = [], $messages = 'Berhasil memproses permintaan.', $status_codes = 200) {
        http_response_code($status_codes);
        echo json_encode([
            'success' => true,
            'message' => $messages,
            'data'    => $datas
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Mengirimkan respons galat JSON terstandar
     */
    public static function responses_error($messages = 'Terjadi kesalahan pada sistem.', $status_codes = 400, $errors_details = null) {
        http_response_code($status_codes);
        $responses = [
            'success' => false,
            'message' => $messages
        ];
        if ($errors_details !== null) {
            $responses['errors'] = $errors_details;
        }
        echo json_encode($responses, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Mengambil JSON input payload dari HTTP body
     */
    public static function get_json_inputs() {
        $raw_inputs = file_get_contents('php://input');
        if (empty($raw_inputs)) {
            return [];
        }
        $decoded = json_decode($raw_inputs, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Membuat token sesi HMAC-SHA256 untuk client mobile
     */
    public static function generate_tokens($user_datas, $expires_in_days = 7) {
        $payloads = [
            'user_ids'    => $user_datas['id'] ?? $user_datas['user_ids'],
            'user_names'  => $user_datas['name'] ?? $user_datas['user_names'],
            'user_emails' => $user_datas['email'] ?? $user_datas['user_emails'],
            'user_roles'  => $user_datas['role'] ?? $user_datas['user_roles'],
            'nim'         => $user_datas['nim'] ?? '',
            'iat'         => time(),
            'exp'         => time() + ($expires_in_days * 86400)
        ];

        $encoded_payloads = rtrim(strtr(base64_encode(json_encode($payloads)), '+/', '-_'), '=');
        $signatures = hash_hmac('sha256', $encoded_payloads, self::$token_secrets);

        return $encoded_payloads . '.' . $signatures;
    }

    /**
     * Memvalidasi token Authorization Bearer dari header HTTP
     */
    public static function authenticates_tokens() {
        $auth_headers = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth_headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth_headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $auth_headers = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (empty($auth_headers) || !preg_match('/Bearer\s+(.*)$/i', $auth_headers, $matches)) {
            self::responses_error('Akses ditolak: Token autentikasi tidak ditemukan dalam permintaan.', 401);
        }

        $tokens = trim($matches[1]);
        $parts = explode('.', $tokens);
        if (count($parts) !== 2) {
            self::responses_error('Akses ditolak: Format token tidak valid.', 401);
        }

        list($encoded_payloads, $signatures) = $parts;
        $expected_signatures = hash_hmac('sha256', $encoded_payloads, self::$token_secrets);

        if (!hash_equals($expected_signatures, $signatures)) {
            self::responses_error('Akses ditolak: Tanda tangan token tidak sah.', 401);
        }

        $json_payloads = base64_decode(strtr($encoded_payloads, '-_', '+/'));
        $payloads = json_decode($json_payloads, true);

        if (!$payloads || empty($payloads['user_ids']) || empty($payloads['exp'])) {
            self::responses_error('Akses ditolak: Muatan token rusak.', 401);
        }

        if ($payloads['exp'] < time()) {
            self::responses_error('Akses ditolak: Token autentikasi telah kedaluwarsa. Silakan masuk kembali.', 401);
        }

        return $payloads;
    }
}
