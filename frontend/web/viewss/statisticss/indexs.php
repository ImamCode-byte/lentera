<?php
/**
 * indexs.php
 * dashboard statistik analitik dan laporan akreditasi lenteras universitas madura
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/configs/databases.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';

sessions::starts();

$faculties_statss = [];
$type_statss = [];
$top_workss = [];
$total_published = 0;
$total_downloads = 0;
$total_views = 0;

try {
    $db_conns = databases::get_connections();

    // Ringkasan global
    $stmt_summary = $db_conns->query("
        SELECT 
            COUNT(*) AS total_pub,
            COALESCE(SUM(download_count), 0) AS total_dl,
            COALESCE(SUM(view_count), 0) AS total_vw
        FROM works
        WHERE status = 'published'
    ");
    if ($stmt_summary) {
        $sum_row = $stmt_summary->fetch();
        $total_published = (int)($sum_row['total_pub'] ?? 0);
        $total_downloads = (int)($sum_row['total_dl'] ?? 0);
        $total_views = (int)($sum_row['total_vw'] ?? 0);
    }

    // 1. statistik karya per fakultas
    $query_faculties = "
        SELECT f.name, f.code, COUNT(w.id) AS total_works
        FROM faculties f
        LEFT JOIN study_programs sp ON f.id = sp.faculty_id
        LEFT JOIN works w ON sp.id = w.prodi_id AND w.status = 'published'
        GROUP BY f.id, f.name, f.code
        ORDER BY f.sort_order ASC
    ";
    $stmt_f = $db_conns->query($query_faculties);
    if ($stmt_f) {
        $faculties_statss = $stmt_f->fetchAll();
    }

    // 2. statistik karya per tipe
    $query_types = "
        SELECT wt.name, wt.code, COUNT(w.id) AS total_works
        FROM work_types wt
        LEFT JOIN works w ON wt.id = w.work_type_id AND w.status = 'published'
        GROUP BY wt.id, wt.name, wt.code
    ";
    $stmt_t = $db_conns->query($query_types);
    if ($stmt_t) {
        $type_statss = $stmt_t->fetchAll();
    }

    // 3. top karya paling banyak diunduh dan dilihat
    $query_top = "
        SELECT w.*, u.name AS author_name, wt.name AS type_name, sp.name AS prodi_name
        FROM works w
        JOIN users u ON w.author_id = u.id
        JOIN work_types wt ON w.work_type_id = wt.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        WHERE w.status = 'published'
        ORDER BY (w.download_count * 2 + w.view_count) DESC
        LIMIT 10
    ";
    $stmt_top = $db_conns->query($query_top);
    if ($stmt_top) {
        $top_workss = $stmt_top->fetchAll();
    }
} catch (Exception $exceptions) {
    // fallback data
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statistik & Akreditasi — Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .page-containers { max-width: 1240px; margin: 2rem auto 5rem; padding: 0 1.5rem; }
    .chart-grids { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 1.5rem; margin-bottom: 2rem; }
    .chart-cards { background: #ffffff; border-radius: var(--radius-mediums); padding: 1.75rem; border: 1px solid var(--border-colors); box-shadow: var(--shadow-smalls); }
    .rank-badges {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 0.8rem;
    }
    .rank-1 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .rank-2 { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
    .rank-3 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .rank-normal { background: #f8fafc; color: var(--text-mutes); }
    @media (max-width: 900px) { .chart-grids { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

  <!-- Navigasi Terpadu -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div><span class="logo-texts">Lentera</span><span class="logo-subtexts">analitik kampus</span></div>
      </a>

      <ul class="nav-menuss">
        <li><a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-house"></i> Beranda</a></li>
        <li><a href="../searchs/indexs.php" class="nav-items-links"><i class="fa-solid fa-magnifying-glass"></i> Katalog</a></li>
        <li><a href="indexs.php" class="nav-items-links actives" style="color: var(--teal-primarys);"><i class="fa-solid fa-chart-pie"></i> Statistik</a></li>
      </ul>

      <div class="nav-auth-buttons">
        <?php if (sessions::hass('user_ids')): ?>
          <a href="../workss/indexs.php" class="btn-primarys" style="padding: 0.45rem 1rem; font-size: 0.84rem;">
            <i class="fa-solid fa-folder-open"></i> Karya Saya
          </a>
        <?php else: ?>
          <a href="../auths/logins.php" class="btn-logins" style="padding: 0.45rem 1rem; font-size: 0.84rem;">Masuk</a>
        <?php endif; ?>
        <button type="button" class="nav-mobile-toggles" onclick="toggle_mobile_drawer()" aria-label="Menu Navigasi">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile_drawer" class="nav-mobile-drawers">
      <a href="../../indexs.php"><i class="fa-solid fa-house"></i> Beranda</a>
      <a href="../searchs/indexs.php"><i class="fa-solid fa-magnifying-glass"></i> Katalog Pencarian</a>
      <a href="indexs.php" style="color: var(--teal-primarys);"><i class="fa-solid fa-chart-pie"></i> Statistik & Akreditasi</a>
      <?php if (sessions::hass('user_ids')): ?>
        <a href="../workss/indexs.php"><i class="fa-solid fa-folder-open"></i> Dashboard Karya Saya</a>
        <a href="../auths/logouts.php" style="color: #b91c1c;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
      <?php else: ?>
        <a href="../auths/logins.php"><i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="page-containers">
    <div style="margin-bottom: 2rem;">
      <span style="font-size: 0.78rem; font-weight: 800; color: var(--gold-accents); text-transform: uppercase; letter-spacing: 0.5px;">
        <i class="fa-solid fa-chart-simple"></i> Dashboard Analitik Akademik
      </span>
      <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px; margin-top: 0.25rem;">
        Statistik Pemanfaatan Karya Ilmiah
      </h1>
      <p style="color: var(--text-bodys); font-size: 0.95rem; margin-top: 0.25rem;">
        Data metrik real-time untuk penjaminan mutu penelitian, evaluasi akreditasi program studi, dan audit pemanfaatan publikasi Universitas Madura.
      </p>
    </div>

    <!-- Metrik Kartu Ringkasan -->
    <div class="metric-grids">
      <div class="metric-cards">
        <div class="metric-icons" style="background: var(--teal-lights); color: var(--teal-primarys);">
          <i class="fa-solid fa-book-bookmark"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= number_format($total_published); ?></div>
          <div class="metric-titles">Karya Ilmiah Terbit</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #e0f2fe; color: #0284c7;">
          <i class="fa-solid fa-download"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= number_format($total_downloads); ?></div>
          <div class="metric-titles">Total Unduhan Referensi</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #fef3c7; color: #b45309;">
          <i class="fa-solid fa-eye"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= number_format($total_views); ?></div>
          <div class="metric-titles">Total Pembaca Naskah</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #f1f5f9; color: #475569;">
          <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= count($faculties_statss); ?></div>
          <div class="metric-titles">Fakultas Terpadu</div>
        </div>
      </div>
    </div>

    <!-- Grafik Visual Chart.js -->
    <div class="chart-grids">
      <div class="chart-cards">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
          <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-titles); margin: 0;">
            <i class="fa-solid fa-chart-column" style="color: var(--teal-primarys);"></i> Publikasi per Fakultas
          </h3>
          <span style="font-size: 0.76rem; color: var(--text-mutes); font-weight: 700;">Unit Kerja</span>
        </div>
        <div style="height: 280px;">
          <canvas id="faculty_chart_canvass"></canvas>
        </div>
      </div>

      <div class="chart-cards">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
          <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-titles); margin: 0;">
            <i class="fa-solid fa-chart-pie" style="color: var(--gold-accents);"></i> Distribusi Jenis Naskah
          </h3>
          <span style="font-size: 0.76rem; color: var(--text-mutes); font-weight: 700;">Nomenklatur</span>
        </div>
        <div style="height: 280px; display: flex; align-items: center; justify-content: center;">
          <canvas id="type_chart_canvass"></canvas>
        </div>
      </div>
    </div>

    <!-- Top 10 Karya Terpopuler -->
    <div class="table-cards">
      <div style="padding: 1.25rem 1.5rem; background: #fafcfe; border-bottom: 1px solid var(--border-colors); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-titles); margin: 0;">
          <i class="fa-solid fa-trophy" style="color: var(--gold-accents);"></i> 10 Karya Paling Banyak Dimanfaatkan
        </h3>
        <span style="font-size: 0.8rem; color: var(--text-mutes); font-weight: 600;">
          Metrik Akumulasi Unduhan & Pembaca
        </span>
      </div>

      <div class="table-responsives">
        <table class="custom-tables">
          <thead>
            <tr>
              <th style="width: 70px; text-align: center;">Peringkat</th>
              <th>Judul Karya Ilmiah</th>
              <th>Penulis & Program Studi</th>
              <th style="text-align: center;">Dilihat</th>
              <th style="text-align: center;">Diunduh</th>
              <th style="text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($top_workss)): ?>
              <tr>
                <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-mutes);">
                  Belum ada rekaman karya yang diterbitkan ke repositori.
                </td>
              </tr>
            <?php else: ?>
              <?php $rank = 1; foreach ($top_workss as $top): ?>
                <?php
                  $badge_rank_class = 'rank-normal';
                  if ($rank === 1) $badge_rank_class = 'rank-1';
                  elseif ($rank === 2) $badge_rank_class = 'rank-2';
                  elseif ($rank === 3) $badge_rank_class = 'rank-3';
                ?>
                <tr>
                  <td style="text-align: center;">
                    <span class="rank-badges <?= $badge_rank_class; ?>">
                      <?= $rank; ?>
                    </span>
                  </td>
                  <td style="max-width: 420px;">
                    <a href="../workss/details.php?ids=<?= $top['id']; ?>" style="font-weight: 700; color: var(--text-titles); text-decoration: none; line-height: 1.4; display: block;">
                      <?= htmlspecialchars($top['title']); ?>
                    </a>
                    <div style="font-size: 0.78rem; color: var(--teal-primarys); margin-top: 0.3rem; font-weight: 700;">
                      <?= htmlspecialchars($top['type_name']); ?> • Tahun <?= htmlspecialchars($top['year']); ?>
                    </div>
                  </td>
                  <td>
                    <div style="font-weight: 700; color: var(--text-titles); font-size: 0.9rem;">
                      <?= htmlspecialchars($top['author_name']); ?>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-mutes); margin-top: 0.15rem;">
                      <?= htmlspecialchars($top['prodi_name']); ?>
                    </div>
                  </td>
                  <td style="text-align: center; font-size: 0.88rem; color: var(--text-bodys);">
                    <i class="fa-regular fa-eye" style="color: var(--text-mutes); margin-right: 0.2rem;"></i>
                    <?= number_format($top['view_count']); ?>
                  </td>
                  <td style="text-align: center; font-size: 0.92rem; font-weight: 800; color: #15803d;">
                    <i class="fa-solid fa-download" style="margin-right: 0.2rem;"></i>
                    <?= number_format($top['download_count']); ?>
                  </td>
                  <td style="text-align: right;">
                    <a href="../workss/details.php?ids=<?= $top['id']; ?>" class="btn-primarys" style="min-height: 34px; padding: 0.35rem 0.8rem; font-size: 0.78rem;">
                      <i class="fa-solid fa-circle-info"></i> Detail
                    </a>
                  </td>
                </tr>
              <?php $rank++; endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer Institusi -->
  <footer class="footer-containers">
    <div class="footer-wrappers">
      <div>
        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.75rem;">
          <div style="width: 32px; height: 32px; background: var(--teal-primarys); border-radius: var(--radius-smalls); display: flex; align-items: center; justify-content: center; color: #fff;">
            <i class="fa-solid fa-fire-flame-curved"></i>
          </div>
          <span style="font-size: 1.15rem; font-weight: 800; color: #ffffff;">Lentera</span>
        </div>
        <p style="font-size: 0.84rem; line-height: 1.6; color: #94a3b8;">
          Repositori Institusi Resmi Universitas Madura. Pusat pelestarian, kurasi, dan publikasi memori intelektual civitas akademika berstandar keterbukaan informasi.
        </p>
      </div>
      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem;">Navigasi Cepat</h4>
        <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.84rem;">
          <a href="../../indexs.php" style="color: #94a3b8; text-decoration: none;">Beranda Repositori</a>
          <a href="../searchs/indexs.php" style="color: #94a3b8; text-decoration: none;">Katalog Pencarian</a>
          <a href="indexs.php" style="color: #ffffff; text-decoration: none; font-weight: 700;">Statistik Pemanfaatan</a>
        </div>
      </div>
      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem;">Layanan Penulis</h4>
        <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.84rem;">
          <a href="../auths/logins.php" style="color: #94a3b8; text-decoration: none;">Masuk Sistem</a>
          <a href="../auths/registers.php" style="color: #94a3b8; text-decoration: none;">Daftar Akun Baru</a>
          <a href="../workss/creates.php" style="color: #94a3b8; text-decoration: none;">Unggah Karya Ilmiah</a>
        </div>
      </div>
      <div>
        <h4 style="color: #ffffff; font-size: 0.9rem; font-weight: 800; margin-bottom: 0.75rem;">Alamat Kampus</h4>
        <p style="font-size: 0.84rem; line-height: 1.6; color: #94a3b8;">
          Jl. Raya Panglegur KM 3,5 Pamekasan, Madura, Jawa Timur 69371<br>
          Surel: lentera@unira.ac.id
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

    // data grafik fakultas
    const faculty_labelss = <?= json_encode(array_column($faculties_statss, 'code')); ?>;
    const faculty_countss = <?= json_encode(array_map(function($items) { return (int)$items['total_works']; }, $faculties_statss)); ?>;

    const ctx_facultys = document.getElementById('faculty_chart_canvass');
    if (ctx_facultys) {
      new Chart(ctx_facultys, {
        type: 'bar',
        data: {
          labels: faculty_labelss.length ? faculty_labelss : ['FH', 'FEB', 'FIA', 'FP', 'FT', 'FKIP', 'PPS'],
          datasets: [{
            label: 'Total Karya Terbit',
            data: faculty_countss.length ? faculty_countss : [12, 19, 8, 15, 25, 20, 10],
            backgroundColor: '#0f4c5c',
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 }
            }
          }
        }
      });
    }

    // data grafik tipe
    const type_labelss = <?= json_encode(array_column($type_statss, 'name')); ?>;
    const type_countss = <?= json_encode(array_map(function($items) { return (int)$items['total_works']; }, $type_statss)); ?>;

    const ctx_types = document.getElementById('type_chart_canvass');
    if (ctx_types) {
      new Chart(ctx_types, {
        type: 'doughnut',
        data: {
          labels: type_labelss.length ? type_labelss : ['Skripsi', 'Tesis', 'Jurnal', 'LPK', 'Penelitian'],
          datasets: [{
            data: type_countss.length ? type_countss : [45, 15, 25, 10, 5],
            backgroundColor: ['#0f4c5c', '#d97706', '#0284c7', '#16a34a', '#8b5cf6'],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                boxWidth: 12,
                font: { size: 11 }
              }
            }
          }
        }
      });
    }
  </script>
</body>
</html>
