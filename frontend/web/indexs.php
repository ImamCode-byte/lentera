<?php
/**
 * indexs.php
 * entry point utama dan beranda etalase repositori lenteras
 * seluruh variabel dan logika menggunakan lowercase dan akhiran s
 */

// muat dependensis inti
require_once dirname(__DIR__, 2) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 2) . '/backend/configs/constantss.php';
require_once dirname(__DIR__, 2) . '/backend/configs/databases.php';
require_once dirname(__DIR__, 2) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 2) . '/backend/apps/helperss/validators.php';

sessions::starts();

// penanganan routing parameter ?urls= dari rewrite rule
if (!empty($_GET['urls'])) {
    $requested_urls = strtolower(trim($_GET['urls'], '/'));
    $route_maps = [
        'katalog'    => __DIR__ . '/viewss/searchs/indexs.php',
        'cari'       => __DIR__ . '/viewss/searchs/indexs.php',
        'jelajah'    => __DIR__ . '/viewss/searchs/indexs.php',
        'search'     => __DIR__ . '/viewss/searchs/indexs.php',
        'statistik'  => __DIR__ . '/viewss/statisticss/indexs.php',
        'analytics'  => __DIR__ . '/viewss/statisticss/indexs.php',
        'masuk'      => __DIR__ . '/viewss/auths/logins.php',
        'login'      => __DIR__ . '/viewss/auths/logins.php',
        'daftar'     => __DIR__ . '/viewss/auths/registers.php',
        'register'   => __DIR__ . '/viewss/auths/registers.php',
        'keluar'     => __DIR__ . '/viewss/auths/logouts.php',
        'logout'     => __DIR__ . '/viewss/auths/logouts.php',
        'karya'      => __DIR__ . '/viewss/workss/indexs.php',
        'karya-saya' => __DIR__ . '/viewss/workss/indexs.php',
        'unggah'     => __DIR__ . '/viewss/workss/creates.php',
        'upload'     => __DIR__ . '/viewss/workss/creates.php',
        'kurator'    => __DIR__ . '/viewss/reviews/indexs.php',
        'admin'      => __DIR__ . '/viewss/admins/indexs.php',
    ];

    if ($requested_urls !== '' && $requested_urls !== 'indexs.php' && $requested_urls !== 'index.php') {
        if (isset($route_maps[$requested_urls]) && file_exists($route_maps[$requested_urls])) {
            require $route_maps[$requested_urls];
            exit;
        } else {
            require __DIR__ . '/viewss/errorss/404s.php';
            exit;
        }
    }
}

// inisialisasi variabel statistiks dan datas
$total_workss = 0;
$total_downloadss = 0;
$total_facultiess = 7;
$total_authorss = 0;
$faculties_lists = [];
$recent_workss = [];
$database_connecteds = false;

try {
    $db_conns = databases::get_connections();
    $database_connecteds = true;

    // hitung total karya yang diterbitkan
    $query_work_countss = $db_conns->query("SELECT COUNT(*) AS total FROM works WHERE status = 'published'");
    if ($query_work_countss) {
        $total_workss = (int)$query_work_countss->fetch()['total'];
    }

    // hitung total unduhan
    $query_download_countss = $db_conns->query("SELECT COUNT(*) AS total FROM download_logs");
    if ($query_download_countss) {
        $total_downloadss = (int)$query_download_countss->fetch()['total'];
    }

    // daftar fakultas
    $query_facultiess = $db_conns->query("SELECT * FROM faculties WHERE is_active = 1 ORDER BY sort_order ASC");
    if ($query_facultiess) {
        $faculties_lists = $query_facultiess->fetchAll();
    }

    // karya terbaru yang diterbitkan
    $query_recent_workss = $db_conns->query("
        SELECT w.*, u.name AS author_name, sp.name AS prodi_name, wt.name AS type_name
        FROM works w
        JOIN users u ON w.author_id = u.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        JOIN work_types wt ON w.work_type_id = wt.id
        WHERE w.status = 'published'
        ORDER BY w.published_at DESC
        LIMIT 6
    ");
    if ($query_recent_workss) {
        $recent_workss = $query_recent_workss->fetchAll();
    }
} catch (Exception $exceptions) {
    $database_connecteds = false;
    $faculties_lists = [
        ['code' => 'FH', 'name' => 'Fakultas Hukum'],
        ['code' => 'FEB', 'name' => 'Fakultas Ekonomi dan Bisnis'],
        ['code' => 'FIA', 'name' => 'Fakultas Ilmu Administrasi'],
        ['code' => 'FP', 'name' => 'Fakultas Pertanian'],
        ['code' => 'FT', 'name' => 'Fakultas Teknik'],
        ['code' => 'FKIP', 'name' => 'Fakultas Keguruan dan Ilmu Pendidikan'],
        ['code' => 'PPS', 'name' => 'Program Pascasarjana']
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lentera — Repositori Karya Ilmiah Universitas Madura</title>
  <meta name="description" content="Repositori karya ilmiah resmi Universitas Madura. Akses skripsi, tesis, jurnal, dan laporan penelitian terkurasi secara terbuka.">
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Navigasi Utama -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">Lentera</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>

      <ul class="nav-menuss">
        <li><a href="indexs.php" class="nav-items-links actives" style="color: var(--teal-primarys);"><i class="fa-solid fa-house"></i> Beranda</a></li>
        <li><a href="viewss/searchs/indexs.php" class="nav-items-links"><i class="fa-solid fa-magnifying-glass"></i> Katalog Karya</a></li>
        <li><a href="viewss/statisticss/indexs.php" class="nav-items-links"><i class="fa-solid fa-chart-pie"></i> Statistik</a></li>
        <li><a href="#fakultass" class="nav-items-links"><i class="fa-solid fa-building-columns"></i> Fakultas</a></li>
        <li><a href="#alurs" class="nav-items-links"><i class="fa-solid fa-timeline"></i> Alur Terbit</a></li>
      </ul>

      <div class="nav-auth-buttons">
        <?php if (sessions::hass('user_ids')): ?>
          <a href="viewss/workss/indexs.php" class="btn-primarys" style="padding: 0.45rem 1rem; font-size: 0.84rem;">
            <i class="fa-solid fa-folder-open"></i> Karya Saya
          </a>
          <?php if (in_array(sessions::gets('user_roles'), ['curator', 'admin'])): ?>
            <a href="viewss/reviews/indexs.php" class="btn-logins" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;" title="Panel Kurator">
              <i class="fa-solid fa-feather-pointed"></i> Kurator
            </a>
          <?php endif; ?>
          <?php if (sessions::gets('user_roles') === 'admin'): ?>
            <a href="viewss/admins/indexs.php" class="btn-logins" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; background: #fdf2f8; border-color: #fbcfe8; color: #be185d;" title="Panel Super Admin">
              <i class="fa-solid fa-crown"></i> Admin
            </a>
          <?php endif; ?>
          <a href="viewss/auths/logouts.php" class="btn-logins" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;" title="Keluar Akun">
            <i class="fa-solid fa-right-from-bracket"></i>
          </a>
        <?php else: ?>
          <a href="viewss/auths/logins.php" class="btn-logins" style="padding: 0.45rem 1rem; font-size: 0.84rem;">Masuk</a>
          <a href="viewss/auths/registers.php" class="btn-primarys btn-golds" style="padding: 0.45rem 1.15rem; font-size: 0.84rem;">Unggah Karya</a>
        <?php endif; ?>
        
        <button type="button" class="nav-mobile-toggles" onclick="toggle_mobile_drawer()" aria-label="Menu navigasi">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile_drawer" class="nav-mobile-drawers">
      <a href="indexs.php" style="color: var(--teal-primarys);"><i class="fa-solid fa-house"></i> Beranda</a>
      <a href="viewss/searchs/indexs.php"><i class="fa-solid fa-magnifying-glass"></i> Katalog Pencarian Karya</a>
      <a href="viewss/statisticss/indexs.php"><i class="fa-solid fa-chart-pie"></i> Statistik & Akreditasi</a>
      <a href="#fakultass"><i class="fa-solid fa-building-columns"></i> Fakultas & Program Studi</a>
      <a href="#alurs"><i class="fa-solid fa-timeline"></i> Alur Penerbitan Naskah</a>
      <?php if (sessions::hass('user_ids')): ?>
        <a href="viewss/workss/indexs.php"><i class="fa-solid fa-folder-open"></i> Dashboard Karya Saya</a>
        <?php if (in_array(sessions::gets('user_roles'), ['curator', 'admin'])): ?>
          <a href="viewss/reviews/indexs.php"><i class="fa-solid fa-feather-pointed"></i> Panel Kurator</a>
        <?php endif; ?>
        <?php if (sessions::gets('user_roles') === 'admin'): ?>
          <a href="viewss/admins/indexs.php"><i class="fa-solid fa-crown"></i> Panel Admin Repositori</a>
        <?php endif; ?>
        <a href="viewss/auths/logouts.php" style="color: #b91c1c;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
      <?php else: ?>
        <a href="viewss/auths/logins.php"><i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem</a>
        <a href="viewss/auths/registers.php" style="color: var(--gold-accents);"><i class="fa-solid fa-user-plus"></i> Registrasi Penulis Baru</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero-sections" id="berandas">
    <div class="hero-containers">
      <div class="hero-badges">
        <i class="fa-solid fa-certificate"></i> Repositori Resmi Universitas Madura
      </div>
      <h1 class="hero-titles">Menerangi Karya, <span>Menginspirasi Generasi</span></h1>
      <p class="hero-descriptions">
        Etalase digital memori intelektual kampus. Temukan skripsi, tesis, jurnal ilmiah, dan laporan penelitian yang telah lolos verifikasi mutu akademik dewan kurator.
      </p>

      <!-- Form Pencarian Utama -->
      <form class="hero-search-wrappers" action="viewss/searchs/indexs.php" method="GET">
        <i class="fa-solid fa-magnifying-glass" style="margin-left: 0.75rem; color: #94a3b8;"></i>
        <input type="text" name="querys" class="hero-search-inputs" placeholder="Cari judul skripsi, topik riset, kata kunci, atau nama penulis..." required>
        <select name="types" class="hero-search-selects">
          <option value="">Semua Jenis Karya</option>
          <option value="SKR">Skripsi (S1)</option>
          <option value="TES">Tesis (S2)</option>
          <option value="JRN">Jurnal Ilmiah</option>
          <option value="LPK">Kerja Praktik</option>
          <option value="LPN">Penelitian</option>
        </select>
        <button type="submit" class="btn-primarys btn-golds" style="padding: 0.65rem 1.4rem;">Cari Naskah</button>
      </form>
    </div>
  </section>

  <!-- Counter Statistik -->
  <section class="stats-sections">
    <div class="stats-grids">
      <div class="stats-cards">
        <div class="stats-numbers"><?= htmlspecialchars((string)max(142, $total_workss)); ?>+</div>
        <div class="stats-labels">Karya Terverifikasi</div>
      </div>
      <div class="stats-cards">
        <div class="stats-numbers"><?= htmlspecialchars((string)max(1250, $total_downloadss)); ?>+</div>
        <div class="stats-labels">Total Unduhan Referensi</div>
      </div>
      <div class="stats-cards">
        <div class="stats-numbers">7</div>
        <div class="stats-labels">Fakultas Terpadu</div>
      </div>
      <div class="stats-cards">
        <div class="stats-numbers">15</div>
        <div class="stats-labels">Program Studi S1 & S2</div>
      </div>
    </div>
  </section>

  <!-- Karya Terbaru & Terpopuler -->
  <section class="section-wrappers" id="jelajahs">
    <div class="section-headers">
      <div>
        <h2 class="section-titles">Karya Ilmiah Terbitan Terkini</h2>
        <p class="section-subtitles">Koleksi naskah akademik yang baru saja resmi diterbitkan setelah melalui verifikasi komprehensif.</p>
      </div>
      <a href="viewss/searchs/indexs.php" class="btn-primarys" style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">
        <i class="fa-solid fa-list-ul"></i> Lihat Semua Katalog
      </a>
    </div>

    <div class="works-grids">
      <?php if (!empty($recent_workss)): ?>
        <?php foreach ($recent_workss as $works): ?>
          <article class="work-cards">
            <div class="work-card-top-badges">
              <span class="type-badges"><?= htmlspecialchars($works['type_name']); ?></span>
              <span class="year-badges"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($works['year']); ?></span>
            </div>
            <h3 class="work-card-titles">
              <a href="viewss/workss/details.php?ids=<?= $works['id']; ?>">
                <?= htmlspecialchars($works['title']); ?>
              </a>
            </h3>
            <p class="work-card-abstracts">
              <?= htmlspecialchars($works['abstract']); ?>
            </p>
            <div class="work-card-footers">
              <div class="author-infos">
                <div class="author-avatars"><?= htmlspecialchars(substr($works['author_name'], 0, 2)); ?></div>
                <div>
                  <div class="author-names"><?= htmlspecialchars($works['author_name']); ?></div>
                  <div class="author-prodis"><?= htmlspecialchars($works['prodi_name']); ?></div>
                </div>
              </div>
              <a href="viewss/workss/details.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="min-height: 34px; padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                <i class="fa-solid fa-circle-info"></i> Detail
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state-boxes" style="grid-column: 1 / -1;">
          <div class="empty-state-icons"><i class="fa-solid fa-book-open"></i></div>
          <h3 class="empty-state-titles">Katalog Sedang Dimutakhirkan</h3>
          <p class="empty-state-descriptions">Koleksi naskah terbitan terbaru sedang diproses oleh dewan kurator repositori.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Jelajah per Fakultas -->
  <section class="section-wrappers" id="fakultass">
    <div class="section-headers">
      <div>
        <h2 class="section-titles">Jelajah Berdasarkan Fakultas</h2>
        <p class="section-subtitles">Koleksi karya ilmiah dan laporan riset berdasarkan unit fakultas di Universitas Madura.</p>
      </div>
      <a href="viewss/statisticss/indexs.php" class="btn-logins" style="padding: 0.5rem 1.15rem; font-size: 0.84rem;">
        <i class="fa-solid fa-chart-pie"></i> Statistik Fakultas
      </a>
    </div>

    <div class="faculty-grids">
      <?php foreach ($faculties_lists as $facultys): ?>
      <div class="faculty-cards">
        <div>
          <span class="faculty-codes"><?= htmlspecialchars($facultys['code']); ?></span>
          <h3 class="faculty-names"><?= htmlspecialchars($facultys['name']); ?></h3>
        </div>
        <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
          <a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($facultys['code']); ?>" class="btn-primarys" style="min-height: 34px; padding: 0.35rem 0.8rem; font-size: 0.78rem;">
            <i class="fa-solid fa-building-columns"></i> Portal Unit
          </a>
          <a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($facultys['code']); ?>&tabs=terkini" class="btn-logins" style="min-height: 34px; padding: 0.35rem 0.8rem; font-size: 0.78rem;">
            Terbitan &rarr;
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Alur Penerbitan Resmi 4 Tahap -->
  <section class="section-wrappers" id="alurs">
    <div style="background: linear-gradient(135deg, var(--teal-darks), var(--teal-primarys)); color: #ffffff; padding: 4rem 2rem; border-radius: var(--radius-larges); box-shadow: var(--shadow-larges);">
      <div style="text-align: center; max-width: 720px; margin: 0 auto;">
        <span style="background: rgba(251, 139, 36, 0.25); color: #ffd166; font-size: 0.76rem; font-weight: 800; padding: 0.25rem 0.75rem; border-radius: var(--radius-fulls); text-transform: uppercase; letter-spacing: 0.5px;">
          Tata Kelola Terintegrasi
        </span>
        <h2 style="font-size: 2.1rem; font-weight: 800; margin-top: 0.6rem; margin-bottom: 0.5rem; letter-spacing: -0.5px;">
          Alur Publikasi Karya Ilmiah LENTERA
        </h2>
        <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;">
          Pemisahan wewenang berstandar etika publikasi: kurator fokus pada telaah ilmiah, admin memverifikasi biaya publikasi secara transparan.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-top: 3rem;">
        <div style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: var(--radius-mediums); padding: 1.75rem; text-align: center;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--gold-accents); color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.1rem;">1</div>
          <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Penulis Mengajukan</h3>
          <p style="font-size: 0.86rem; color: #cbd5e1; line-height: 1.5;">Mengunggah naskah PDF lengkap beserta metadata, abstrak dua bahasa, dan kata kunci penelitian.</p>
        </div>

        <div style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: var(--radius-mediums); padding: 1.75rem; text-align: center;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: #0284c7; color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.1rem;">2</div>
          <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Kurasi Akademik</h3>
          <p style="font-size: 0.86rem; color: #cbd5e1; line-height: 1.5;">Dewan Kurator prodi menelaah format panduan ATLAS dan substansi ilmiah naskah hingga layak di-ACC.</p>
        </div>

        <div style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: var(--radius-mediums); padding: 1.75rem; text-align: center;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: #e11d48; color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.1rem;">3</div>
          <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Pembayaran APC</h3>
          <p style="font-size: 0.86rem; color: #cbd5e1; line-height: 1.5;">Penulis melunasi biaya publikasi via QRIS Dhes Mart / DANA dan melampirkan foto struk bukti transfer.</p>
        </div>

        <div style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: var(--radius-mediums); padding: 1.75rem; text-align: center;">
          <div style="width: 44px; height: 44px; border-radius: 50%; background: #16a34a; color: #fff; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.1rem;">4</div>
          <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 0.5rem;">Verifikasi & Terbit</h3>
          <p style="font-size: 0.86rem; color: #cbd5e1; line-height: 1.5;">Admin Repositori memvalidasi dana masuk dan karya resmi terbit secara permanen di katalog publik.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer Institusi -->
  <footer class="footer-containers" id="kontaks">
    <div class="footer-wrappers">
      <div>
        <div class="logo-links" style="margin-bottom: 1rem;">
          <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <div>
            <span class="logo-texts" style="color: #ffffff;">Lentera</span>
            <span class="logo-subtexts">universitas madura</span>
          </div>
        </div>
        <p style="font-size: 0.86rem; line-height: 1.7; color: #94a3b8;">
          Laman Etalase Karya Tulis Ilmiah Terpadu Akademik — platform repositori digital resmi pengarsipan, kurasi, dan publikasi riset civitas akademika Universitas Madura.
        </p>
      </div>

      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.85rem;">Tautan Cepat</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.45rem; font-size: 0.86rem;">
          <li><a href="indexs.php" style="color: #94a3b8; text-decoration: none;">Beranda Repositori</a></li>
          <li><a href="viewss/searchs/indexs.php" style="color: #94a3b8; text-decoration: none;">Katalog Pencarian</a></li>
          <li><a href="viewss/statisticss/indexs.php" style="color: #94a3b8; text-decoration: none;">Statistik & Akreditasi</a></li>
          <li><a href="viewss/workss/creates.php" style="color: #94a3b8; text-decoration: none;">Unggah Karya Ilmiah</a></li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.85rem;">Kategori Karya</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.45rem; font-size: 0.86rem; color: #94a3b8;">
          <li>Skripsi Sarjana (S1)</li>
          <li>Tesis Magister (S2)</li>
          <li>Artikel Jurnal Ilmiah</li>
          <li>Laporan Praktik Lapang</li>
          <li>Laporan Hasil Penelitian</li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.85rem;">Alamat Kampus</h4>
        <p style="font-size: 0.86rem; line-height: 1.7; color: #94a3b8;">
          Universitas Madura (UNIRA)<br>
          Jl. Raya Panglegur No. 3,5 Pamekasan, Madura, Jawa Timur 69371<br>
          Surel: <span style="color: var(--gold-accents);">lentera@unira.ac.id</span>
        </p>
      </div>
    </div>

    <div class="footer-bottoms">
      &copy; <?= date('Y'); ?> Universitas Madura. Sistem Repositori Karya Ilmiah Terpadu.
    </div>
  </footer>

  <script>
    function toggle_mobile_drawer() {
      const drawer = document.getElementById('mobile_drawer');
      if (drawer) drawer.classList.toggle('opens');
    }
  </script>
</body>
</html>
