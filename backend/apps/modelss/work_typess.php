<?php
/**
 * work_typess.php
 * model tipe karya ilmiah (skripsi, tesis, jurnal, dll)
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class work_typess {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function get_all_actives() {
        $querys = "SELECT * FROM work_types WHERE is_active = 1 ORDER BY id ASC";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute();
        return $statements->fetchAll();
    }

    public function find_by_ids($ids) {
        $querys = "SELECT * FROM work_types WHERE id = :ids LIMIT 1";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':ids' => $ids]);
        return $statements->fetch();
    }
}
