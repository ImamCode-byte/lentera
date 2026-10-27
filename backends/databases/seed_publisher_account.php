<?php
require_once dirname(__DIR__) . '/configs/databases.php';

try {
    $db = databases::get_connections();

    // 1. Ambil ID prodi Teknik Informatika (atau prodi pertama)
    $stmt_prodi = $db->query("SELECT id FROM study_programs WHERE code = 'TI' LIMIT 1");
    $prodi_id = (int)($stmt_prodi->fetchColumn() ?: 1);

    // Ambil ID tipe karya JRN (Jurnal) dan SKR (Skripsi)
    $stmt_jrn = $db->query("SELECT id FROM work_types WHERE code = 'JRN' LIMIT 1");
    $jrn_id = (int)($stmt_jrn->fetchColumn() ?: 1);

    $stmt_skr = $db->query("SELECT id FROM work_types WHERE code = 'SKR' LIMIT 1");
    $skr_id = (int)($stmt_skr->fetchColumn() ?: 1);

    // 2. Insert/Update Akun Kurator Imam Hambali
    $password_hash = password_hash('Imam Hambali 001', PASSWORD_BCRYPT);
    $stmt_user = $db->prepare("
        INSERT INTO users (name, username, email, password, role, nim_nip, prodi_id, affiliation, country, phone, orcid_id, bio, is_reviewer_candidate, is_active)
        VALUES (:name, :username, :email, :password, 'curator', :nim_nip, :prodi_id, :affiliation, 'Indonesia', '081234567890', :orcid_id, :bio, 1, 1)
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            username = VALUES(username),
            password = VALUES(password),
            role = 'curator',
            nim_nip = VALUES(nim_nip),
            prodi_id = VALUES(prodi_id),
            affiliation = VALUES(affiliation),
            country = 'Indonesia',
            phone = '081234567890',
            orcid_id = VALUES(orcid_id),
            bio = VALUES(bio),
            is_reviewer_candidate = 1,
            is_active = 1
    ");
    $stmt_user->execute([
        ':name'        => 'Imam Hambali, S.Kom., M.Kom.',
        ':username'    => 'imam_hambali_001',
        ':email'       => 'imamdhespachito001@gmail.com',
        ':password'    => $password_hash,
        ':nim_nip'     => '199208152020121001',
        ':prodi_id'    => $prodi_id,
        ':affiliation' => 'Universitas Madura',
        ':orcid_id'    => '0009-0004-9821-4321',
        ':bio'         => 'Kurator & Dosen Peneliti Bidang Jaringan Komputer, Mobile Ad Hoc Network, IoT, dan Business Intelligence Universitas Madura.'
    ]);

    // Ambil user_id
    $stmt_get_user = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt_get_user->execute(['imamdhespachito001@gmail.com']);
    $user_id = (int)$stmt_get_user->fetchColumn();

    echo "User curator registered with ID: {$user_id}\n";

    // 3. Daftarkan 3 Karya Ilmiah Nyata Terbit (Published) + 2 Antrean Kurator
    $works_to_seed = [
        [
            'title'        => 'Analisis Data Pariwisata Jawa Timur Menggunakan Business Intelligence untuk Pemetaan Potensi Destinasi',
            'abstract'     => 'Meskipun open data multidimensi menawarkan potensi analitik yang besar bagi manajemen pariwisata, proses transformasinya menjadi informasi spasial yang strategis masih menjadi tantangan metodologis. Penelitian ini merancang arsitektur Business Intelligence yang mereplikasi sintesis data terbuka pariwisata untuk mengkalkulasi, mengelompokkan, dan memetakan potensi destinasi secara objektif melalui algoritma clustering spasial terdistribusi.',
            'work_type_id' => $jrn_id,
            'prodi_id'     => $prodi_id,
            'author_id'    => $user_id,
            'status'       => 'published',
            'year'         => 2026,
            'view_count'   => 142,
            'download_count'=> 45,
            'keywords'     => ['Business Intelligence', 'Spatial Mapping', 'Open Data', 'Data Clustering', 'Pariwisata Jatim']
        ],
        [
            'title'        => 'Implementasi IoT pada Sistem Irigasi Otomatis Berbasis Machine Learning untuk Efisiensi Pertanian Cerdas',
            'abstract'     => 'Penerapan Internet of Things (IoT) pada sektor agrikultur di wilayah lahan kering Madura untuk mengotomatisasi penyiraman dan pemberian nutrisi tanaman berbasis sensor kelembaban tanah, suhu, dan algoritma prediktif machine learning guna menghemat konsumsi air hingga 38%.',
            'work_type_id' => $skr_id,
            'prodi_id'     => $prodi_id,
            'author_id'    => $user_id,
            'status'       => 'published',
            'year'         => 2026,
            'view_count'   => 98,
            'download_count'=> 32,
            'keywords'     => ['Internet of Things', 'Smart Agriculture', 'Machine Learning', 'Irigasi Otomatis']
        ],
        [
            'title'        => 'Optimasi Konsumsi Air Pada Sistem Irigasi Cerdas Berbasis IoT Menggunakan Sensor Kelembaban Tanah',
            'abstract'     => 'Studi eksperimental pengukuran efisiensi penggunaan sumber daya air tawar tanah pada perkebunan tembakau dan jagung di Pamekasan menggunakan modul ESP32, sensor resistif kelembaban tanah, dan protokol MQTT.',
            'work_type_id' => $jrn_id,
            'prodi_id'     => $prodi_id,
            'author_id'    => $user_id,
            'status'       => 'published',
            'year'         => 2026,
            'view_count'   => 76,
            'download_count'=> 28,
            'keywords'     => ['IoT', 'Optimasi Air', 'Sensor Tanah', 'ESP32']
        ],
        [
            'title'        => 'Rancang Bangun Sistem Pemantauan Distribusi Logistik Garam Berbasis RFID dan GPS Tracker di Madura',
            'abstract'     => 'Penelitian integrasi modul RFID dan komunikasi satelit GPS untuk melacak alur distribusi dan transparansi tonase panen garam rakyat dari sentra tambak ke gudang penyangga di pesisir Sumenep dan Sampang.',
            'work_type_id' => $skr_id,
            'prodi_id'     => $prodi_id,
            'author_id'    => $user_id,
            'status'       => 'submitted',
            'year'         => 2026,
            'view_count'   => 12,
            'download_count'=> 0,
            'keywords'     => ['RFID', 'GPS Tracker', 'Logistik Garam', 'Rantai Pasok']
        ],
        [
            'title'        => 'Klasifikasi Kualitas Daun Tembakau Madura Menggunakan Convolutional Neural Network (CNN)',
            'abstract'     => 'Model klasifikasi citra digital berbasis arsitektur MobileNetV2 dan CNN untuk mengklasifikasi grade mutu daun tembakau rajangan kering secara objektif guna membantu petani menentukan harga pasar.',
            'work_type_id' => $jrn_id,
            'prodi_id'     => $prodi_id,
            'author_id'    => $user_id,
            'status'       => 'revision',
            'year'         => 2026,
            'view_count'   => 24,
            'download_count'=> 2,
            'keywords'     => ['Computer Vision', 'CNN', 'Tembakau Madura', 'Klasifikasi Citra']
        ]
    ];

    foreach ($works_to_seed as $item) {
        $stmt_check = $db->prepare("SELECT id FROM works WHERE title = ? LIMIT 1");
        $stmt_check->execute([$item['title']]);
        $existing_id = $stmt_check->fetchColumn();

        if (!$existing_id) {
            $stmt_ins = $db->prepare("
                INSERT INTO works (title, abstract, work_type_id, author_id, prodi_id, status, year, view_count, download_count, published_at, created_at)
                VALUES (:title, :abstract, :type, :author, :prodi, :status, :year, :views, :downloads, :pub_at, CURRENT_TIMESTAMP)
            ");
            $pub_at = ($item['status'] === 'published') ? date('Y-m-d H:i:s') : null;
            $stmt_ins->execute([
                ':title'     => $item['title'],
                ':abstract'  => $item['abstract'],
                ':type'      => $item['work_type_id'],
                ':author'    => $item['author_id'],
                ':prodi'     => $item['prodi_id'],
                ':status'    => $item['status'],
                ':year'      => $item['year'],
                ':views'     => $item['view_count'],
                ':downloads' => $item['download_count'],
                ':pub_at'    => $pub_at
            ]);
            $w_id = $db->lastInsertId();

            // Insert keywords
            $stmt_kw = $db->prepare("INSERT INTO work_keywords (work_id, keyword) VALUES (?, ?)");
            foreach ($item['keywords'] as $kw) {
                $stmt_kw->execute([$w_id, $kw]);
            }

            // Insert work_files
            $stmt_file = $db->prepare("
                INSERT INTO work_files (work_id, original_name, stored_name, file_path, file_size, version, is_current)
                VALUES (?, ?, ?, ?, 1048576, 1, 1)
            ");
            $dummy_file = 'naskah_publikasi_' . $w_id . '.pdf';
            $stmt_file->execute([$w_id, $dummy_file, $dummy_file, 'uploads/' . $dummy_file]);

            echo "Inserted work ID: {$w_id} - {$item['title']}\n";
        } else {
            echo "Work already exists with ID: {$existing_id}\n";
        }
    }

    echo "Semua akun dan karya berhasil disinkronisasi!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}