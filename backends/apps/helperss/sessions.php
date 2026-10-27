<?php
/**
 * sessions.php
 * pengelola sesi pengguna yang aman untuk lenteras
 * seluruh fungsi dan variabel konsisten lowercase dengan akhiran s
 */

class sessions {
    public static function starts() {
        if (session_status() === PHP_SESSION_NONE) {
            $cookiess = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => 7200,
                'path'     => '/',
                'domain'   => $cookiess['domain'],
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public static function sets($keys, $values) {
        self::starts();
        $_SESSION[$keys] = $values;
    }

    public static function gets($keys, $defaults = null) {
        self::starts();
        return $_SESSION[$keys] ?? $defaults;
    }

    public static function hass($keys) {
        self::starts();
        return isset($_SESSION[$keys]);
    }

    public static function removes($keys) {
        self::starts();
        if (isset($_SESSION[$keys])) {
            unset($_SESSION[$keys]);
        }
    }

    public static function destroys() {
        self::starts();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function regenerates() {
        self::starts();
        session_regenerate_id(true);
    }
}
