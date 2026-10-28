<?php
/**
 * indexs.php
 * dashboard kurasi karya ilmiah untuk kurator dan super admin
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/controllerss/review_controllers.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/study_programss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

// pastikan peran kurator atau admin
$user_roles = sessions::gets('user_roles');
if (!in_array($user_roles, ['curator', 'admin'])) {
    flashs::sets_error('Akses ditolak. Halaman ini khusus untuk Kurator atau Administrator Repositori.');
    header('Location: ../auths/logins.php');
    exit;
}

$user_names = sessions::gets('user_names');
$user_emails = sessions::gets('user_emails');
$status_filters = $_GET['statuss'] ?? null;
$search_querys = trim($_GET['qs'] ?? '');
$prodi_filters = !empty($_GET['prodis']) ? (int)$_GET['prodis'] : null;

$work_models = new workss();

// proses review jika ada form post aksi kurator
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_controllers = new review_controllers();
    $review_controllers->process_reviews($_POST);
    $redirect_url = "indexs.php";
    $query_parts = [];
    if (!empty($status_filters)) $query_parts[] = "statuss=" . urlencode($status_filters);
    if (!empty($search_querys)) $query_parts[] = "qs=" . urlencode($search_querys);
    if (!empty($prodi_filters)) $query_parts[] = "prodis=" . urlencode($prodi_filters);
    if (!empty($query_parts)) $redirect_url .= "?" . implode("&", $query_parts);
    header("Location: {$redirect_url}");
    exit;
}

// ambil data metrik statistik editorial
$metric_counts = $work_models->get_curator_metric_counts();

// ambil daftar karya terfilter
$curation_workss = $work_models->get_for_curators($status_filters, $search_querys, $prodi_filters);

// ambil daftar program studi untuk dropdown filter
$prodi_models = new study_programss();
$prodi_options = $prodi_models->get_all_actives();

$success_messages = flashs::gets('success');
$error_messages = flashs::gets('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Kurasi & Dewan Editorial — LENTERA Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --bg-dashboard: #f8fafc;
    }
    body {
      background-color: var(--bg-dashboard);
    }
    .page-containers {
      max-width: 1320px;
      margin: 2rem auto 5rem;
      padding: 0 1.5rem;
    }
    .curator-headers {
      background: linear-gradient(135deg, #0f4c5c 0%, #0a333e 100%);
      color: #ffffff;
      border-radius: var(--radius-larges);
      padding: 2.25rem 2rem;
      margin-bottom: 2rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(15, 76, 92, 0.2);
    }
    .curator-headers::after {
      content: '';
      position: absolute;
      right: -20px;
      top: -20px;
      width: 240px;
      height: 240px;
      background: radial-gradient(circle, rgba(251, 139, 36, 0.18) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }
    .metric-grids {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2rem;
    }
    .metric-cards {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-mediums);
      padding: 1.35rem 1.25rem;
      box-shadow: var(--shadow-smalls);
      display: flex;
      align-items: center;
      gap: 1.1rem;
      transition: var(--transition-fasts);
      text-decoration: none;
    }
    .metric-cards:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-mediums);
      border-color: var(--teal-primarys);
    }
    .metric-icons {
      width: 48px;
      height: 48px;
      border-radius: var(--radius-mediums);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }
    .metric-info .metric-numbers {
      font-size: 1.7rem;
      font-weight: 800;
      color: var(--text-titles);
      line-height: 1.1;
    }
    .metric-info .metric-titles {
      font-size: 0.8rem;
      color: var(--text-bodys);
      font-weight: 600;
      margin-top: 0.25rem;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .filter-bars {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      padding: 1.25rem;
      margin-bottom: 1.5rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: center;
      justify-content: space-between;
    }
    .filter-tabs {
      display: flex;
      gap: 0.45rem;
      flex-wrap: wrap;
    }
    .tab-items {
      padding: 0.5rem 1rem;
      border-radius: var(--radius-fulls);
      font-size: 0.84rem;
      font-weight: 700;
      text-decoration: none;
      color: var(--text-bodys);
      background: #f1f5f9;
      border: 1px solid transparent;
      transition: var(--transition-fasts);
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
    }
    .tab-items .tab-badge {
      background: #e2e8f0;
      color: var(--text-titles);
      font-size: 0.72rem;
      padding: 0.15rem 0.45rem;
      border-radius: 10px;
    }
    .tab-items.actives, .tab-items:hover {
      background: var(--teal-primarys);
      color: #ffffff;
    }
    .tab-items.actives .tab-badge {
      background: var(--gold-accents);
      color: #ffffff;
    }
    .table-cards {
      background: #ffffff;
      border-radius: var(--radius-larges);
      overflow: hidden;
      border: 1px solid var(--border-colors);
      box-shadow: var(--shadow-mediums);
    }
    .custom-tables {
      width: 100%;
      border-collapse: collapse;
    }
    .custom-tables th {
      background: #f8fafc;
      padding: 1.1rem 1.25rem;
      font-size: 0.8rem;
      font-weight: 800;
      color: var(--text-titles);
      border-bottom: 2px solid var(--border-colors);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      text-align: left;
    }
    .custom-tables td {
      padding: 1.25rem;
      border-bottom: 1px solid var(--border-colors);
      font-size: 0.9rem;
      vertical-align: top;
    }
    .custom-tables tbody tr:hover {
      background-color: #fafcfe;
    }
    .badge-statuss {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.35rem 0.75rem;
      border-radius: var(--radius-fulls);
      font-size: 0.74rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .status-submitteds { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .status-in_reviews { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
    .status-revisions  { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    .status-waiting_payments { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .status-publisheds { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .status-rejecteds  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    /* Modal Styling */
    .modal-overlays {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(4px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      padding: 1.5rem;
    }
    .modal-cards {
      background: #ffffff;
      border-radius: var(--radius-larges);
      width: 100%;
      max-width: 760px;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
      border: 1px solid var(--border-colors);
      overflow: hidden;
      animation: modalFadeIn 0.25s ease-out;
    }
    @keyframes modalFadeIn {
      from { opacity: 0; transform: scale(0.96); }
      to { opacity: 1; transform: scale(1); }
    }
    .modal-header-bars {
      padding: 1.5rem 1.75rem;
      border-bottom: 1px solid var(--border-colors);
      background: #fafcfe;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .modal-body-scrolls {
      padding: 1.75rem;
      overflow-y: auto;
      flex: 1;
    }
    .modal-tab-navs {
      display: flex;
      border-bottom: 2px solid #f1f5f9;
      margin-bottom: 1.5rem;
      gap: 1.5rem;
    }
    .modal-nav-links {
      padding-bottom: 0.65rem;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--text-mutes);
      border-bottom: 3px solid transparent;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .modal-nav-links.actives {
      color: var(--teal-primarys);
      border-color: var(--teal-primarys);
    }
    .meta-box-panels {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: var(--radius-smalls);
      padding: 1.1rem;
      margin-bottom: 1.25rem;
    }
  </style>
</head>
<body>

  <!-- Top App Navigation -->
  <header style="background: #ffffff; border-bottom: 1px solid var(--border-colors); padding: 0.85rem 0;">
    <div class="page-containers" style="margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
      <a href="../../indexs.php" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
        <div style="width: 38px; height: 38px; background: linear-gradient(135deg, var(--teal-primarys), var(--teal-darks)); border-radius: var(--radius-smalls); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.1rem;">
          <i class="fa-solid fa-fire-flame-curved"></i>
        </div>
        <div>
          <span style="font-size: 1.25rem; font-weight: 800; color: var(--teal-primarys); letter-spacing: -0.5px;">lentera</span>
          <span style="display: block; font-size: 0.68rem; font-weight: 700; color: var(--gold-accents); text-transform: uppercase;">universitas madura</span>
        </div>
      </a>

      <div style="display: flex; gap: 1rem; align-items: center;">
        <div style="text-align: right;">
          <div style="font-size: 0.88rem; font-weight: 700; color: var(--text-titles);"><?= htmlspecialchars($user_names); ?></div>
          <span style="font-size: 0.74rem; background: rgba(15, 76, 92, 0.1); color: var(--teal-primarys); font-weight: 800; padding: 0.15rem 0.5rem; border-radius: var(--radius-fulls); text-transform: uppercase;">
            <i class="fa-solid fa-feather-pointed"></i> <?= htmlspecialchars($user_roles); ?>
          </span>
        </div>
        <a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-globe"></i> Portal Publik</a>
        <a href="../auths/logouts.php" class="btn-logins" style="padding: 0.45rem 1rem; font-size: 0.82rem;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
      </div>
    </div>
  </header>

  <main class="page-containers">
    <!-- Hero / Curator Welcome Banner -->
    <div class="curator-headers">
      <div style="max-width: 800px; position: relative; z-index: 1;">
        <span style="background: rgba(251, 139, 36, 0.25); color: #ffd166; font-size: 0.76rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: var(--radius-fulls); text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-stamp"></i> Editorial Management System
        </span>
        <h1 style="font-size: 1.9rem; font-weight: 800; margin-top: 0.6rem; letter-spacing: -0.5px;">Portal Kurasi & Dewan Editorial</h1>
        <p style="color: #e2e8f0; font-size: 0.95rem; margin-top: 0.4rem; line-height: 1.5;">
          Periksa orisinalitas naskah, telaah format sitasi dan metodologi ilmiah, lalu berikan keputusan penerbitan untuk repositori resmi Universitas Madura.
        </p>
      </div>
    </div>

    <?php if (!empty($success_messages)): ?>
      <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 1rem 1.25rem; border-radius: var(--radius-smalls); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fa-solid fa-circle-check" style="font-size: 1.2rem;"></i>
        <span style="font-weight: 600;"><?= htmlspecialchars($success_messages); ?></span>
      </div>
    <?php endif; ?>

    <?php if (!empty($error_messages)): ?>
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem 1.25rem; border-radius: var(--radius-smalls); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 1.2rem;"></i>
        <span style="font-weight: 600;"><?= htmlspecialchars($error_messages); ?></span>
      </div>
    <?php endif; ?>

    <!-- Editorial Stat Cards (OJS Alur Metrik) -->
    <div class="metric-grids">
      <a href="indexs.php" class="metric-cards">
        <div class="metric-icons" style="background: #e0f2fe; color: #0284c7;">
          <i class="fa-solid fa-folder-open"></i>
        </div>
        <div class="metric-info">
          <div class="metric-numbers"><?= $metric_counts['total']; ?></div>
          <div class="metric-titles">Total Naskah</div>
        </div>
      </a>

      <a href="indexs.php?statuss=submitted" class="metric-cards">
        <div class="metric-icons" style="background: #fef3c7; color: #d97706;">
          <i class="fa-solid fa-inbox"></i>
        </div>
        <div class="metric-info">
          <div class="metric-numbers"><?= $metric_counts['submitted']; ?></div>
          <div class="metric-titles">Perlu Telaah (Submitted)</div>
        </div>
      </a>

      <a href="indexs.php?statuss=in_review" class="metric-cards">
        <div class="metric-icons" style="background: #e0e7ff; color: #4f46e5;">
          <i class="fa-solid fa-glasses"></i>
        </div>
        <div class="metric-info">
          <div class="metric-numbers"><?= $metric_counts['in_review']; ?></div>
          <div class="metric-titles">Sedang Ditelaah</div>
        </div>
      </a>

      <a href="indexs.php?statuss=revision" class="metric-cards">
        <div class="metric-icons" style="background: #ffedd5; color: #ea580c;">
          <i class="fa-solid fa-rotate"></i>
        </div>
        <div class="metric-info">
          <div class="metric-numbers"><?= $metric_counts['revision']; ?></div>
          <div class="metric-titles">Menunggu Revisi</div>
        </div>
      </a>

      <a href="indexs.php?statuss=waiting_payment" class="metric-cards">
        <div class="metric-icons" style="background: #fef3c7; color: #b45309;">
          <i class="fa-solid fa-qrcode"></i>
        </div>
        <div class="metric-info">
          <div class="metric-numbers"><?= $metric_counts['waiting_payment']; ?></div>
          <div class="metric-titles">Menunggu Bayar (APC)</div>
        </div>
      </a>

      <a href="indexs.php?statuss=published" class="metric-cards">
        <div class="metric-icons" style="background: #dcfce7; color: #16a34a;">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="metric-info">
          <div class="metric-numbers"><?= $metric_counts['published']; ?></div>
          <div class="metric-titles">Resmi Terbit</div>
        </div>
      </a>
    </div>

    <!-- Filter and Search Bar -->
    <div class="filter-bars">
      <div class="filter-tabs">
        <a href="indexs.php<?= $search_querys ? '?qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= empty($status_filters) ? 'actives' : ''; ?>">
          <span>Semua</span>
          <span class="tab-badge"><?= $metric_counts['total']; ?></span>
        </a>
        <a href="indexs.php?statuss=submitted<?= $search_querys ? '&qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= $status_filters === 'submitted' ? 'actives' : ''; ?>">
          <span>Perlu Telaah</span>
          <span class="tab-badge"><?= $metric_counts['submitted']; ?></span>
        </a>
        <a href="indexs.php?statuss=in_review<?= $search_querys ? '&qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= $status_filters === 'in_review' ? 'actives' : ''; ?>">
          <span>Dalam Telaah</span>
          <span class="tab-badge"><?= $metric_counts['in_review']; ?></span>
        </a>
        <a href="indexs.php?statuss=revision<?= $search_querys ? '&qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= $status_filters === 'revision' ? 'actives' : ''; ?>">
          <span>Revisi</span>
          <span class="tab-badge"><?= $metric_counts['revision']; ?></span>
        </a>
        <a href="indexs.php?statuss=waiting_payment<?= $search_querys ? '&qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= $status_filters === 'waiting_payment' ? 'actives' : ''; ?>">
          <span>Menunggu Bayar (APC)</span>
          <span class="tab-badge"><?= $metric_counts['waiting_payment']; ?></span>
        </a>
        <a href="indexs.php?statuss=published<?= $search_querys ? '&qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= $status_filters === 'published' ? 'actives' : ''; ?>">
          <span>Terbit</span>
          <span class="tab-badge"><?= $metric_counts['published']; ?></span>
        </a>
        <a href="indexs.php?statuss=rejected<?= $search_querys ? '&qs=' . urlencode($search_querys) : ''; ?>" class="tab-items <?= $status_filters === 'rejected' ? 'actives' : ''; ?>">
          <span>Ditolak</span>
          <span class="tab-badge"><?= $metric_counts['rejected']; ?></span>
        </a>
      </div>

      <!-- Quick Search & Prodi Filter Form -->
      <form action="indexs.php" method="GET" style="display: flex; gap: 0.6rem; align-items: center;">
        <?php if (!empty($status_filters)): ?>
          <input type="hidden" name="statuss" value="<?= htmlspecialchars($status_filters); ?>">
        <?php endif; ?>

        <div style="position: relative;">
          <input type="text" name="qs" placeholder="Cari judul atau nama penulis..." value="<?= htmlspecialchars($search_querys); ?>" style="padding: 0.45rem 0.85rem 0.45rem 2rem; font-size: 0.85rem; border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); outline: none; width: 220px;">
          <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-mutes); font-size: 0.8rem;"></i>
        </div>

        <button type="submit" class="btn-primarys" style="padding: 0.45rem 0.9rem; font-size: 0.85rem;">
          Filter
        </button>
        <?php if (!empty($search_querys) || !empty($status_filters)): ?>
          <a href="indexs.php" class="btn-logins" style="padding: 0.45rem 0.75rem; font-size: 0.82rem;" title="Reset Filter">
            <i class="fa-solid fa-xmark"></i>
          </a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Editorial Curation Queue Table -->
    <div class="table-cards">
      <?php if (empty($curation_workss)): ?>
      <div style="text-align: center; padding: 4.5rem 1.5rem;">
        <i class="fa-solid fa-clipboard-check" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1.25rem;"></i>
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles);">Tidak Ada Naskah Dalam Antrean Ini</h3>
        <p style="color: var(--text-bodys); font-size: 0.9rem; max-width: 480px; margin: 0.5rem auto 1.5rem;">
          Semua naskah pada status filter ini telah selesai diproses atau belum ada naskah baru yang masuk.
        </p>
        <a href="indexs.php" class="btn-primarys" style="font-size: 0.88rem;">Tampilkan Semua Naskah</a>
      </div>
      <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="custom-tables">
          <thead>
            <tr>
              <th style="width: 70px;">ID</th>
              <th>Judul Naskah Ilmiah</th>
              <th>Penulis & Program Studi</th>
              <th>Status Editorial</th>
              <th>Berkas Naskah (Galley)</th>
              <th style="text-align: right;">Aksi Kurasi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($curation_workss as $works): ?>
            <tr>
              <td>
                <span style="font-weight: 800; color: var(--teal-primarys); font-size: 0.82rem; background: #f0fdfa; padding: 0.2rem 0.45rem; border-radius: 4px; border: 1px solid #ccfbf1;">
                  #<?= str_pad($works['id'], 5, '0', STR_PAD_LEFT); ?>
                </span>
              </td>
              <td style="max-width: 380px;">
                <div style="font-weight: 800; color: var(--text-titles); font-size: 0.94rem; line-height: 1.45; margin-bottom: 0.35rem;">
                  <?= htmlspecialchars($works['title']); ?>
                </div>
                <div style="display: flex; gap: 0.4rem; align-items: center; flex-wrap: wrap;">
                  <span class="type-badges" style="font-size: 0.7rem;"><?= htmlspecialchars($works['type_name']); ?></span>
                  <span style="font-size: 0.76rem; color: var(--text-mutes);">
                    <i class="fa-regular fa-calendar"></i> Tahun <?= htmlspecialchars($works['year']); ?>
                  </span>
                  <?php if (!empty($works['published_at'])): ?>
                    <span style="font-size: 0.74rem; color: #16a34a; font-weight: 700;">
                      <i class="fa-solid fa-check"></i> Terbit: <?= date('d M Y', strtotime($works['published_at'])); ?>
                    </span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div style="font-weight: 700; color: var(--text-titles); font-size: 0.92rem;"><?= htmlspecialchars($works['author_name']); ?></div>
                <div style="font-size: 0.8rem; color: var(--text-mutes); margin-top: 0.15rem;">
                  NIM/NIP: <span style="font-family: monospace; font-weight: 600;"><?= htmlspecialchars($works['author_nim_nip'] ?? '-'); ?></span>
                </div>
                <div style="font-size: 0.8rem; color: var(--teal-primarys); font-weight: 600; margin-top: 0.15rem;">
                  <?= htmlspecialchars($works['prodi_name']); ?>
                </div>
              </td>
              <td>
                <span class="badge-statuss status-<?= htmlspecialchars($works['status']); ?>s">
                  <?php if ($works['status'] === 'submitted'): ?>
                    <i class="fa-solid fa-clock-rotate-left"></i> Masuk (Submitted)
                  <?php elseif ($works['status'] === 'in_review'): ?>
                    <i class="fa-solid fa-glasses"></i> Ditelaah (In Review)
                  <?php elseif ($works['status'] === 'revision'): ?>
                    <i class="fa-solid fa-rotate"></i> Butuh Revisi
                  <?php elseif ($works['status'] === 'waiting_payment'): ?>
                    <i class="fa-solid fa-qrcode"></i> Menunggu Bayar (APC)
                  <?php elseif ($works['status'] === 'published'): ?>
                    <i class="fa-solid fa-circle-check"></i> Diterbitkan
                  <?php elseif ($works['status'] === 'rejected'): ?>
                    <i class="fa-solid fa-circle-xmark"></i> Ditolak
                  <?php else: ?>
                    <?= htmlspecialchars($works['status']); ?>
                  <?php endif; ?>
                </span>
              </td>
              <td>
                <?php if (!empty($works['file_path'])): ?>
                  <a href="../workss/downloads.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; background: #e0f2fe; color: #0369a1; box-shadow: none; border: 1px solid #bae6fd;">
                    <i class="fa-solid fa-file-pdf"></i> Galley PDF (v<?= htmlspecialchars($works['file_version'] ?? 1); ?>)
                  </a>
                <?php else: ?>
                  <span style="color: var(--text-mutes); font-size: 0.8rem; font-style: italic;">Belum ada berkas</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <button type="button" class="btn-primarys btn-golds" style="padding: 0.45rem 0.95rem; font-size: 0.84rem;" onclick='open_curation_workspace(<?= json_encode([
                  "id"          => $works["id"],
                  "title"       => $works["title"],
                  "abstract"    => $works["abstract"] ?? "Belum ada abstrak.",
                  "author_name" => $works["author_name"],
                  "nim_nip"     => $works["author_nim_nip"] ?? "-",
                  "prodi_name"  => $works["prodi_name"],
                  "faculty"     => $works["faculty_name"] ?? "Universitas Madura",
                  "type_name"   => $works["type_name"],
                  "status"      => $works["status"],
                  "file_version"=> $works["file_version"] ?? 1,
                  "has_file"    => !empty($works["file_path"])
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                  <i class="fa-solid fa-gavel"></i> Workspace Kurasi
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- MODAL WORKSPACE KURASI & KEPUTUSAN EDITORIAL (OJS SUBMISSION STYLE) -->
  <div id="curation_modal" class="modal-overlays">
    <div class="modal-cards">
      <div class="modal-header-bars">
        <div>
          <span id="modal_type_pill" style="font-size: 0.72rem; font-weight: 800; background: var(--teal-primarys); color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; text-transform: uppercase;">
            Naskah Masuk
          </span>
          <h3 id="modal_display_title" style="font-size: 1.15rem; font-weight: 800; color: var(--text-titles); margin-top: 0.4rem; line-height: 1.4;">
            Judul Naskah
          </h3>
          <div style="font-size: 0.82rem; color: var(--text-bodys); margin-top: 0.25rem;">
            Penulis: <strong id="modal_author_label"></strong> (<span id="modal_prodi_label"></span>)
          </div>
        </div>
        <button type="button" onclick="close_curation_modal()" style="background: none; border: none; font-size: 1.4rem; color: var(--text-mutes); cursor: pointer; padding: 0.2rem;">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Tab Navigasi Modal -->
      <div style="padding: 1rem 1.75rem 0; background: #fafcfe; border-bottom: 1px solid var(--border-colors);">
        <div class="modal-tab-navs">
          <div class="modal-nav-links actives" id="tab_btn_decision" onclick="switch_modal_tab('decision')">
            <i class="fa-solid fa-gavel"></i> 1. Keputusan Editorial
          </div>
          <div class="modal-nav-links" id="tab_btn_metadata" onclick="switch_modal_tab('metadata')">
            <i class="fa-solid fa-file-lines"></i> 2. Metadata & Abstrak
          </div>
          <div class="modal-nav-links" id="tab_btn_files" onclick="switch_modal_tab('files')">
            <i class="fa-solid fa-paperclip"></i> 3. Berkas & Alur Naskah
          </div>
        </div>
      </div>

      <div class="modal-body-scrolls">
        <!-- SUB-TAB 1: FORMULIR KEPUTUSAN EDITORIAL -->
        <div id="subtab_decision">
          <form action="indexs.php<?= $status_filters ? '?statuss=' . urlencode($status_filters) : ''; ?>" method="POST">
            <?= csrfs::inputs(); ?>
            <input type="hidden" id="modal_input_work_id" name="work_ids" value="">

            <div style="margin-bottom: 1.25rem;">
              <label style="display: block; font-weight: 700; font-size: 0.9rem; color: var(--text-titles); margin-bottom: 0.5rem;">
                Keputusan Kurator / Dewan Editor <span style="color: #ef4444;">*</span>
              </label>
              <select name="actions" id="modal_select_action" required style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border-colors); border-radius: var(--radius-smalls); font-size: 0.92rem; outline: none;">
                <option value="">-- Pilih Keputusan Dewan Editorial --</option>
                <option value="acc_payment">💳 ACC Kelayakan Naskah (Minta Penulis Bayar APC & Auto-Publish)</option>
                <option value="approve">✅ Setujui & Terbitkan Langsung (Bypass Pembayaran)</option>
                <option value="in_review">🔍 Tetapkan Status Dalam Penelaahan (In Review)</option>
                <option value="revision">🔄 Minta Penulis Melakukan Revisi (Request Revision)</option>
                <option value="reject">❌ Tolak Naskah (Decline Submission)</option>
              </select>
              <div style="font-size: 0.8rem; color: var(--text-mutes); margin-top: 0.35rem;">
                Pilih <strong>ACC Kelayakan</strong> agar penulis menerima notifikasi tagihan APC dan naskah otomatis terbit seketika pembayaran dikonfirmasi.
              </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
              <label style="display: block; font-weight: 700; font-size: 0.9rem; color: var(--text-titles); margin-bottom: 0.5rem;">
                Catatan Telaah Kurator (Peer Review Feedback / Editorial Notes) <span style="color: #ef4444;">*</span>
              </label>
              <textarea name="comments" id="modal_textarea_comments" rows="5" required style="width: 100%; padding: 0.85rem; border: 1.5px solid var(--border-colors); border-radius: var(--radius-smalls); font-family: inherit; font-size: 0.92rem; line-height: 1.5; outline: none;" placeholder="Tuliskan evaluasi metodologi, catatan format IEEE/APA, perbaikan yang diperlukan, atau apresiasi kelayakan naskah..."></textarea>
            </div>

            <div style="display: flex; gap: 0.85rem; justify-content: flex-end; align-items: center; border-top: 1px solid var(--border-colors); padding-top: 1.25rem;">
              <button type="button" class="btn-logins" onclick="close_curation_modal()">Batal</button>
              <button type="submit" class="btn-primarys btn-golds" style="padding: 0.75rem 1.4rem;">
                <i class="fa-solid fa-check-double"></i> Simpan & Terapkan Keputusan
              </button>
            </div>
          </form>
        </div>

        <!-- SUB-TAB 2: METADATA & ABSTRAK -->
        <div id="subtab_metadata" style="display: none;">
          <div class="meta-box-panels">
            <h4 style="font-size: 0.86rem; font-weight: 800; color: var(--teal-primarys); text-transform: uppercase; margin-bottom: 0.5rem;">
              <i class="fa-solid fa-align-left"></i> Abstrak Penelitian
            </h4>
            <p id="modal_abstract_text" style="font-size: 0.92rem; line-height: 1.6; color: var(--text-titles); margin: 0; text-align: justify;"></p>
          </div>

          <div class="meta-box-panels">
            <h4 style="font-size: 0.86rem; font-weight: 800; color: var(--teal-primarys); text-transform: uppercase; margin-bottom: 0.5rem;">
              <i class="fa-solid fa-building-columns"></i> Departemen & Fakultas
            </h4>
            <div style="font-size: 0.9rem; color: var(--text-titles);">
              Fakultas: <strong id="modal_faculty_text"></strong><br>
              Program Studi: <strong id="modal_prodi_text"></strong>
            </div>
          </div>
        </div>

        <!-- SUB-TAB 3: BERKAS & ALUR NASKAH -->
        <div id="subtab_files" style="display: none;">
          <div class="meta-box-panels">
            <h4 style="font-size: 0.86rem; font-weight: 800; color: var(--teal-primarys); text-transform: uppercase; margin-bottom: 0.75rem;">
              <i class="fa-solid fa-file-pdf"></i> Dokumen Naskah Ilmiah (Galley)
            </h4>
            <div id="modal_download_container">
              <!-- rendered via javascript -->
            </div>
          </div>

          <div class="meta-box-panels">
            <h4 style="font-size: 0.86rem; font-weight: 800; color: var(--teal-primarys); text-transform: uppercase; margin-bottom: 0.5rem;">
              <i class="fa-solid fa-shield-halved"></i> Standar Kurasi LENTERA Universitas Madura
            </h4>
            <ul style="font-size: 0.85rem; color: var(--text-bodys); line-height: 1.6; padding-left: 1.25rem; margin: 0;">
              <li>Pemeriksaan tingkat kemiripan naskah (Similarity index maks. 25%).</li>
              <li>Kesesuaian struktur naskah (Judul, Abstrak bilingual, Metodologi, Pembahasan, Kesimpulan).</li>
              <li>Validitas sumber rujukan primer (minimal 80% jurnal ilmiah 5 tahun terakhir).</li>
              <li>Format sitasi baku (APA 7th Edition atau IEEE Standard).</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function open_curation_workspace(data) {
      document.getElementById('modal_input_work_id').value = data.id;
      document.getElementById('modal_type_pill').textContent = data.type_name;
      document.getElementById('modal_display_title').textContent = data.title;
      document.getElementById('modal_author_label').textContent = data.author_name;
      document.getElementById('modal_prodi_label').textContent = data.prodi_name;
      document.getElementById('modal_abstract_text').textContent = data.abstract;
      document.getElementById('modal_faculty_text').textContent = data.faculty;
      document.getElementById('modal_prodi_text').textContent = data.prodi_name;

      // file download link
      const dl_box = document.getElementById('modal_download_container');
      if (data.has_file) {
        dl_box.innerHTML = `
          <div style="display: flex; align-items: center; justify-content: space-between; background: #ffffff; padding: 0.85rem 1rem; border: 1px solid var(--border-colors); border-radius: var(--radius-smalls);">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
              <i class="fa-solid fa-file-pdf" style="font-size: 1.8rem; color: #dc2626;"></i>
              <div>
                <strong style="color: var(--text-titles); font-size: 0.9rem;">Naskah_Ilmiah_${data.id}_v${data.file_version}.pdf</strong>
                <div style="font-size: 0.75rem; color: var(--text-mutes);">Versi ${data.file_version} • Format Dokumen PDF Lengkap</div>
              </div>
            </div>
            <a href="../workss/downloads.php?ids=${data.id}" class="btn-primarys" style="padding: 0.45rem 0.95rem; font-size: 0.82rem;">
              <i class="fa-solid fa-download"></i> Unduh Berkas
            </a>
          </div>
        `;
      } else {
        dl_box.innerHTML = '<p style="color: var(--text-mutes); font-size: 0.85rem;">Tidak ada dokumen terlampir pada naskah ini.</p>';
      }

      // reset tab to decision
      switch_modal_tab('decision');

      // show modal
      document.getElementById('curation_modal').style.display = 'flex';
    }

    function close_curation_modal() {
      document.getElementById('curation_modal').style.display = 'none';
    }

    function switch_modal_tab(tab_name) {
      document.getElementById('subtab_decision').style.display = tab_name === 'decision' ? 'block' : 'none';
      document.getElementById('subtab_metadata').style.display = tab_name === 'metadata' ? 'block' : 'none';
      document.getElementById('subtab_files').style.display = tab_name === 'files' ? 'block' : 'none';

      document.getElementById('tab_btn_decision').className = 'modal-nav-links' + (tab_name === 'decision' ? ' actives' : '');
      document.getElementById('tab_btn_metadata').className = 'modal-nav-links' + (tab_name === 'metadata' ? ' actives' : '');
      document.getElementById('tab_btn_files').className = 'modal-nav-links' + (tab_name === 'files' ? ' actives' : '');
    }

    // close on clicking backdrop
    window.addEventListener('click', function(e) {
      const modal = document.getElementById('curation_modal');
      if (e.target === modal) {
        close_curation_modal();
      }
    });
  </script>
</body>
</html>