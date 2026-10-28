<?php
/**
 * auth_controllers.php
 * pengontrol alur autentikasi pengguna (login, register, logout)
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__) . '/modelss/users.php';
require_once dirname(__DIR__) . '/helperss/sessions.php';
require_once dirname(__DIR__) . '/helperss/csrfs.php';
require_once dirname(__DIR__) . '/helperss/validators.php';
require_once dirname(__DIR__) . '/helperss/flashs.php';

class auth_controllers {
    private $user_models;

    public function __construct() {
        $this->user_models = new users();
    }

    public function process_logins($post_datas) {
        sessions::starts();

        // 1. verifikasi token csrfs
        $tokens = $post_datas['csrf_tokens'] ?? '';
        if (!csrfs::validates($tokens)) {
            flashs::sets_error('Sesi keamanan telah kedaluwarsa. Silakan muat ulang formulir.');
            return false;
        }

        // 2. validasi input
        $identifiers = trim($post_datas['identifiers'] ?? $post_datas['emails'] ?? '');
        $passwords = $post_datas['passwords'] ?? '';

        if (empty($identifiers) || empty($passwords)) {
            flashs::sets_error('Email/Username/NIM dan kata sandi wajib diisi.');
            return false;
        }

        // 3. cari data pengguna di basis data (bisa via email, username, atau NIM/NIP)
        $users = $this->user_models->find_by_identifiers($identifiers);

        if (!$users) {
            flashs::sets_error('Kombinasi identitas pengguna dan kata sandi tidak ditemukan.');
            return false;
        }

        if (empty($users['is_active'])) {
            flashs::sets_error('Akun Anda sedang dinonaktifkan. Silakan hubungi kurator atau administrator repositori.');
            return false;
        }

        // 4. verifikasi kata sandi dengan bcrypt
        if (!$this->user_models->verifys_passwords($passwords, $users['password'])) {
            flashs::sets_error('Kombinasi identitas pengguna dan kata sandi tidak ditemukan.');
            return false;
        }

        // 5. login berhasil: amankan sesi terhadap session fixation
        sessions::regenerates();
        sessions::sets('user_ids', $users['id']);
        sessions::sets('user_names', $users['name']);
        sessions::sets('user_usernames', $users['username'] ?? '');
        sessions::sets('user_emails', $users['email']);
        sessions::sets('user_roles', $users['role']);
        sessions::sets('user_prodi_ids', $users['prodi_id']);

        $this->user_models->updates_last_logins($users['id']);
        flashs::sets_success("Selamat datang kembali, {$users['name']}!");

        return $users;
    }

    public function process_registers($post_datas) {
        sessions::starts();

        // 1. verifikasi token csrfs
        $tokens = $post_datas['csrf_tokens'] ?? '';
        if (!csrfs::validates($tokens)) {
            flashs::sets_error('Sesi keamanan kedaluwarsa. Silakan ulangi pendaftaran.');
            return false;
        }

        // 2. validasi kelengkapan data wajib
        $validators = new validators();
        $is_valids = $validators->validates($post_datas, [
            'names'     => 'required|min:3|max:100',
            'emails'    => 'required|email|max:150',
            'nim_nips'  => 'required|min:5|max:30',
            'passwords' => 'required|min:8',
        ]);

        if (!$is_valids) {
            $error_itemss = $validators->get_errorss();
            $first_errors = reset($error_itemss)[0] ?? 'Data pendaftaran belum valid.';
            flashs::sets_error($first_errors);
            return false;
        }

        // validasi username
        $usernames = trim($post_datas['usernames'] ?? '');
        if (!empty($usernames) && !preg_match('/^[a-zA-Z0-9_.-]{3,60}$/', $usernames)) {
            flashs::sets_error('Username hanya boleh huruf, angka, underscore, titik, atau strip (3-60 karakter).');
            return false;
        }

        // periksa kesesuaian konfirmasi sandi
        if (($post_datas['passwords'] ?? '') !== ($post_datas['password_confirms'] ?? '')) {
            flashs::sets_error('Konfirmasi kata sandi tidak cocok.');
            return false;
        }

        // validasi persetujuan etika & privasi
        if (empty($post_datas['consent_privacys'])) {
            flashs::sets_error('Anda wajib menyetujui Pernyataan Kebijakan Privasi & Penyimpanan Data.');
            return false;
        }
        if (empty($post_datas['consent_integritys'])) {
            flashs::sets_error('Anda wajib menyetujui Pakta Integritas Akademik & Bebas Plagiasi.');
            return false;
        }

        $emails = trim($post_datas['emails']);
        $nim_nips = trim($post_datas['nim_nips']);

        // 3. pastikan email belum pernah terdaftar
        $existing_emails = $this->user_models->find_by_emails($emails);
        if ($existing_emails) {
            flashs::sets_error('Alamat email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.');
            return false;
        }

        // pastikan username belum pernah terdaftar
        if (!empty($usernames)) {
            $existing_users = $this->user_models->find_by_usernames($usernames);
            if ($existing_users) {
                flashs::sets_error('Nama pengguna (username) sudah digunakan. Silakan pilih username lain.');
                return false;
            }
        }

        // pastikan nim/nip belum terdaftar
        $existing_nim = $this->user_models->find_by_nim_nips($nim_nips);
        if ($existing_nim) {
            flashs::sets_error('Nomor Induk (NIM / NIP) ini sudah terdaftar di sistem.');
            return false;
        }

        // 4. simpan pengguna baru lengkap dengan seluruh atribut ilmiah
        $new_user_ids = $this->user_models->creates_users([
            'names'                  => trim($post_datas['names']),
            'usernames'              => !empty($usernames) ? $usernames : null,
            'emails'                 => $emails,
            'passwords'              => $post_datas['passwords'],
            'roles'                  => 'author',
            'nim_nips'               => $nim_nips,
            'prodi_ids'              => !empty($post_datas['prodi_ids']) ? (int)$post_datas['prodi_ids'] : null,
            'affiliations'           => !empty($post_datas['affiliations']) ? trim($post_datas['affiliations']) : 'Universitas Madura',
            'countries'              => !empty($post_datas['countries']) ? trim($post_datas['countries']) : 'Indonesia',
            'phones'                 => !empty($post_datas['phones']) ? trim($post_datas['phones']) : null,
            'orcid_ids'              => !empty($post_datas['orcid_ids']) ? trim($post_datas['orcid_ids']) : null,
            'bios'                   => !empty($post_datas['bios']) ? trim($post_datas['bios']) : null,
            'is_reviewer_candidates' => !empty($post_datas['is_reviewer_candidates']) ? 1 : 0,
        ]);

        if ($new_user_ids) {
            flashs::sets_success('Pendaftaran akun berhasil! Silakan masuk menggunakan Email/Username dan Kata Sandi Anda.');
            return true;
        }

        flashs::sets_error('Terjadi kendala saat mendaftar. Silakan coba lagi beberapa saat.');
        return false;
    }

    public static function logouts() {
        sessions::destroys();
    }
}