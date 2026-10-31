<?php
/**
 * workss.php
 * model utama pengelolaan karya ilmiah, kata kunci, dan berkas multi-versi
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 2) . '/configs/databases.php';

class workss {
    private $db_conns;

    public function __construct() {
        $this->db_conns = databases::get_connections();
    }

    public function creates_works($work_datas, $keyword_arrays, $file_datas) {
        try {
            $this->db_conns->beginTransaction();

            // 1. insert data karya ilmiah
            $query_works = "
                INSERT INTO works (title, abstract, author_id, prodi_id, work_type_id, status, year, supervisor, submitted_at)
                VALUES (:titles, :abstracts, :author_ids, :prodi_ids, :work_type_ids, :statuss, :years, :supervisors, CURRENT_TIMESTAMP)
            ";
            $stmt_works = $this->db_conns->prepare($query_works);
            $stmt_works->execute([
                ':titles'        => $work_datas['titles'],
                ':abstracts'     => $work_datas['abstracts'],
                ':author_ids'    => $work_datas['author_ids'],
                ':prodi_ids'     => $work_datas['prodi_ids'],
                ':work_type_ids' => $work_datas['work_type_ids'],
                ':statuss'       => $work_datas['statuss'] ?? 'submitted',
                ':years'         => $work_datas['years'],
                ':supervisors'   => $work_datas['supervisors'] ?? null,
            ]);
            $work_ids = $this->db_conns->lastInsertId();

            // 2. insert kata kunci
            if (!empty($keyword_arrays)) {
                $query_keywords = "INSERT INTO work_keywords (work_id, keyword) VALUES (:work_ids, :keywords)";
                $stmt_keywords = $this->db_conns->prepare($query_keywords);
                foreach ($keyword_arrays as $keywords) {
                    $trimmed_keywords = trim($keywords);
                    if ($trimmed_keywords !== '') {
                        $stmt_keywords->execute([
                            ':work_ids' => $work_ids,
                            ':keywords' => $trimmed_keywords
                        ]);
                    }
                }
            }

            // 3. insert file pdf versi 1
            if (!empty($file_datas)) {
                $query_files = "
                    INSERT INTO work_files (work_id, original_name, stored_name, file_path, file_size, version, is_current)
                    VALUES (:work_ids, :original_names, :stored_names, :file_paths, :file_sizess, 1, 1)
                ";
                $stmt_files = $this->db_conns->prepare($query_files);
                $stmt_files->execute([
                    ':work_ids'       => $work_ids,
                    ':original_names' => $file_datas['original_names'],
                    ':stored_names'   => $file_datas['stored_names'],
                    ':file_paths'     => $file_datas['file_paths'],
                    ':file_sizess'    => $file_datas['file_sizess'],
                ]);
            }

            $this->db_conns->commit();
            return $work_ids;
        } catch (Exception $exceptions) {
            $this->db_conns->rollBack();
            error_log("gagal menyimpan karya ilmiah: " . $exceptions->getMessage());
            return false;
        }
    }

    public function find_by_ids($ids) {
        $querys = "
            SELECT w.*, u.name AS author_name, u.email AS author_email, u.nim_nip AS author_nim_nip,
                   sp.name AS prodi_name, sp.degree AS prodi_degree,
                   f.name AS faculty_name, f.code AS faculty_code,
                   wt.name AS type_name, wt.code AS type_code,
                   wf.file_path, wf.original_name, wf.file_size, wf.version AS file_version
            FROM works w
            JOIN users u ON w.author_id = u.id
            JOIN study_programs sp ON w.prodi_id = sp.id
            JOIN faculties f ON sp.faculty_id = f.id
            JOIN work_types wt ON w.work_type_id = wt.id
            LEFT JOIN work_files wf ON w.id = wf.work_id AND wf.is_current = 1
            WHERE w.id = :ids LIMIT 1
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':ids' => $ids]);
        $works = $statements->fetch();

        if ($works) {
            // ambil kata kunci
            $query_keywords = "SELECT keyword FROM work_keywords WHERE work_id = :ids";
            $stmt_keywords = $this->db_conns->prepare($query_keywords);
            $stmt_keywords->execute([':ids' => $ids]);
            $works['keywordss'] = $stmt_keywords->fetchAll(PDO::FETCH_COLUMN);

            // ambil riwayat file multi-versi
            $query_all_files = "SELECT * FROM work_files WHERE work_id = :ids ORDER BY version DESC";
            $stmt_all_files = $this->db_conns->prepare($query_all_files);
            $stmt_all_files->execute([':ids' => $ids]);
            $works['all_filess'] = $stmt_all_files->fetchAll();

            // ambil catatan kurasi
            $query_reviews = "
                SELECT r.*, u.name AS curator_name
                FROM reviews r
                JOIN users u ON r.curator_id = u.id
                WHERE r.work_id = :ids
                ORDER BY r.created_at DESC
            ";
            $stmt_reviews = $this->db_conns->prepare($query_reviews);
            $stmt_reviews->execute([':ids' => $ids]);
            $works['reviewss'] = $stmt_reviews->fetchAll();
        }

        return $works;
    }

    public function get_by_authors($author_ids) {
        $querys = "
            SELECT w.*, sp.name AS prodi_name, wt.name AS type_name,
                   wf.original_name, wf.version AS file_version,
                   (SELECT comment FROM reviews WHERE work_id = w.id ORDER BY id DESC LIMIT 1) AS last_review_comments,
                   (SELECT action FROM reviews WHERE work_id = w.id ORDER BY id DESC LIMIT 1) AS last_review_actions
            FROM works w
            JOIN study_programs sp ON w.prodi_id = sp.id
            JOIN work_types wt ON w.work_type_id = wt.id
            LEFT JOIN work_files wf ON w.id = wf.work_id AND wf.is_current = 1
            WHERE w.author_id = :author_ids
            ORDER BY w.created_at DESC
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->execute([':author_ids' => $author_ids]);
        return $statements->fetchAll();
    }

    public function get_curator_metric_counts() {
        $querys = "
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS count_submitted,
                SUM(CASE WHEN status = 'in_review' THEN 1 ELSE 0 END) AS count_in_review,
                SUM(CASE WHEN status = 'revision' THEN 1 ELSE 0 END) AS count_revision,
                SUM(CASE WHEN status = 'waiting_payment' THEN 1 ELSE 0 END) AS count_waiting_payment,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS count_published,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS count_rejected
            FROM works
        ";
        $statements = $this->db_conns->query($querys);
        $res = $statements->fetch(PDO::FETCH_ASSOC);
        return [
            'total'           => (int)($res['total'] ?? 0),
            'submitted'       => (int)($res['count_submitted'] ?? 0),
            'in_review'       => (int)($res['count_in_review'] ?? 0),
            'revision'        => (int)($res['count_revision'] ?? 0),
            'waiting_payment' => (int)($res['count_waiting_payment'] ?? 0),
            'published'       => (int)($res['count_published'] ?? 0),
            'rejected'        => (int)($res['count_rejected'] ?? 0),
        ];
    }

    public function get_for_curators($status_filters = null, $keyword_filters = null, $prodi_filters = null) {
        $querys = "
            SELECT w.*, u.name AS author_name, u.nim_nip AS author_nim_nip, u.email AS author_email,
                   sp.name AS prodi_name, f.name AS faculty_name, f.code AS faculty_code,
                   wt.name AS type_name, wt.code AS type_code,
                   wf.original_name, wf.file_path, wf.version AS file_version, wf.file_size
            FROM works w
            JOIN users u ON w.author_id = u.id
            JOIN study_programs sp ON w.prodi_id = sp.id
            JOIN faculties f ON sp.faculty_id = f.id
            JOIN work_types wt ON w.work_type_id = wt.id
            LEFT JOIN work_files wf ON w.id = wf.work_id AND wf.is_current = 1
            WHERE 1=1
        ";
        $params = [];

        if ($status_filters) {
            $querys .= " AND w.status = :status_filters";
            $params[':status_filters'] = $status_filters;
        }

        if (!empty($keyword_filters)) {
            $querys .= " AND (w.title LIKE :kw1 OR u.name LIKE :kw2 OR u.nim_nip LIKE :kw3)";
            $params[':kw1'] = '%' . $keyword_filters . '%';
            $params[':kw2'] = '%' . $keyword_filters . '%';
            $params[':kw3'] = '%' . $keyword_filters . '%';
        }

        if (!empty($prodi_filters)) {
            $querys .= " AND sp.id = :prodi_id";
            $params[':prodi_id'] = (int)$prodi_filters;
        }

        $querys .= " ORDER BY CASE WHEN w.status = 'submitted' THEN 1 WHEN w.status = 'in_review' THEN 2 WHEN w.status = 'revision' THEN 3 ELSE 4 END, w.created_at DESC";

        $statements = $this->db_conns->prepare($querys);
        $statements->execute($params);
        return $statements->fetchAll();
    }
    public function updates_statuss($work_ids, $statuss, $published_at = null) {
        $querys = "UPDATE works SET status = :statuss";
        $params = [':work_ids' => $work_ids, ':statuss' => $statuss];

        if ($statuss === 'published') {
            $querys .= ", published_at = CURRENT_TIMESTAMP";
        }
        $querys .= " WHERE id = :work_ids";

        $statements = $this->db_conns->prepare($querys);
        return $statements->execute($params);
    }

    public function adds_revisions($work_ids, $file_datas, $revision_notes = null) {
        try {
            $this->db_conns->beginTransaction();

            // hitung versi terakhir
            $query_versions = "SELECT MAX(version) AS max_v FROM work_files WHERE work_id = :work_ids";
            $stmt_v = $this->db_conns->prepare($query_versions);
            $stmt_v->execute([':work_ids' => $work_ids]);
            $current_versions = (int)($stmt_v->fetch()['max_v'] ?? 1);
            $next_versions = $current_versions + 1;

            // matikan status is_current file sebelumnya
            $query_resets = "UPDATE work_files SET is_current = 0 WHERE work_id = :work_ids";
            $stmt_resets = $this->db_conns->prepare($query_resets);
            $stmt_resets->execute([':work_ids' => $work_ids]);

            // insert file versi baru
            $query_files = "
                INSERT INTO work_files (work_id, original_name, stored_name, file_path, file_size, version, is_current)
                VALUES (:work_ids, :original_names, :stored_names, :file_paths, :file_sizess, :next_versions, 1)
            ";
            $stmt_files = $this->db_conns->prepare($query_files);
            $stmt_files->execute([
                ':work_ids'       => $work_ids,
                ':original_names' => $file_datas['original_names'],
                ':stored_names'   => $file_datas['stored_names'],
                ':file_paths'     => $file_datas['file_paths'],
                ':file_sizess'    => $file_datas['file_sizess'],
                ':next_versions'  => $next_versions,
            ]);

            // ubah status kembali ke submitted agar kurator memeriksa kembali
            $query_works = "UPDATE works SET status = 'submitted', updated_at = CURRENT_TIMESTAMP WHERE id = :work_ids";
            $stmt_works = $this->db_conns->prepare($query_works);
            $stmt_works->execute([':work_ids' => $work_ids]);

            $this->db_conns->commit();
            return true;
        } catch (Exception $exceptions) {
            $this->db_conns->rollBack();
            error_log("gagal menambah revisi: " . $exceptions->getMessage());
            return false;
        }
    }

    public function search_public_works($query_texts = null, $type_filters = null, $faculty_filters = null, $prodi_filters = null, $year_filters = null) {
        $querys = "
            SELECT w.*, u.name AS author_name, sp.name AS prodi_name, sp.code AS prodi_code,
                   f.name AS faculty_name, f.code AS faculty_code,
                   wt.name AS type_name, wt.code AS type_code,
                   wf.file_path, wf.original_name, wf.file_size
            FROM works w
            JOIN users u ON w.author_id = u.id
            JOIN study_programs sp ON w.prodi_id = sp.id
            JOIN faculties f ON sp.faculty_id = f.id
            JOIN work_types wt ON w.work_type_id = wt.id
            LEFT JOIN work_files wf ON w.id = wf.work_id AND wf.is_current = 1
            WHERE w.status = 'published'
        ";
        $params = [];

        if (!empty($query_texts)) {
            $querys .= " AND (w.title LIKE :query1 OR w.abstract LIKE :query2 OR u.name LIKE :query3 OR w.id IN (SELECT work_id FROM work_keywords WHERE keyword LIKE :query4))";
            $params[':query1'] = '%' . $query_texts . '%';
            $params[':query2'] = '%' . $query_texts . '%';
            $params[':query3'] = '%' . $query_texts . '%';
            $params[':query4'] = '%' . $query_texts . '%';
        }

        if (!empty($type_filters)) {
            $querys .= " AND wt.code = :type_filters";
            $params[':type_filters'] = $type_filters;
        }

        if (!empty($faculty_filters)) {
            $querys .= " AND f.code = :faculty_filters";
            $params[':faculty_filters'] = $faculty_filters;
        }

        if (!empty($prodi_filters)) {
            $querys .= " AND sp.id = :prodi_filters";
            $params[':prodi_filters'] = $prodi_filters;
        }

        if (!empty($year_filters)) {
            $querys .= " AND w.year = :year_filters";
            $params[':year_filters'] = $year_filters;
        }

        $querys .= " ORDER BY w.published_at DESC";

        $statements = $this->db_conns->prepare($querys);
        $statements->execute($params);
        return $statements->fetchAll();
    }

    public function increment_views($work_ids) {
        $querys = "UPDATE works SET view_count = view_count + 1 WHERE id = :work_ids";
        $statements = $this->db_conns->prepare($querys);
        return $statements->execute([':work_ids' => $work_ids]);
    }

    public function record_downloads($work_ids, $user_ids = null, $ip_addresss = null, $user_agents = null) {
        try {
            $this->db_conns->beginTransaction();
            $query_counter = "UPDATE works SET download_count = download_count + 1 WHERE id = :work_ids";
            $stmt_counter = $this->db_conns->prepare($query_counter);
            $stmt_counter->execute([':work_ids' => $work_ids]);

            $query_logs = "
                INSERT INTO download_logs (work_id, user_id, ip_address, user_agent, downloaded_at)
                VALUES (:work_ids, :user_ids, :ip_addresss, :user_agents, CURRENT_TIMESTAMP)
            ";
            $stmt_logs = $this->db_conns->prepare($query_logs);
            $stmt_logs->execute([
                ':work_ids'     => $work_ids,
                ':user_ids'     => $user_ids,
                ':ip_addresss'  => $ip_addresss ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'),
                ':user_agents'  => $user_agents ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'browser')
            ]);
            $this->db_conns->commit();
            return true;
        } catch (Exception $exceptions) {
            $this->db_conns->rollBack();
            return false;
        }
    }

    public function get_recent_download_logs($limits = 15) {
        $querys = "
            SELECT dl.*, w.title AS work_title, wt.code AS type_code, u.name AS user_name
            FROM download_logs dl
            JOIN works w ON dl.work_id = w.id
            JOIN work_types wt ON w.work_type_id = wt.id
            LEFT JOIN users u ON dl.user_id = u.id
            ORDER BY dl.downloaded_at DESC
            LIMIT :limits
        ";
        $statements = $this->db_conns->prepare($querys);
        $statements->bindValue(':limits', (int)$limits, PDO::PARAM_INT);
        $statements->execute();
        return $statements->fetchAll();
    }

    public function get_admin_repository_stats() {
        $querys = "
            SELECT 
                COUNT(*) AS total_works,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS total_published,
                SUM(view_count) AS total_views,
                SUM(download_count) AS total_downloads,
                (SELECT COUNT(*) FROM download_logs) AS total_download_logs
            FROM works
        ";
        $statements = $this->db_conns->query($querys);
        return $statements->fetch(PDO::FETCH_ASSOC);
    }
}
