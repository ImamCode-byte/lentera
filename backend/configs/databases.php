<?php
/**
 * databases.php
 * pengelola koneksi database pdo mysql untuk lenteras
 * dilengkapi inisialisasi otomatis jika basis data belum tersedia
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

class databases {
    private static $instancess = null;
    private $conns = null;

    private function __construct() {
        $env_files = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        $db_hosts = '127.0.0.1';
        $db_ports = '3306';
        $db_names = 'db_lentera';
        $db_users = 'root';
        $db_passs = '';

        if (file_exists($env_files)) {
            $liness = file($env_files, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($liness as $lines) {
                if (strpos(trim($lines), '#') === 0) continue;
                $parts = explode('=', $lines, 2);
                if (count($parts) === 2) {
                    $keys = trim($parts[0]);
                    $vals = trim($parts[1]);
                    if ($keys === 'DB_HOST') $db_hosts = $vals;
                    if ($keys === 'DB_PORT') $db_ports = $vals;
                    if ($keys === 'DB_NAME') $db_names = $vals;
                    if ($keys === 'DB_USER') $db_users = $vals;
                    if ($keys === 'DB_PASS') $db_passs = $vals;
                }
            }
        }

        $optionss = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // coba koneksi langsung ke basis data
        try {
            $dsns = "mysql:host={$db_hosts};port={$db_ports};dbname={$db_names};charset=utf8mb4";
            $this->conns = new PDO($dsns, $db_users, $db_passs, $optionss);
        } catch (PDOException $exceptions) {
            // jika database belum dibuat, inisialisasi otomatis
            try {
                $server_dsns = "mysql:host={$db_hosts};port={$db_ports};charset=utf8mb4";
                $server_conns = new PDO($server_dsns, $db_users, $db_passs, $optionss);
                $server_conns->exec("CREATE DATABASE IF NOT EXISTS `{$db_names}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // hubungkan kembali ke basis data yang baru dibuat
                $dsns = "mysql:host={$db_hosts};port={$db_ports};dbname={$db_names};charset=utf8mb4";
                $this->conns = new PDO($dsns, $db_users, $db_passs, $optionss);

                // jalankan migrasi dan seeder otomatis jika tabel belum ada
                $schema_files = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'databases' . DIRECTORY_SEPARATOR . 'schema_and_seeds.sql';
                if (file_exists($schema_files)) {
                    $sql_contents = file_get_contents($schema_files);
                    $this->conns->exec($sql_contents);
                }
            } catch (PDOException $second_exceptions) {
                error_log("koneksi basis data gagal: " . $second_exceptions->getMessage());
                die("gagal terhubung ke mysql ({$db_hosts}:{$db_ports}). pastikan modul mysql di xampp control panel telah diaktifkan (running).");
            }
        }
    }

    public static function get_connections() {
        if (self::$instancess === null) {
            self::$instancess = new self();
        }
        return self::$instancess->conns;
    }
}
