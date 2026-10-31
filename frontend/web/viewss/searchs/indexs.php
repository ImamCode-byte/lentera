<?php
/**
 * indexs.php
 * halaman pencarian dan penjelajahan katalog karya ilmiah terpublikasi
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/work_typess.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/study_programss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';

sessions::starts();

$query_texts = trim($_GET['querys'] ?? $_GET['qs'] ?? '');
$type_filters = trim($_GET['types'] ?? '');
$faculty_filters = trim($_GET['facultys'] ?? $_GET['codes'] ?? '');
$prodi_filters = !empty($_GET['prodis']) ? (int)$_GET['prodis'] : null;
$year_filters = !empty($_GET['years']) ? (int)$_GET['years'] : null;

$work_models = new workss();
$search_resultss = [];

try {
    $search_resultss = $work_models->search_public_works(
        $query_texts,
        $type_filters,
        $faculty_filters,
        $prodi_filters,
        $year_filters
    );
} catch (Exception $exceptions) {
    $search_resultss = [];
}

$work_type_models = new work_typess();
$work_type_lists = [];
try {
    $work_type_lists = $work_type_models->get_all_actives();
} catch (Exception $exceptions) {
    $work_type_lists = [];
}

$prodi_models = new study_programss();
$all_prodis = [];
try {
    $all_prodis = $prodi_models->get_all_actives();
} catch (Exception $exceptions) {
    $all_prodis = [];
}

$has_active_filters = !empty($query_texts) || !empty($type_filters) || !empty($faculty_filters) || !empty($prodi_filters) || !empty($year_filters);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Katalog Pencarian Karya — Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers { max-width: 1240px; margin: 2rem auto 5rem; padding: 0 1.5rem; }
    .search-filter-card {
      background: #ffffff;
      border-radius: var(--radius-mediums);
      padding: 1.5rem;
      border: 1px solid var(--border-colors);
      box-shadow: var(--shadow-smalls);
      margin-bottom: 2rem;
    }
    .filter-grids {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr 0.9fr auto;
      gap: 0.85rem;
      align-items: flex-end;
    }
    .filter-pills {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      background: var(--teal-lights);
      color: var(--teal-primarys);
      font-size: 0.78rem;
      font-weight: 700;
      padding: 0.25rem 0.65rem;
      border-radius: var(--radius-fulls);
      margin-right: 0.4rem;
      margin-bottom: 0.4rem;
    }
    @media (max-width: 960px) {
      .filter-grids { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
      .filter-grids { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- Navigasi Terpadu -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div><span class="logo-texts">Lentera</span><span class="logo-subtexts">katalog digital</span></div>
      </a>

      <ul class="nav-menuss">
        <li><a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-house"></i> Beranda</a></li>
        <li><a href="indexs.php" class="nav-items-links actives" style="color: var(--teal-primarys);"><i class="fa-solid fa-magnifying-glass"></i> Katalog</a></li>
        <li><a href="../statisticss/indexs.php" class="nav-items-links"><i class="fa-solid fa-chart-pie"></i> Statistik</a></li>
      </ul>

      <div class="nav-auth-buttons">
        <?php if (sessions::hass('user_ids')): ?>
          <a href="../workss/indexs.php" class="btn-primarys" style="padding: 0.45rem 1rem; font-size: 0.84rem;">
            <i class="fa-solid fa-folder-open"></i> Karya Saya
          </a>
        <?php else: ?>
          <a href="../auths/logins.php" class="btn-logins" style="padding: 0.45rem 1rem; font-size: 0.84rem;">Masuk</a>
        <?php endif; ?>
        <button type="button" class="nav-mobile-toggles" onclick="toggle_mobile_drawer()" aria-label="Menu navigasi">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile_drawer" class="nav-mobile-drawers">
      <a href="../../indexs.php"><i class="fa-solid fa-house"></i> Beranda</a>
      <a href="indexs.php" style="color: var(--teal-primarys);"><i class="fa-solid fa-magnifying-glass"></i> Katalog Pencarian</a>
      <a href="../statisticss/indexs.php"><i class="fa-solid fa-chart-pie"></i> Statistik & Akreditasi</a>
      <?php if (sessions::hass('user_ids')): ?>
        <a href="../workss/indexs.php"><i class="fa-solid fa-folder-open"></i> Dashboard Karya Saya</a>
        <a href="../auths/logouts.php" style="color: #b91c1c;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
      <?php else: ?>
        <a href="../auths/logins.php"><i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="page-containers">
    <div style="margin-bottom: 1.75rem;">
      <span style="font-size: 0.78rem; font-weight: 800; color: var(--gold-accents); text-transform: uppercase; letter-spacing: 0.5px;">
        <i class="fa-solid fa-compass"></i> Eksplorasi Koleksi Ilmiah
      </span>
      <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px; margin-top: 0.25rem;">
        Katalog Karya Tulis Ilmiah
      </h1>
      <p style="color: var(--text-bodys); font-size: 0.95rem; margin-top: 0.25rem;">
        Pencarian menyeluruh koleksi skripsi, tesis, jurnal, dan laporan penelitian civitas akademika Universitas Madura.
      </p>
    </div>

    <!-- Panel Filter Multi-Kriteria -->
    <div class="search-filter-card">
      <form action="indexs.php" method="GET" class="filter-grids">
        <div>
          <label class="form-labels" for="querys">Kata Kunci / Topik</label>
          <input type="text" id="querys" name="querys" value="<?= htmlspecialchars($query_texts); ?>" placeholder="Judul, topik riset, abstrak, atau penulis..." class="form-inputs">
        </div>

        <div>
          <label class="form-labels" for="types">Jenis Naskah</label>
          <select id="types" name="types" class="form-selects">
            <option value="">Semua Jenis</option>
            <?php foreach ($work_type_lists as $types): ?>
              <option value="<?= htmlspecialchars($types['code']); ?>" <?= $type_filters === $types['code'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($types['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="form-labels" for="facultys">Fakultas</label>
          <select id="facultys" name="facultys" class="form-selects">
            <option value="">Semua Fakultas</option>
            <option value="FH"   <?= $faculty_filters === 'FH' ? 'selected' : ''; ?>>Hukum (FH)</option>
            <option value="FEB"  <?= $faculty_filters === 'FEB' ? 'selected' : ''; ?>>Ekonomi & Bisnis (FEB)</option>
            <option value="FIA"  <?= $faculty_filters === 'FIA' ? 'selected' : ''; ?>>Ilmu Administrasi (FIA)</option>
            <option value="FP"   <?= $faculty_filters === 'FP' ? 'selected' : ''; ?>>Pertanian (FP)</option>
            <option value="FT"   <?= $faculty_filters === 'FT' ? 'selected' : ''; ?>>Teknik (FT)</option>
            <option value="FKIP" <?= $faculty_filters === 'FKIP' ? 'selected' : ''; ?>>Keguruan & Ilmu Pend. (FKIP)</option>
            <option value="PPS"  <?= $faculty_filters === 'PPS' ? 'selected' : ''; ?>>Pascasarjana (PPS)</option>
          </select>
        </div>

        <div>
          <label class="form-labels" for="prodis">Program Studi</label>
          <select id="prodis" name="prodis" class="form-selects">
            <option value="">Semua Prodi</option>
            <?php foreach ($all_prodis as $pr): ?>
              <option value="<?= $pr['id']; ?>" <?= $prodi_filters == $pr['id'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($pr['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="form-labels" for="years">Tahun Terbit</label>
          <input type="number" id="years" name="years" value="<?= htmlspecialchars((string)($year_filters ?? '')); ?>" placeholder="Tahun..." class="form-inputs">
        </div>

        <div>
          <button type="submit" class="btn-primarys btn-golds" style="width: 100%; justify-content: center;">
            <i class="fa-solid fa-magnifying-glass"></i> Cari
          </button>
        </div>
      </form>

      <?php if ($has_active_filters): ?>
        <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px dashed var(--border-colors); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
          <div style="display: flex; align-items: center; flex-wrap: wrap;">
            <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-mutes); margin-right: 0.5rem;">Filter Aktif:</span>
            <?php if (!empty($query_texts)): ?>
              <span class="filter-pills">"<?= htmlspecialchars($query_texts); ?>"</span>
            <?php endif; ?>
            <?php if (!empty($type_filters)): ?>
              <span class="filter-pills">Tipe: <?= htmlspecialchars($type_filters); ?></span>
            <?php endif; ?>
            <?php if (!empty($faculty_filters)): ?>
              <span class="filter-pills">Fakultas: <?= htmlspecialchars($faculty_filters); ?></span>
            <?php endif; ?>
            <?php if (!empty($year_filters)): ?>
              <span class="filter-pills">Tahun: <?= htmlspecialchars((string)$year_filters); ?></span>
            <?php endif; ?>
          </div>
          <a href="indexs.php" style="color: #b91c1c; font-size: 0.82rem; font-weight: 700; text-decoration: none;">
            <i class="fa-solid fa-rotate-left"></i> Reset Filter
          </a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Header Hasil Pencarian -->
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
      <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles); margin: 0;">
        Ditemukan <?= count($search_resultss); ?> Karya Ilmiah Terverifikasi
      </h2>
      <span style="font-size: 0.82rem; color: var(--text-mutes);">
        Menampilkan karya resmi terpublikasi
      </span>
    </div>

    <?php if (empty($search_resultss)): ?>
      <div class="empty-state-boxes table-cards">
        <div class="empty-state-icons"><i class="fa-solid fa-book-open-reader"></i></div>
        <h3 class="empty-state-titles">Tidak Ada Karya yang Cocok</h3>
        <p class="empty-state-descriptions">
          Coba gunakan kata kunci yang lebih umum, periksa ejaan, atau hapus sebagian filter pencarian Anda di atas.
        </p>
        <a href="indexs.php" class="btn-primarys" style="padding: 0.5rem 1.25rem;">
          <i class="fa-solid fa-arrows-rotate"></i> Tampilkan Semua Koleksi
        </a>
      </div>
    <?php else: ?>
      <div class="works-grids">
        <?php foreach ($search_resultss as $works): ?>
          <article class="work-cards">
            <div class="work-card-top-badges">
              <span class="type-badges"><?= htmlspecialchars($works['type_name']); ?></span>
              <span class="year-badges"><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($works['year']); ?></span>
            </div>
            <h3 class="work-card-titles">
              <a href="../workss/details.php?ids=<?= $works['id']; ?>">
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
              <a href="../workss/details.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="min-height: 34px; padding: 0.35rem 0.85rem; font-size: 0.8rem;">
                <i class="fa-solid fa-circle-info"></i> Detail
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Footer Institusi -->
  <footer class="footer-containers">
    <div class="footer-wrappers">
      <div>
        <div class="logo-links" style="margin-bottom: 0.85rem;">
          <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <div><span class="logo-texts" style="color: #ffffff;">Lentera</span><span class="logo-subtexts">universitas madura</span></div>
        </div>
        <p style="font-size: 0.84rem; line-height: 1.6; color: #94a3b8;">
          Katalog digital karya akademik Universitas Madura. Seluruh koleksi dapat diakses publik untuk pengembangan riset dan ilmu pengetahuan.
        </p>
      </div>
      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem;">Navigasi Cepat</h4>
        <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.84rem;">
          <a href="../../indexs.php" style="color: #94a3b8; text-decoration: none;">Beranda Repositori</a>
          <a href="indexs.php" style="color: #ffffff; text-decoration: none; font-weight: 700;">Katalog Pencarian</a>
          <a href="../statisticss/indexs.php" style="color: #94a3b8; text-decoration: none;">Statistik & Akreditasi</a>
        </div>
      </div>
      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem;">Layanan Penulis</h4>
        <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.84rem;">
          <a href="../auths/logins.php" style="color: #94a3b8; text-decoration: none;">Masuk Sistem</a>
          <a href="../auths/registers.php" style="color: #94a3b8; text-decoration: none;">Daftar Akun Penulis</a>
          <a href="../workss/creates.php" style="color: #94a3b8; text-decoration: none;">Unggah Karya Ilmiah</a>
        </div>
      </div>
      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem;">Alamat Kampus</h4>
        <p style="font-size: 0.84rem; line-height: 1.6; color: #94a3b8;">
          Universitas Madura (UNIRA)<br>
          Jl. Raya Panglegur KM 3,5 Pamekasan, Jawa Timur<br>
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
