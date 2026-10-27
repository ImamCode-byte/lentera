<?php
/**
 * flashs.php
 * pengelola pesan kilat (flash messages) antar request
 * seluruh fungsi dan variabel konsisten lowercase dengan akhiran s
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'sessions.php';

class flashs {
    public static function sets($types, $messages) {
        sessions::starts();
        $_SESSION['flash_messagess'][$types] = $messages;
    }

    public static function sets_success($messages) {
        self::sets('success', $messages);
    }

    public static function sets_error($messages) {
        self::sets('error', $messages);
    }

    public static function sets_warning($messages) {
        self::sets('warning', $messages);
    }

    public static function gets($types) {
        sessions::starts();
        if (isset($_SESSION['flash_messagess'][$types])) {
            $messages = $_SESSION['flash_messagess'][$types];
            unset($_SESSION['flash_messagess'][$types]);
            return $messages;
        }
        return null;
    }

    public static function hass($types) {
        sessions::starts();
        return isset($_SESSION['flash_messagess'][$types]);
    }
}
