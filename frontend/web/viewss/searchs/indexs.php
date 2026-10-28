<?php
/**
 * indexs.php
 * halaman pencarian dan penjelajahan katalog karya ilmiah terpublikasi
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/work_typess.php';
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pencarian & Jelajah Karya — Lentera</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers { max-width: 1240px; margin: 2rem auto 5rem; padding: 0 1.5rem; }
    .search-filter-boxes { background: #ffffff; border-radius: var(--radius-larges); padding: 1.75rem; border: 1px solid var(--border-colors); box-shadow: var(--shadow-smalls); margin-bottom: 2.5rem; }
    .filter-grids { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 1rem; align-items: flex-end; }
    @media (max-width: 900px) { .filter-grids { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div><span class="logo-texts">Lentera</span><span class="logo-subtexts">katalog digital</span></div>
      </a>
      <div style="display: flex; gap: 1rem; align-items: center;">
        <a href="../../indexs.php" class="nav-items-links">Beranda</a>
        <?php if (sessions::hass('user_ids')): ?>
          <a href="../workss/indexs.php" class="btn-primarys" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">Karya Saya</a>
        <?php else: ?>
          <a href="../auths/logins.php" class="btn-logins">Masuk</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="page-containers">
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px;">Eksplorasi Katalog Karya Ilmiah</h1>
      <p style="color: var(--text-bodys); font-size: 0.95rem;">Temukan naskah akademik terpublikasi berdasarkan topik, penulis, fakultas, atau jenis karya.</p>
    </div>

    <!-- panel formulir pencarian -->
    <div class="search-filter-boxes">
      <form action="indexs.php" method="GET" class="filter-grids">
        <div>
          <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-titles);">Kata Kunci Pencarian</label>
          <input type="text" name="querys" value="<?= htmlspecialchars($query_texts); ?>" placeholder="Judul, abstrak, topik, atau penulis..." class="hero-search-inputs" style="border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); width: 100%; padding: 0.7rem 0.9rem;">
        </div>

        <div>
          <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-titles);">Jenis Karya</label>
          <select name="types" class="hero-search-selects" style="border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); width: 100%; padding: 0.7rem 0.9rem;">
            <option value="">Semua Jenis</option>
            <?php foreach ($work_type_lists as $types): ?>
              <option value="<?= htmlspecialchars($types['code']); ?>" <?= $type_filters === $types['code'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($types['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-titles);">Fakultas</label>
          <select name="facultys" class="hero-search-selects" style="border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); width: 100%; padding: 0.7rem 0.9rem;">
            <option value="">Semua Fakultas</option>
            <option value="FH"   <?= $faculty_filters === 'FH' ? 'selected' : ''; ?>>Hukum (FH)</option>
            <option value="FEB"  <?= $faculty_filters === 'FEB' ? 'selected' : ''; ?>>Ekonomi & Bisnis (FEB)</option>
            <option value="FIA"  <?= $faculty_filters === 'FIA' ? 'selected' : ''; ?>>Ilmu Administrasi (FIA)</option>
            <option value="FP"   <?= $faculty_filters === 'FP' ? 'selected' : ''; ?>>Pertanian (FP)</option>
            <option value="FT"   <?= $faculty_filters === 'FT' ? 'selected' : ''; ?>>Teknik (FT)</option>
            <option value="FKIP" <?= $faculty_filters === 'FKIP' ? 'selected' : ''; ?>>Keguruan & Ilmu Pendidikan (FKIP)</option>
            <option value="PPS"  <?= $faculty_filters === 'PPS' ? 'selected' : ''; ?>>Pascasarjana (PPS)</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 0.4rem; color: var(--text-titles);">Tahun</label>
          <input type="number" name="years" value="<?= htmlspecialchars((string)($year_filters ?? '')); ?>" placeholder="Tahun..." style="border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); width: 100%; padding: 0.7rem 0.9rem;">
        </div>

        <div>
          <button type="submit" class="btn-primarys btn-golds" style="width: 100%; justify-content: center; padding: 0.75rem 1.4rem;">
            <i class="fa-solid fa-magnifying-glass"></i> Filter
          </button>
        </div>
      </form>
    </div>

    <!-- hasil pencarian -->
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
      <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles);">
        Hasil Temuan (<?= count($search_resultss); ?> Karya)
      </h2>
      <?php if (!empty($query_texts) || !empty($type_filters) || !empty($faculty_filters) || !empty($year_filters)): ?>
        <a href="indexs.php" style="color: #b91c1c; font-size: 0.85rem; font-weight: 700; text-decoration: none;">
          <i class="fa-solid fa-xmark"></i> Hapus Semua Filter
        </a>
      <?php endif; ?>
    </div>

    <?php if (empty($search_resultss)): ?>
      <div style="background: #ffffff; border-radius: var(--radius-larges); padding: 4rem 2rem; text-align: center; border: 1px solid var(--border-colors);">
        <i class="fa-solid fa-book-open-reader" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-titles); margin-bottom: 0.5rem;">Tidak Ditemukan Karya yang Sesuai</h3>
        <p style="color: var(--text-bodys); font-size: 0.9rem;">Coba gunakan kata kunci yang lebih umum atau kosongkan beberapa filter di atas.</p>
      </div>
    <?php else: ?>
      <div class="works-grids">
        <?php foreach ($search_resultss as $works): ?>
          <article class="work-cards">
            <div class="work-card-top-badges">
              <span class="type-badges"><?= htmlspecialchars($works['type_name']); ?></span>
              <span class="year-badges"><?= htmlspecialchars($works['year']); ?></span>
            </div>
            <h3 class="work-card-titles" style="font-size: 1.15rem;">
              <a href="../workss/details.php?ids=<?= $works['id']; ?>" style="color: inherit; text-decoration: none;">
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
              <a href="../workss/details.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">
                Detail <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
  <script src="../../jss/apps.js"></script>
</body>
</html>
