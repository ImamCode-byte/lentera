<?php
/**
 * details.php
 * halaman portal repositori khusus fakultas lenteras universitas madura
 * mengadaptasi fungsionalitas ejournal unit dengan desain prd deep teal + warm gold
 * seluruh variabel dan fungsi menggunakan lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/configs/constantss.php';
require_once dirname(__DIR__, 4) . '/backend/configs/databases.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/validators.php';

sessions::starts();

// ambil parameter kode atau id fakultas
$faculty_codes = trim($_GET['codes'] ?? 'FT');
$active_tabs = trim($_GET['tabs'] ?? 'terkini');
$search_querys = trim($_GET['qs'] ?? '');

$faculty_datas = null;
$study_programss = [];
$recent_workss = [];
$total_faculty_workss = 0;
$total_faculty_downloadss = 0;
$curator_lists = [];
$archive_yearss = [];

try {
    $db_conns = databases::get_connections();

    // 1. ambil data fakultas
    $stmt_facultys = $db_conns->prepare("SELECT * FROM faculties WHERE code = ? AND is_active = 1 LIMIT 1");
    $stmt_facultys->execute([$faculty_codes]);
    $faculty_datas = $stmt_facultys->fetch();

    if (!$faculty_datas) {
        // fallback jika kode tidak ketemu, ambil fakultas pertama
        $stmt_fallback = $db_conns->query("SELECT * FROM faculties WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 1");
        $faculty_datas = $stmt_fallback->fetch();
        $faculty_codes = $faculty_datas['code'] ?? 'FT';
    }

    $faculty_ids = (int)$faculty_datas['id'];

    // 2. ambil program studi di bawah fakultas ini
    $stmt_prodis = $db_conns->prepare("
        SELECT sp.*, COUNT(w.id) AS total_works
        FROM study_programs sp
        LEFT JOIN works w ON sp.id = w.prodi_id AND w.status = 'published'
        WHERE sp.faculty_id = ? AND sp.is_active = 1
        GROUP BY sp.id
        ORDER BY sp.name ASC
    ");
    $stmt_prodis->execute([$faculty_ids]);
    $study_programss = $stmt_prodis->fetchAll();

    // 3. hitung total karya terbit di fakultas ini
    $stmt_total_workss = $db_conns->prepare("
        SELECT COUNT(w.id) AS total
        FROM works w
        JOIN study_programs sp ON w.prodi_id = sp.id
        WHERE sp.faculty_id = ? AND w.status = 'published'
    ");
    $stmt_total_workss->execute([$faculty_ids]);
    $total_faculty_workss = (int)$stmt_total_workss->fetch()['total'];

    // 4. hitung total unduhan karya fakultas ini
    $stmt_total_downloadss = $db_conns->prepare("
        SELECT COUNT(dl.id) AS total
        FROM download_logs dl
        JOIN works w ON dl.work_id = w.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        WHERE sp.faculty_id = ?
    ");
    $stmt_total_downloadss->execute([$faculty_ids]);
    $total_faculty_downloadss = (int)$stmt_total_downloadss->fetch()['total'];

    // 5. ambil daftar karya (terkini / pencarian)
    $sql_workss = "
        SELECT w.*, u.name AS author_name, sp.name AS prodi_name, sp.degree, wt.name AS type_name
        FROM works w
        JOIN users u ON w.author_id = u.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        JOIN work_types wt ON w.work_type_id = wt.id
        WHERE sp.faculty_id = :faculty_ids AND w.status = 'published'
    ";
    $params_workss = [':faculty_ids' => $faculty_ids];

    if (!empty($search_querys)) {
        $sql_workss .= " AND (w.title LIKE :querys OR w.abstract LIKE :querys OR u.name LIKE :querys)";
        $params_workss[':querys'] = '%' . $search_querys . '%';
    }

    if ($active_tabs === 'arsip' && isset($_GET['tahuns'])) {
        $filter_years = (int)$_GET['tahuns'];
        $sql_workss .= " AND YEAR(w.published_at) = :tahuns";
        $params_workss[':tahuns'] = $filter_years;
    }

    $sql_workss .= " ORDER BY w.published_at DESC LIMIT 20";
    $stmt_workss = $db_conns->prepare($sql_workss);
    $stmt_workss->execute($params_workss);
    $recent_workss = $stmt_workss->fetchAll();

    // 6. ambil tahun arsip yang tersedia
    $stmt_yearss = $db_conns->prepare("
        SELECT DISTINCT YEAR(w.published_at) AS tahun, COUNT(w.id) AS count
        FROM works w
        JOIN study_programs sp ON w.prodi_id = sp.id
        WHERE sp.faculty_id = ? AND w.status = 'published'
        GROUP BY YEAR(w.published_at)
        ORDER BY tahun DESC
    ");
    $stmt_yearss->execute([$faculty_ids]);
    $archive_yearss = $stmt_yearss->fetchAll();

    // 7. tim kurator fakultas
    $stmt_curators = $db_conns->prepare("
        SELECT u.name, u.email, sp.name AS prodi_name
        FROM users u
        LEFT JOIN study_programs sp ON u.prodi_id = sp.id
        WHERE u.role IN ('curator', 'admin') AND (sp.faculty_id = ? OR sp.faculty_id IS NULL)
        LIMIT 6
    ");
    $stmt_curators->execute([$faculty_ids]);
    $curator_lists = $stmt_curators->fetchAll();

} catch (Exception $exceptions) {
    // fallback jika database bermasalah
    $faculty_datas = [
        'id' => 1,
        'code' => $faculty_codes,
        'name' => 'Fakultas ' . $faculty_codes
    ];
}

// deskripsi fokus keilmuan tiap fakultas
$faculty_scopes = [
    'FT' => 'Pengembangan rekayasa perangkat lunak, sistem cerdas, infrastruktur sipil tahan gempa, optimasi proses industri manufaktur dan supply chain regional Madura.',
    'FH' => 'Kajian hukum pidana, hukum perdata bisnis syariah, hukum tata negara, serta advokasi perlindungan hak masyarakat adat pesisir.',
    'FEB' => 'Riset manajemen strategis UMKM garam dan tembakau, akuntansi forensik, inovasi perbankan digital, dan ekonomi terapan daerah.',
    'FIA' => 'Kebijakan publik, tata kelola pemerintahan desa (good governance), administrasi niaga, dan digitalisasi layanan publik kepulauan.',
    'FP' => 'Agroteknologi lahan kering Madura, peternakan sapi lokal potong Madura, agribisnis ramah lingkungan, dan ketahanan pangan.',
    'FKIP' => 'Inovasi model pembelajaran abad 21, linguistik bahasa Indonesia dan Inggris, pendidikan matematika realistik, serta literasi pedagogik.',
    'PPS' => 'Riset multidisipliner tingkat magister di bidang kepastian hukum ekonomi, kepemimpinan korporasi, dan pembangunan berkelanjutan.'
];
$current_scopes = $faculty_scopes[$faculty_codes] ?? 'Pusat publikasi ilmiah, riset terapan, dan pengabdian masyarakat civitas akademika Universitas Madura.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($faculty_datas['name']); ?> — Repositori Lentera UNIRA</title>
  <meta name="description" content="Etalase repositori karya ilmiah resmi <?= htmlspecialchars($faculty_datas['name']); ?> Universitas Madura. Akses skripsi, tesis, dan jurnal.">
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers {
      max-width: 1240px;
      margin: 2rem auto 5rem;
      padding: 0 1.5rem;
    }
    
    /* banner fakultas */
    .faculty-banners {
      background: linear-gradient(135deg, var(--teal-darks) 0%, var(--teal-primarys) 100%);
      border-radius: var(--radius-larges);
      padding: 3rem 2.5rem;
      color: #ffffff;
      margin-bottom: 2.5rem;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-mediums);
    }
    .faculty-banners::before {
      content: '';
      position: absolute;
      top: -50px;
      right: -50px;
      width: 260px;
      height: 260px;
      background: rgba(251, 139, 36, 0.15);
      border-radius: 50%;
      filter: blur(40px);
    }
    .faculty-header-top {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1.5rem;
      flex-wrap: wrap;
      margin-bottom: 1.5rem;
    }
    .faculty-badges {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      padding: 0.35rem 0.9rem;
      border-radius: var(--radius-fulls);
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .faculty-titles {
      font-size: 2.3rem;
      font-weight: 800;
      line-height: 1.25;
      margin-bottom: 0.75rem;
    }
    .faculty-subtitles {
      font-size: 1rem;
      opacity: 0.9;
      max-width: 780px;
      line-height: 1.6;
    }

    /* quick stats banner */
    .faculty-stat-pills {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      margin-top: 1.75rem;
    }
    .stat-pill {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: var(--radius-mediums);
      padding: 0.65rem 1.2rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .stat-pill-val {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--gold-accents);
    }
    .stat-pill-lbl {
      font-size: 0.8rem;
      opacity: 0.85;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* layout 2 kolom: konten utama & sidebar akademik */
    .portal-grids {
      display: grid;
      grid-template-columns: 2.2fr 1fr;
      gap: 2.5rem;
    }
    @media (max-width: 992px) {
      .portal-grids {
        grid-template-columns: 1fr;
      }
    }

    /* navigasi tab lokal */
    .tab-bars {
      display: flex;
      gap: 0.5rem;
      border-bottom: 2px solid var(--border-colors);
      margin-bottom: 2rem;
      flex-wrap: wrap;
    }
    .tab-btn {
      padding: 0.75rem 1.3rem;
      font-size: 0.92rem;
      font-weight: 700;
      text-decoration: none;
      color: var(--text-bodys);
      border-bottom: 3px solid transparent;
      margin-bottom: -2px;
      transition: var(--transition-fasts);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .tab-btn.actives, .tab-btn:hover {
      color: var(--teal-primarys);
      border-bottom-color: var(--teal-primarys);
    }

    /* form cari lokal */
    .local-search-bars {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-mediums);
      padding: 0.5rem 0.75rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-smalls);
    }
    .local-search-inputs {
      border: none;
      outline: none;
      flex: 1;
      font-size: 0.92rem;
      padding: 0.4rem 0.6rem;
      font-family: inherit;
    }

    /* sidebar cards */
    .sidebar-cards {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: var(--shadow-smalls);
    }
    .sidebar-titles {
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--teal-primarys);
      margin-bottom: 1.1rem;
      padding-bottom: 0.6rem;
      border-bottom: 2px solid var(--teal-lights);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .sidebar-menus {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }
    .sidebar-menu-links {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.88rem;
      color: var(--text-bodys);
      text-decoration: none;
      padding: 0.45rem 0.65rem;
      border-radius: var(--radius-smalls);
      transition: var(--transition-fasts);
    }
    .sidebar-menu-links:hover {
      background: var(--teal-lights);
      color: var(--teal-primarys);
      font-weight: 600;
    }

    .btn-submit-manuscripts {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      width: 100%;
      background: linear-gradient(135deg, var(--gold-accents), #e87a15);
      color: #ffffff;
      text-decoration: none;
      padding: 0.95rem 1.25rem;
      border-radius: var(--radius-mediums);
      font-weight: 800;
      font-size: 0.95rem;
      box-shadow: var(--shadow-glows);
      transition: var(--transition-normals);
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .btn-submit-manuscripts:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(251, 139, 36, 0.4);
    }

    /* badge indexing */
    .index-badge-grids {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.75rem;
    }
    .index-badge-box {
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-smalls);
      padding: 0.65rem;
      text-align: center;
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--text-titles);
      background: #f8fafc;
    }

    /* kartu karya list */
    .work-items {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-mediums);
      padding: 1.5rem;
      margin-bottom: 1.25rem;
      transition: var(--transition-fasts);
      box-shadow: var(--shadow-smalls);
    }
    .work-items:hover {
      border-color: var(--teal-primarys);
      transform: translateY(-2px);
      box-shadow: var(--shadow-mediums);
    }
    .work-item-headers {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 0.75rem;
      font-size: 0.8rem;
    }
    .work-item-titles {
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text-titles);
      margin-bottom: 0.6rem;
      line-height: 1.4;
    }
    .work-item-titles a {
      color: inherit;
      text-decoration: none;
    }
    .work-item-titles a:hover {
      color: var(--teal-primarys);
    }
    .work-item-authors {
      font-size: 0.88rem;
      color: var(--text-bodys);
      margin-bottom: 0.75rem;
    }
  </style>
</head>
<body>

  <!-- navigasi atas -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="/lentera/indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">Lentera</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>

      <ul class="nav-menuss">
        <li><a href="/lentera/indexs.php" class="nav-items-links">Beranda</a></li>
        <li><a href="/lentera/viewss/searchs/indexs.php" class="nav-items-links">Katalog Karya</a></li>
        <li><a href="/lentera/viewss/statisticss/indexs.php" class="nav-items-links">Statistik</a></li>
        <li><a href="/lentera/indexs.php#fakultass" class="nav-items-links" style="color: var(--teal-primarys); font-weight: 700;">Fakultas</a></li>
      </ul>

      <div class="nav-auth-buttons">
        <?php if (sessions::hass('user_ids')): ?>
          <a href="/lentera/viewss/workss/indexs.php" class="nav-items-links" style="color: var(--teal-primarys); font-weight: 700;">
            <i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars(sessions::gets('user_names')); ?>
          </a>
          <a href="/lentera/viewss/auths/logouts.php" class="btn-logins" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;">Keluar</a>
        <?php else: ?>
          <a href="/lentera/viewss/auths/logins.php" class="btn-logins">Masuk</a>
          <a href="/lentera/viewss/auths/registers.php" class="btn-primarys">Daftar</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="page-containers">
    
    <!-- breadcrumb -->
    <div style="font-size: 0.85rem; color: var(--text-mutes); margin-bottom: 1.25rem;">
      <a href="/lentera/indexs.php" style="color: inherit; text-decoration: none;">Beranda</a> &rsaquo; 
      <a href="/lentera/indexs.php#fakultass" style="color: inherit; text-decoration: none;">Fakultas</a> &rsaquo; 
      <strong style="color: var(--teal-primarys);"><?= htmlspecialchars($faculty_datas['name']); ?></strong>
    </div>

    <!-- banner fakultas -->
    <section class="faculty-banners">
      <div class="faculty-header-top">
        <div class="faculty-badges">
          <i class="fa-solid fa-graduation-cap"></i> Repositori Unit Fakultas
        </div>
        <div style="display: flex; gap: 0.5rem;">
          <span class="faculty-badges" style="background: rgba(251, 139, 36, 0.25); border-color: rgba(251, 139, 36, 0.5);">
            <i class="fa-solid fa-unlock-keyhole"></i> Akses Terbuka (Open Access)
          </span>
          <span class="faculty-badges">
            <i class="fa-solid fa-certificate"></i> Terakreditasi
          </span>
        </div>
      </div>

      <h1 class="faculty-titles"><?= htmlspecialchars($faculty_datas['name']); ?></h1>
      <p class="faculty-subtitles"><?= htmlspecialchars($current_scopes); ?></p>

      <div class="faculty-stat-pills">
        <div class="stat-pill">
          <span class="stat-pill-val"><?= $total_faculty_workss; ?></span>
          <span class="stat-pill-lbl">Karya Terbit</span>
        </div>
        <div class="stat-pill">
          <span class="stat-pill-val"><?= $total_faculty_downloadss; ?></span>
          <span class="stat-pill-lbl">Total Unduhan</span>
        </div>
        <div class="stat-pill">
          <span class="stat-pill-val"><?= count($study_programss); ?></span>
          <span class="stat-pill-lbl">Program Studi</span>
        </div>
        <div class="stat-pill">
          <span class="stat-pill-val">CC BY 4.0</span>
          <span class="stat-pill-lbl">Lisensi Publikasi</span>
        </div>
      </div>
    </section>

    <!-- layout utama -->
    <div class="portal-grids">

      <!-- kolom kiri: konten & naskah -->
      <div>
        
        <!-- form cari lokal fakultas -->
        <form method="GET" action="" class="local-search-bars">
          <input type="hidden" name="codes" value="<?= htmlspecialchars($faculty_codes); ?>">
          <input type="hidden" name="tabs" value="<?= htmlspecialchars($active_tabs); ?>">
          <i class="fa-solid fa-magnifying-glass" style="color: var(--text-mutes); margin-left: 0.5rem;"></i>
          <input type="text" name="qs" value="<?= htmlspecialchars($search_querys); ?>" placeholder="Cari skripsi, tesis, atau riset di <?= htmlspecialchars($faculty_datas['name']); ?>..." class="local-search-inputs">
          <button type="submit" class="btn-primarys" style="padding: 0.45rem 1rem; font-size: 0.85rem;">Cari</button>
          <?php if (!empty($search_querys)): ?>
            <a href="details.php?codes=<?= urlencode($faculty_codes); ?>" style="font-size: 0.8rem; color: #dc2626; text-decoration: none; margin-left: 0.5rem;">Reset</a>
          <?php endif; ?>
        </form>

        <!-- tab navigasi fakultas -->
        <nav class="tab-bars">
          <a href="details.php?codes=<?= urlencode($faculty_codes); ?>&tabs=terkini" class="tab-btn <?= $active_tabs === 'terkini' ? 'actives' : ''; ?>">
            <i class="fa-solid fa-book-open"></i> Terbitan Terkini
          </a>
          <a href="details.php?codes=<?= urlencode($faculty_codes); ?>&tabs=arsip" class="tab-btn <?= $active_tabs === 'arsip' ? 'actives' : ''; ?>">
            <i class="fa-solid fa-box-archive"></i> Arsip Koleksi
          </a>
          <a href="details.php?codes=<?= urlencode($faculty_codes); ?>&tabs=prodi" class="tab-btn <?= $active_tabs === 'prodi' ? 'actives' : ''; ?>">
            <i class="fa-solid fa-sitemap"></i> Program Studi (<?= count($study_programss); ?>)
          </a>
          <a href="details.php?codes=<?= urlencode($faculty_codes); ?>&tabs=tentang" class="tab-btn <?= $active_tabs === 'tentang' ? 'actives' : ''; ?>">
            <i class="fa-solid fa-circle-info"></i> Dewan Kurator & Pedoman
          </a>
        </nav>

        <!-- isi tab 1: terbitan terkini -->
        <?php if ($active_tabs === 'terkini'): ?>
          <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles);">
              <?= !empty($search_querys) ? 'Hasil Pencarian: "' . htmlspecialchars($search_querys) . '"' : 'Karya Ilmiah Terbaru yang Diterbitkan'; ?>
            </h2>
            <span style="font-size: 0.85rem; color: var(--text-mutes);"><?= count($recent_workss); ?> naskah ditemukan</span>
          </div>

          <?php if (empty($recent_workss)): ?>
            <div style="background: #ffffff; border: 1px dashed var(--border-colors); border-radius: var(--radius-mediums); padding: 3rem; text-align: center;">
              <i class="fa-solid fa-folder-open" style="font-size: 2.5rem; color: var(--text-mutes); margin-bottom: 1rem;"></i>
              <h3 style="font-size: 1.1rem; color: var(--text-titles); margin-bottom: 0.5rem;">Belum Ada Karya yang Diterbitkan</h3>
              <p style="font-size: 0.88rem; color: var(--text-bodys); max-width: 480px; margin: 0 auto 1.5rem;">
                Karya ilmiah mahasiswa dan dosen di fakultas ini sedang dalam proses review kurasi atau silakan lakukan pencarian dengan kata kunci lain.
              </p>
              <a href="/lentera/viewss/workss/creates.php" class="btn-primarys">Unggah Karya Pertama</a>
            </div>
          <?php else: ?>
            <?php foreach ($recent_workss as $work_items): ?>
              <article class="work-items">
                <div class="work-item-headers">
                  <span class="type-badges" style="background: var(--teal-lights); color: var(--teal-primarys); padding: 0.2rem 0.6rem; border-radius: var(--radius-smalls); font-weight: 700; font-size: 0.75rem;">
                    <?= htmlspecialchars($work_items['type_name']); ?> (<?= htmlspecialchars($work_items['degree']); ?>)
                  </span>
                  <span style="color: var(--text-mutes);">
                    <i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($work_items['published_at'] ?? $work_items['created_at'])); ?>
                  </span>
                </div>
                
                <h3 class="work-item-titles">
                  <a href="/lentera/viewss/workss/details.php?ids=<?= (int)$work_items['id']; ?>">
                    <?= htmlspecialchars($work_items['title']); ?>
                  </a>
                </h3>

                <div class="work-item-authors">
                  <i class="fa-solid fa-user-pen" style="color: var(--gold-accents);"></i> 
                  <strong><?= htmlspecialchars($work_items['author_name']); ?></strong> &bull; 
                  <span>Program Studi <?= htmlspecialchars($work_items['prodi_name']); ?></span>
                </div>

                <p style="font-size: 0.88rem; color: var(--text-bodys); line-height: 1.6; margin-bottom: 1.25rem;">
                  <?= htmlspecialchars(mb_strimwidth($work_items['abstract'], 0, 220, '...')); ?>
                </p>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-colors); padding-top: 0.85rem; font-size: 0.82rem;">
                  <div style="display: flex; gap: 1rem; color: var(--text-mutes);">
                    <span><i class="fa-regular fa-eye"></i> <?= (int)$work_items['view_count']; ?> Dilihat</span>
                    <span><i class="fa-solid fa-download"></i> <?= (int)$work_items['download_count']; ?> Diunduh</span>
                  </div>
                  <div style="display: flex; gap: 0.5rem;">
                    <a href="/lentera/viewss/workss/details.php?ids=<?= (int)$work_items['id']; ?>" class="btn-primarys" style="padding: 0.35rem 0.8rem; font-size: 0.78rem;">
                      Lihat Abstrak & Sitasi
                    </a>
                    <a href="/lentera/viewss/workss/downloads.php?work_ids=<?= (int)$work_items['id']; ?>" class="btn-logins" style="padding: 0.35rem 0.8rem; font-size: 0.78rem;">
                      <i class="fa-solid fa-file-pdf" style="color: #dc2626;"></i> PDF
                    </a>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>

        <!-- isi tab 2: arsip -->
        <?php elseif ($active_tabs === 'arsip'): ?>
          <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles); margin-bottom: 1.25rem;">Arsip Terbitan Karya Ilmiah</h2>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <?php if (empty($archive_yearss)): ?>
              <p style="color: var(--text-mutes); font-size: 0.9rem;">Belum ada arsip tahunan yang tercatat.</p>
            <?php else: ?>
              <?php foreach ($archive_yearss as $archive_items): ?>
                <a href="details.php?codes=<?= urlencode($faculty_codes); ?>&tabs=arsip&tahuns=<?= (int)$archive_items['tahun']; ?>" style="background: #ffffff; border: 1px solid var(--border-colors); border-radius: var(--radius-mediums); padding: 1.25rem; text-decoration: none; color: inherit; display: block; transition: var(--transition-fasts);">
                  <div style="font-size: 1.5rem; font-weight: 800; color: var(--teal-primarys); margin-bottom: 0.25rem;"><?= (int)$archive_items['tahun']; ?></div>
                  <div style="font-size: 0.82rem; color: var(--text-mutes);"><?= (int)$archive_items['count']; ?> Karya Terpublikasi &rarr;</div>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

        <!-- isi tab 3: program studi -->
        <?php elseif ($active_tabs === 'prodi'): ?>
          <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles); margin-bottom: 1.25rem;">Program Studi di <?= htmlspecialchars($faculty_datas['name']); ?></h2>
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($study_programss as $prodi_items): ?>
              <div style="background: #ffffff; border: 1px solid var(--border-colors); border-radius: var(--radius-mediums); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <div style="font-size: 1.05rem; font-weight: 700; color: var(--text-titles); margin-bottom: 0.2rem;">
                    <?= htmlspecialchars($prodi_items['name']); ?> (<?= htmlspecialchars($prodi_items['degree']); ?>)
                  </div>
                  <div style="font-size: 0.82rem; color: var(--text-mutes);">
                    Kode Prodi: <strong><?= htmlspecialchars($prodi_items['code']); ?></strong> &bull; Jenjang Strata-<?= $prodi_items['degree'] === 'S2' ? '2 (Magister)' : '1 (Sarjana)'; ?>
                  </div>
                </div>
                <a href="/lentera/viewss/searchs/indexs.php?prodis=<?= (int)$prodi_items['id']; ?>" class="btn-primarys" style="padding: 0.45rem 0.9rem; font-size: 0.82rem;">
                  <?= (int)$prodi_items['total_works']; ?> Karya Terbit &rarr;
                </a>
              </div>
            <?php endforeach; ?>
          </div>

        <!-- isi tab 4: dewan kurator & pedoman -->
        <?php elseif ($active_tabs === 'tentang'): ?>
          <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles); margin-bottom: 1.25rem;">Dewan Kurator & Tim Penjamin Mutu</h2>
          <p style="font-size: 0.9rem; color: var(--text-bodys); line-height: 1.7; margin-bottom: 1.5rem;">
            Setiap karya ilmiah yang diunggah ke repositori <?= htmlspecialchars($faculty_datas['name']); ?> melalui tahapan pemeriksaan orisinalitas, validitas metodologi, serta format naskah oleh kurator program studi terkait.
          </p>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2.5rem;">
            <?php if (empty($curator_lists)): ?>
              <p style="color: var(--text-mutes); font-size: 0.88rem;">Daftar kurator prodi dapat dihubungi melalui sekretariat dekanat.</p>
            <?php else: ?>
              <?php foreach ($curator_lists as $curator_items): ?>
                <div style="background: #ffffff; border: 1px solid var(--border-colors); border-radius: var(--radius-mediums); padding: 1.25rem; display: flex; gap: 1rem; align-items: center;">
                  <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--teal-lights); color: var(--teal-primarys); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                    <?= strtoupper(substr($curator_items['name'], 0, 2)); ?>
                  </div>
                  <div>
                    <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-titles);"><?= htmlspecialchars($curator_items['name']); ?></div>
                    <div style="font-size: 0.78rem; color: var(--text-mutes);"><?= htmlspecialchars($curator_items['prodi_name'] ?? 'Kurator Utama Kampus'); ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-titles); margin-bottom: 0.75rem;">Etika Publikasi & Integritas Ilmiah</h3>
          <ul style="padding-left: 1.25rem; font-size: 0.88rem; color: var(--text-bodys); line-height: 1.8;">
            <li>Naskah wajib merupakan karya orisinal mahasiswa/peneliti dan belum pernah diterbitkan di repositori lain.</li>
            <li>Batas toleransi uji kemiripan (Turnitin / Plagiarism Checker) maksimal <strong>25%</strong>.</li>
            <li>Wajib mencantumkan data pembimbing utama dan pendamping secara akurat.</li>
            <li>Seluruh sitasi dan daftar referensi wajib mengacu pada literatur terindeks primer sekurang-kurangnya 80%.</li>
          </ul>
        <?php endif; ?>

      </div>

      <!-- kolom kanan: sidebar akademik -->
      <aside>
        
        <!-- tombol kirim naskah -->
        <a href="<?= sessions::hass('user_ids') ? '/lentera/viewss/workss/creates.php' : '/lentera/viewss/auths/logins.php'; ?>" class="btn-submit-manuscripts">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <span>Kirim Naskah Karya Ilmiah</span>
        </a>

        <!-- fokus & ruang lingkup -->
        <div class="sidebar-cards">
          <h3 class="sidebar-titles"><i class="fa-solid fa-bullseye"></i> Ruang Lingkup Riset</h3>
          <p style="font-size: 0.85rem; line-height: 1.6; color: var(--text-bodys); margin-bottom: 0.75rem;">
            <?= htmlspecialchars($current_scopes); ?>
          </p>
          <div style="font-size: 0.78rem; color: var(--teal-primarys); font-weight: 700;">
            <i class="fa-solid fa-check-circle"></i> Sesuai Rencana Induk Riset UNIRA
          </div>
        </div>

        <!-- navigasi tata kelola akademik -->
        <div class="sidebar-cards">
          <h3 class="sidebar-titles"><i class="fa-solid fa-compass"></i> Tata Kelola Repositori</h3>
          <ul class="sidebar-menus">
            <li>
              <a href="details.php?codes=<?= urlencode($faculty_codes); ?>&tabs=tentang" class="sidebar-menu-links">
                <i class="fa-solid fa-users" style="color: var(--teal-primarys);"></i> Tim Kurator & Reviewer
              </a>
            </li>
            <li>
              <a href="#pedoman" class="sidebar-menu-links" onclick="alert('Pedoman Penulisan: Naskah diketik dalam format A4, spasi 1.5, jenis huruf Times New Roman atau Calibri 12pt, lengkap dengan abstrak dwibahasa dan kata kunci.'); return false;">
                <i class="fa-solid fa-file-signature" style="color: var(--teal-primarys);"></i> Pedoman Penulisan
              </a>
            </li>
            <li>
              <a href="#alur" class="sidebar-menu-links" onclick="alert('Alur Kurasi: 1. Penulis submit PDF -> 2. Kurator verifikasi format & Turnitin -> 3. Keputusan (Terbit / Revisi / Tolak) dalam 3-5 hari kerja.'); return false;">
                <i class="fa-solid fa-code-compare" style="color: var(--teal-primarys);"></i> Alur Tinjauan & Kurasi
              </a>
            </li>
            <li>
              <a href="#plagiasi" class="sidebar-menu-links" onclick="alert('Standar Plagiarisme: Maksimal kesamaan naskah adalah 25%. File hasil cek Turnitin wajib dilampirkan.'); return false;">
                <i class="fa-solid fa-shield-halved" style="color: var(--teal-primarys);"></i> Kebijakan Plagiarisme (25%)
              </a>
            </li>
            <li>
              <a href="#hakcipta" class="sidebar-menu-links" onclick="alert('Lisensi Akses Terbuka: Naskah yang diterbitkan berada di bawah lisensi Creative Commons Attribution 4.0 International (CC BY 4.0).'); return false;">
                <i class="fa-solid fa-creative-commons" style="color: var(--teal-primarys);"></i> Lisensi CC BY 4.0
              </a>
            </li>
          </ul>
        </div>

        <!-- unduh template naskah -->
        <div class="sidebar-cards">
          <h3 class="sidebar-titles"><i class="fa-solid fa-file-word"></i> Template Dokumen</h3>
          <p style="font-size: 0.82rem; color: var(--text-bodys); margin-bottom: 1rem; line-height: 1.5;">
            Gunakan template resmi naskah skripsi dan jurnal agar mempermudah proses verifikasi tim kurator.
          </p>
          <a href="#" onclick="alert('Mengunduh Template Naskah Karya Ilmiah UNIRA (.DOCX)'); return false;" class="btn-logins" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.65rem; font-size: 0.85rem; font-weight: 700;">
            <i class="fa-solid fa-download"></i> Unduh Template (.DOCX)
          </a>
        </div>

        <!-- indeksasi & standar akademik -->
        <div class="sidebar-cards">
          <h3 class="sidebar-titles"><i class="fa-solid fa-award"></i> Standar Indeksasi</h3>
          <div class="index-badge-grids">
            <div class="index-badge-box">GARUDA</div>
            <div class="index-badge-box">Google Scholar</div>
            <div class="index-badge-box">ONESearch</div>
            <div class="index-badge-box">RAMA Kemenristek</div>
          </div>
          <div style="font-size: 0.75rem; color: var(--text-mutes); margin-top: 0.75rem; text-align: center;">
            Terhubung otomatis ke Pangkalan Data Pendidikan Tinggi (PDDikti)
          </div>
        </div>

        <!-- kotak bantuan sekretariat -->
        <div class="sidebar-cards" style="background: var(--teal-lights); border-color: rgba(15, 76, 92, 0.15);">
          <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--teal-primarys); margin-bottom: 0.5rem;">
            <i class="fa-solid fa-headset"></i> Butuh Bantuan?
          </h4>
          <p style="font-size: 0.82rem; color: var(--text-bodys); line-height: 1.6;">
            Sekretariat Repositori <?= htmlspecialchars($faculty_datas['name']); ?><br>
            Email: <strong style="color: var(--teal-primarys);"><?= strtolower($faculty_codes); ?>@lentera.unira.ac.id</strong>
          </p>
        </div>

      </aside>

    </div>

  </main>

  <!-- footers -->
  <footer class="footer-containers">
    <div class="footer-wrappers">
      <div>
        <div class="logo-links" style="margin-bottom: 1rem;">
          <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <div>
            <span class="logo-texts" style="color: #ffffff;">Lentera</span>
            <span class="logo-subtexts">universitas madura</span>
          </div>
        </div>
        <p style="font-size: 0.9rem; line-height: 1.7;">
          Laman Etalase Karya Tulis Ilmiah Terpadu Akademik — platform repositori resmi pengarsipan dan publikasi riset civitas akademika Universitas Madura.
        </p>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Tautan Cepat</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9rem;">
          <li><a href="/lentera/indexs.php" style="color: inherit; text-decoration: none;">Beranda</a></li>
          <li><a href="/lentera/viewss/searchs/indexs.php" style="color: inherit; text-decoration: none;">Katalog & Pencarian</a></li>
          <li><a href="/lentera/viewss/statisticss/indexs.php" style="color: inherit; text-decoration: none;">Statistik & Akreditasi</a></li>
          <li><a href="/lentera/viewss/workss/creates.php" style="color: inherit; text-decoration: none;">Panduan & Unggah Karya</a></li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Fakultas di UNIRA</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.88rem;">
          <li><a href="details.php?codes=FT" style="color: inherit; text-decoration: none;">Fakultas Teknik (FT)</a></li>
          <li><a href="details.php?codes=FH" style="color: inherit; text-decoration: none;">Fakultas Hukum (FH)</a></li>
          <li><a href="details.php?codes=FEB" style="color: inherit; text-decoration: none;">Fakultas Ekonomi dan Bisnis (FEB)</a></li>
          <li><a href="details.php?codes=FP" style="color: inherit; text-decoration: none;">Fakultas Pertanian (FP)</a></li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Sekretariat Pusat</h4>
        <p style="font-size: 0.88rem; line-height: 1.7;">
          Universitas Madura (UNIRA)<br>
          Jl. Raya Panglegur No. 3, Pamekasan, Madura<br>
          Surel: <span style="color: var(--gold-accents);">lentera@unira.ac.id</span>
        </p>
      </div>
    </div>

    <div class="footer-bottoms">
      &copy; 2026 Lentera Universitas Madura. Seluruh hak cipta dilindungi undang-undang.
    </div>
  </footer>

  <script src="/lentera/jss/apps.js?vs=1.0.1"></script>
</body>
</html>
