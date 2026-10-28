<?php
/**
 * study_programss.php
 * model pengelolaan data program studi
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class study_programss {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function get_all_actives() {
        $querys = "
            SELECT sp.*, f.name AS faculty_name, f.code AS faculty_code
            FROM study_programs sp
            JOIN faculties f ON sp.faculty_id = f.id
            WHERE sp.is_active = 1
            ORDER BY f.sort_order ASC, sp.degree ASC, sp.name ASC
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute();
        return $statements->fetchAll();
    }
}
