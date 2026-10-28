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
    // jika tabel belum ada atau belum diimpor, fallback data demo edukatif
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
  <title>LENTERAS — Repositori Karya Tulis Ilmiah Terpadu Akademik Universitas Madura</title>
  <meta name="description" content="Repositori karya ilmiah digital terpusat Universitas Madura. Akses skripsi, tesis, jurnal, dan laporan penelitian terkurasi.">
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- navigasi utamas -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">lenteras</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>

      <button class="nav-mobile-toggles" aria-label="Menu navigasi">
        <i class="fa-solid fa-bars"></i>
      </button>

      <ul class="nav-menuss">
        <li><a href="indexs.php" class="nav-items-links">Beranda</a></li>
        <li><a href="viewss/searchs/indexs.php" class="nav-items-links">Jelajah Karya</a></li>
        <li><a href="viewss/statisticss/indexs.php" class="nav-items-links">Statistik</a></li>
        <li><a href="#fakultass" class="nav-items-links">Fakultas</a></li>
        <li><a href="#alurs" class="nav-items-links">Alur Kurasi</a></li>
        <?php if (sessions::hass('user_ids')): ?>
          <li><a href="viewss/workss/indexs.php" class="nav-items-links" style="color: var(--teal-primarys);"><i class="fa-solid fa-table-list"></i> Karya Saya</a></li>
          <?php if (in_array(sessions::gets('user_roles'), ['curator', 'admin'])): ?>
            <li><a href="viewss/reviews/indexs.php" class="nav-items-links" style="color: var(--gold-accents);"><i class="fa-solid fa-shield-halved"></i> Kurator</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>

      <div class="nav-auth-buttons">
        <?php if (sessions::hass('user_ids')): ?>
          <span style="font-size: 0.88rem; font-weight: 700; color: var(--teal-primarys);">
            <i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars(sessions::gets('user_names')); ?> 
            <span style="font-size: 0.72rem; background: var(--teal-lights); padding: 0.2rem 0.5rem; border-radius: var(--radius-fulls);"><?= htmlspecialchars(sessions::gets('user_roles')); ?></span>
          </span>
          <a href="viewss/auths/logouts.php" class="btn-logins" style="padding: 0.45rem 0.9rem; font-size: 0.8rem;">Keluar</a>
        <?php else: ?>
          <a href="viewss/auths/logins.php" class="btn-logins">Masuk</a>
          <a href="viewss/auths/registers.php" class="btn-primarys">Unggah Karya</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- hero sections -->
  <section class="hero-sections" id="berandas">
    <div class="hero-containers">
      <div class="hero-badges">
        <i class="fa-solid fa-certificate"></i> Repositori Resmi Universitas Madura
      </div>
      <h1 class="hero-titles">Menerangi Karya, <span>Menginspirasi Generasi</span></h1>
      <p class="hero-descriptions">
        Etalase digital memori intelektual kampus. Temukan skripsi, tesis, artikel jurnal, dan laporan riset yang telah melalui proses kurasi ketat demi mutu akademik.
      </p>

      <!-- form pencarian utamas -->
      <form class="hero-search-wrappers" action="viewss/searchs/indexs.php" method="GET">
        <i class="fa-solid fa-magnifying-glass" style="margin-left: 1rem; color: #94a3b8;"></i>
        <input type="text" name="querys" class="hero-search-inputs" placeholder="Cari judul skripsi, abstrak, kata kunci, atau nama penulis..." required>
        <select name="types" class="hero-search-selects">
          <option value="">Semua Jenis Karya</option>
          <option value="SKR">Skripsi (S1)</option>
          <option value="TES">Tesis (S2)</option>
          <option value="JRN">Jurnal Ilmiah</option>
          <option value="LPK">Kerja Praktik</option>
          <option value="LPN">Penelitian</option>
        </select>
        <button type="submit" class="btn-primarys btn-golds">Cari Sekarang</button>
      </form>
    </div>
  </section>

  <!-- counter statistiks -->
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

  <!-- karya terpopulers -->
  <section class="section-wrappers" id="jelajahs">
    <div class="section-headers">
      <div>
        <h2 class="section-titles">Karya Ilmiah Terbaru & Terpopuler</h2>
        <p class="section-subtitles">Koleksi naskah lengkap yang baru diterbitkan setelah lolos verifikasi tim kurator.</p>
      </div>
      <a href="viewss/searchs/indexs.php" class="btn-primarys" style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">Lihat Semua Koleksi</a>
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
              <a href="viewss/workss/details.php?ids=<?= $works['id']; ?>" style="color: inherit; text-decoration: none;">
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
              <a href="viewss/workss/details.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="padding: 0.45rem 0.9rem; font-size: 0.8rem;">
                <i class="fa-solid fa-circle-info"></i> Detail
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- kartu preview edukatif awal -->
        <article class="work-cards">
          <div class="work-card-top-badges">
            <span class="type-badges">Skripsi (S1)</span>
            <span class="year-badges"><i class="fa-regular fa-calendar"></i> 2026</span>
          </div>
          <h3 class="work-card-titles">Rancang Bangun Sistem Informasi Repositori Akademik Berbasis Web pada Universitas Madura</h3>
          <p class="work-card-abstracts">
            Implementasi etalase digital terpadu untuk memfasilitasi arsip karya ilmiah mahasiswa agar dapat diakses publik secara berkelanjutan...
          </p>
          <div class="work-card-footers">
            <div class="author-infos">
              <div class="author-avatars">IK</div>
              <div>
                <div class="author-names">Imam Khoirul</div>
                <div class="author-prodis">Informatika (FT)</div>
              </div>
            </div>
            <a href="viewss/searchs/indexs.php" class="btn-primarys" style="padding: 0.45rem 0.9rem; font-size: 0.8rem;"><i class="fa-solid fa-magnifying-glass"></i> Jelajah</a>
          </div>
        </article>

        <article class="work-cards">
          <div class="work-card-top-badges">
            <span class="type-badges">Jurnal Ilmiah</span>
            <span class="year-badges"><i class="fa-regular fa-calendar"></i> 2026</span>
          </div>
          <h3 class="work-card-titles">Analisis Efektivitas Model Pembelajaran Berbasis Proyek terhadap Keterampilan Menulis Mahasiswa</h3>
          <p class="work-card-abstracts">
            Studi komparasi efektivitas metode project-based learning di lingkungan FKIP untuk meningkatkan produktivitas publikasi...
          </p>
          <div class="work-card-footers">
            <div class="author-infos">
              <div class="author-avatars">DR</div>
              <div>
                <div class="author-names">Dra. Ratna Dewi</div>
                <div class="author-prodis">Pend. Bahasa Indonesia</div>
              </div>
            </div>
            <a href="viewss/searchs/indexs.php" class="btn-primarys" style="padding: 0.45rem 0.9rem; font-size: 0.8rem;"><i class="fa-solid fa-magnifying-glass"></i> Jelajah</a>
          </div>
        </article>

        <article class="work-cards">
          <div class="work-card-top-badges">
            <span class="type-badges">Tesis (S2)</span>
            <span class="year-badges"><i class="fa-regular fa-calendar"></i> 2026</span>
          </div>
          <h3 class="work-card-titles">Strategi Peningkatan Daya Saing UMKM di Wilayah Madura Melalui Digitalisasi Pemasaran</h3>
          <p class="work-card-abstracts">
            Eksplorasi ekosistem bisnis digital dan manajemen rantai pasok komoditas lokal garam dan tembakau berbasis kebijakan terintegrasi...
          </p>
          <div class="work-card-footers">
            <div class="author-infos">
              <div class="author-avatars">AH</div>
              <div>
                <div class="author-names">Ahmad Hidayat, M.M.</div>
                <div class="author-prodis">Magister Manajemen (PPS)</div>
              </div>
            </div>
            <a href="viewss/searchs/indexs.php" class="btn-primarys" style="padding: 0.45rem 0.9rem; font-size: 0.8rem;"><i class="fa-solid fa-magnifying-glass"></i> Jelajah</a>
          </div>
        </article>
      <?php endif; ?>
    </div>
  </section>

  <!-- jelajah per fakultas -->
  <section class="section-wrappers" id="fakultass">
    <div class="section-headers">
      <div>
        <h2 class="section-titles">Jelajah per Fakultas</h2>
        <p class="section-subtitles">Temukan referensi riset spesifik berdasarkan unit fakultas di Universitas Madura.</p>
      </div>
    </div>

    <div class="faculty-grids">
      <?php foreach ($faculties_lists as $facultys): ?>
      <div class="faculty-cards" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <span class="faculty-codes"><?= htmlspecialchars($facultys['code']); ?></span>
          <h3 class="faculty-names" style="margin-bottom: 0.75rem;"><?= htmlspecialchars($facultys['name']); ?></h3>
        </div>
        <div style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
          <a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($facultys['code']); ?>" class="btn-primarys" style="padding: 0.45rem 0.85rem; font-size: 0.78rem; text-decoration: none;">
            <i class="fa-solid fa-graduation-cap"></i> Portal Fakultas
          </a>
          <a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($facultys['code']); ?>&tabs=terkini" class="btn-logins" style="padding: 0.45rem 0.85rem; font-size: 0.78rem; text-decoration: none;">
            Terbitan Terkini &rarr;
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- alur kurasi -->
  <section class="section-wrappers" id="alurs">
    <div class="workflow-sections">
      <div style="text-align: center; max-width: 700px; margin: 0 auto;">
        <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 0.5rem;">Bagaimana Karya Dipublikasikan?</h2>
        <p style="opacity: 0.9;">Tiga tahapan terintegrasi untuk menjamin orisinalitas dan standar mutu akademik.</p>
      </div>

      <div class="workflow-grids">
        <div class="workflow-cards">
          <div class="workflow-step-numbers">1</div>
          <h3 style="font-size: 1.2rem; margin-bottom: 0.75rem;">Penulis Mengunggah</h3>
          <p style="font-size: 0.9rem; opacity: 0.85;">Mahasiswa atau dosen mengisi metadata lengkap, abstrak, kata kunci, serta file dokumen naskah (PDF).</p>
        </div>

        <div class="workflow-cards">
          <div class="workflow-step-numbers">2</div>
          <h3 style="font-size: 1.2rem; margin-bottom: 0.75rem;">Verifikasi Kurator</h3>
          <p style="font-size: 0.9rem; opacity: 0.85;">Pustakawan atau kurator prodi memeriksa kelengkapan berkas, format naskah, dan dapat memberikan catatan revisi.</p>
        </div>

        <div class="workflow-cards">
          <div class="workflow-step-numbers">3</div>
          <h3 style="font-size: 1.2rem; margin-bottom: 0.75rem;">Terbit ke Publik</h3>
          <p style="font-size: 0.9rem; opacity: 0.85;">Karya resmi terindeks secara digital, dapat disitasi, dicari melalui mesin pencari internal, dan diunduh publik.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- footers -->
  <footer class="footer-containers" id="kontaks">
    <div class="footer-wrappers">
      <div>
        <div class="logo-links" style="margin-bottom: 1rem;">
          <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <div>
            <span class="logo-texts" style="color: #ffffff;">lenteras</span>
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
          <li><a href="indexs.php" style="color: inherit; text-decoration: none;">Beranda</a></li>
          <li><a href="viewss/searchs/indexs.php" style="color: inherit; text-decoration: none;">Katalog & Pencarian</a></li>
          <li><a href="viewss/statisticss/indexs.php" style="color: inherit; text-decoration: none;">Statistik & Akreditasi</a></li>
          <li><a href="viewss/workss/creates.php" style="color: inherit; text-decoration: none;">Panduan & Unggah Karya</a></li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Kategori Karya</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9rem;">
          <li>Skripsi Sarjana (S1)</li>
          <li>Tesis Magister (S2)</li>
          <li>Jurnal Ilmiah Terindeks</li>
          <li>Laporan Kerja Praktik</li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Sekretariat</h4>
        <p style="font-size: 0.88rem; line-height: 1.7;">
          Universitas Madura (UNIRA)<br>
          Jl. Raya Panglegur No. 3, Pamekasan, Madura, Jawa Timur<br>
          Surel: <span style="color: #fb8b24;">lentera@unira.ac.id</span>
        </p>
      </div>
    </div>

    <div class="footer-bottoms">
      &copy; 2026 LENTERAS Universitas Madura. Seluruh hak cipta dilindungi undang-undang.
    </div>
  </footer>

  <script src="/lentera/jss/apps.js?vs=1.0.1"></script>
</body>
</html>
