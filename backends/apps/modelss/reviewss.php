<?php
/**
 * reviewss.php
 * model pencatatan hasil review kurasi karya ilmiah
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class reviewss {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function creates_reviews($work_ids, $curator_ids, $actions, $comments) {
        $querys = "
            INSERT INTO reviews (work_id, curator_id, action, comment, created_at)
            VALUES (:work_ids, :curator_ids, :actions, :comments, CURRENT_TIMESTAMP)
        ";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([
            ':work_ids'    => $work_ids,
            ':curator_ids' => $curator_ids,
            ':actions'     => $actions,
            ':comments'    => $comments,
        ]);
    }

    public function get_reviews_by_works($work_ids) {
        $querys = "
            SELECT r.*, u.name AS curator_name
            FROM reviews r
            JOIN users u ON r.curator_id = u.id
            WHERE r.work_id = :work_ids
            ORDER BY r.created_at DESC
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':work_ids' => $work_ids]);
        return $statements->fetchAll();
    }
}
