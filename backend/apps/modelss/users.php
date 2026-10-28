<?php
/**
 * users.php
 * model pengelolaan data pengguna (users)
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class users {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function find_by_emails($emails) {
        $querys = "SELECT * FROM users WHERE email = :emails LIMIT 1";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':emails' => $emails]);
        return $statements->fetch();
    }

    public function find_by_usernames($usernames) {
        $querys = "SELECT * FROM users WHERE username = :usernames LIMIT 1";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':usernames' => $usernames]);
        return $statements->fetch();
    }

    public function find_by_nim_nips($nim_nips) {
        $querys = "SELECT * FROM users WHERE nim_nip = :nim_nips LIMIT 1";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':nim_nips' => $nim_nips]);
        return $statements->fetch();
    }

    public function find_by_identifiers($identifiers) {
        $querys = "
            SELECT * FROM users 
            WHERE email = :id1 
               OR username = :id2 
               OR nim_nip = :id3 
            LIMIT 1
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([
            ':id1' => $identifiers,
            ':id2' => $identifiers,
            ':id3' => $identifiers,
        ]);
        return $statements->fetch();
    }

    public function find_by_ids($ids) {
        $querys = "
            SELECT u.*, sp.name AS prodi_name, f.name AS faculty_name, f.code AS faculty_code
            FROM users u
            LEFT JOIN study_programs sp ON u.prodi_id = sp.id
            LEFT JOIN faculties f ON sp.faculty_id = f.id
            WHERE u.id = :ids LIMIT 1
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':ids' => $ids]);
        return $statements->fetch();
    }

    public function creates_users($datas) {
        $querys = "
            INSERT INTO users (
                name, username, email, password, role, nim_nip, prodi_id, 
                affiliation, country, phone, orcid_id, bio, is_reviewer_candidate, is_active
            )
            VALUES (
                :names, :usernames, :emails, :passwords, :roles, :nim_nips, :prodi_ids, 
                :affiliations, :countries, :phones, :orcid_ids, :bios, :is_reviewer_candidates, 1
            )
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([
            ':names'                  => $datas['names'],
            ':usernames'              => !empty($datas['usernames']) ? $datas['usernames'] : null,
            ':emails'                 => $datas['emails'],
            ':passwords'              => password_hash($datas['passwords'], PASSWORD_BCRYPT),
            ':roles'                  => $datas['roles'] ?? 'author',
            ':nim_nips'               => !empty($datas['nim_nips']) ? $datas['nim_nips'] : null,
            ':prodi_ids'              => !empty($datas['prodi_ids']) ? (int)$datas['prodi_ids'] : null,
            ':affiliations'           => !empty($datas['affiliations']) ? $datas['affiliations'] : 'Universitas Madura',
            ':countries'              => !empty($datas['countries']) ? $datas['countries'] : 'Indonesia',
            ':phones'                 => !empty($datas['phones']) ? $datas['phones'] : null,
            ':orcid_ids'              => !empty($datas['orcid_ids']) ? $datas['orcid_ids'] : null,
            ':bios'                   => !empty($datas['bios']) ? $datas['bios'] : null,
            ':is_reviewer_candidates' => !empty($datas['is_reviewer_candidates']) ? 1 : 0,
        ]);
        return $this->db_conns->lastInsertId();
    }

    public function updates_last_logins($ids) {
        $querys = "UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :ids";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([':ids' => $ids]);
    }

    public function verifys_passwords($input_passwords, $hash_passwords) {
        return password_verify($input_passwords, $hash_passwords);
    }

    public function get_all_users_for_admin() {
        $querys = "
            SELECT u.*, sp.name AS prodi_name, f.name AS faculty_name,
                   (SELECT COUNT(*) FROM works WHERE author_id = u.id) AS total_works
            FROM users u
            LEFT JOIN study_programs sp ON u.prodi_id = sp.id
            LEFT JOIN faculties f ON sp.faculty_id = f.id
            ORDER BY u.id DESC
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute();
        return $statements->fetchAll();
    }

    public function update_user_roles($user_ids, $new_roles) {
        if (!in_array($new_roles, ['author', 'curator', 'admin'])) return false;
        $querys = "UPDATE users SET role = :new_roles WHERE id = :user_ids";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([
            ':new_roles' => $new_roles,
            ':user_ids'  => $user_ids
        ]);
    }

    public function toggle_user_actives($user_ids) {
        $querys = "UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = :user_ids";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([':user_ids' => $user_ids]);
    }

    public function get_admin_user_stats() {
        $querys = "
            SELECT 
                COUNT(*) AS total_users,
                SUM(CASE WHEN role = 'author' THEN 1 ELSE 0 END) AS total_authors,
                SUM(CASE WHEN role = 'curator' THEN 1 ELSE 0 END) AS total_curators,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS total_admins,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS total_actives
            FROM users
        ";
        $statements = $this->db_conns->query($querys);
        return $statements->fetch(PDO::FETCH_ASSOC);
    }
}