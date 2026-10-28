<?php
/**
 * facultiess.php
 * model pengelolaan data fakultas universitas madura
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class facultiess {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function get_all_with_counts() {
        $querys = "
            SELECT f.*, 
                   COUNT(DISTINCT sp.id) AS total_prodis,
                   COUNT(DISTINCT w.id) AS total_published_works
            FROM faculties f
            LEFT JOIN study_programs sp ON f.id = sp.faculty_id AND sp.is_active = 1
            LEFT JOIN works w ON sp.id = w.prodi_id AND w.status = 'published'
            GROUP BY f.id, f.code, f.name, f.sort_order, f.is_active, f.created_at, f.updated_at
            ORDER BY f.sort_order ASC
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute();
        return $statements->fetchAll();
    }
}