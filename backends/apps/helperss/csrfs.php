<?php
/**
 * csrfs.php
 * proteksi cross-site request forgery (csrf) untuk lenteras
 * seluruh fungsi dan variabel konsisten lowercase dengan akhiran s
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'sessions.php';

class csrfs {
    public static function generates() {
        sessions::starts();
        $tokens = bin2hex(random_bytes(32));
        sessions::sets('csrf_tokens', $tokens);
        return $tokens;
    }

    public static function gets() {
        sessions::starts();
        if (!sessions::hass('csrf_tokens')) {
            return self::generates();
        }
        return sessions::gets('csrf_tokens');
    }

    public static function inputs() {
        $tokens = self::gets();
        return '<input type="hidden" name="csrf_tokens" value="' . htmlspecialchars($tokens, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validates($tokens) {
        sessions::starts();
        $stored_tokens = sessions::gets('csrf_tokens');
        if (empty($stored_tokens) || empty($tokens)) {
            return false;
        }
        return hash_equals($stored_tokens, $tokens);
    }
}
