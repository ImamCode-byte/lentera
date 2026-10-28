<?php
/**
 * indexs.php
 * dashboard manajemen dan pemantauan status karya ilmiah penulis
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backends/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backends/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backends/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backends/apps/helperss/flashs.php';

sessions::starts();

if (!sessions::hass('user_ids')) {
    flashs::sets_error('silakan masuk terlebih dahulu.');
    header('Location: ../auths/logins.php');
    exit;
}

$author_ids = (int)sessions::gets('user_ids');
$user_names = sessions::gets('user_names');
$user_roles = sessions::gets('user_roles');

$work_models = new workss();
$author_workss = $work_models->get_by_authors($author_ids);

$success_messages = flashs::gets('success');
$error_messages = flashs::gets('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Karya Saya — LENTERAS Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers {
      max-width: 1140px;
      margin: 2.5rem auto 5rem;
      padding: 0 1.5rem;
    }
    .dashboard-headers {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .table-cards {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      overflow: hidden;
      box-shadow: var(--shadow-mediums);
    }
    .custom-tables {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }
    .custom-tables th {
      background: #f8fafc;
      padding: 1rem 1.25rem;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--text-titles);
      border-bottom: 1px solid var(--border-colors);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .custom-tables td {
      padding: 1.25rem;
      border-bottom: 1px solid var(--border-colors);
      font-size: 0.92rem;
      vertical-align: top;
    }
    .custom-tables tr:last-child td {
      border-bottom: none;
    }
    .custom-tables tr:hover {
      background: #fdfdfd;
    }
    .badge-statuss {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.35rem 0.85rem;
      border-radius: var(--radius-fulls);
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
    }
    .status-submitteds { background: #e0f2fe; color: #0284c7; }
    .status-in_reviews { background: #fef9c3; color: #a16207; }
    .status-revisions  { background: #ffedd5; color: #c2410c; }
    .status-waiting_payments { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .status-publisheds { background: #dcfce7; color: #15803d; }
    .status-rejecteds  { background: #fee2e2; color: #b91c1c; }
    .status-drafts     { background: #f1f5f9; color: #64748b; }
    
    .curator-note-boxes {
      margin-top: 0.75rem;
      background: #fff7ed;
      border-left: 3px solid #f97316;
      padding: 0.6rem 0.85rem;
      border-radius: 4px;
      font-size: 0.82rem;
      color: #9a3412;
    }
    .alert-boxes {
      padding: 0.85rem 1.1rem;
      border-radius: var(--radius-smalls);
      font-size: 0.88rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }
    .alert-successs { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .alert-errors   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
  </style>
</head>
<body>

  <!-- navigasi atas -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">lenteras</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="font-size: 0.9rem; font-weight: 700; color: var(--teal-primarys);">
          <i class="fa-solid fa-user-pen"></i> <?= htmlspecialchars($user_names); ?>
        </span>
        <?php if (in_array($user_roles, ['curator', 'admin'])): ?>
          <a href="../reviews/indexs.php" class="btn-primarys" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">Antrean Kurator</a>
        <?php endif; ?>
        <a href="../../indexs.php" class="nav-items-links">Beranda</a>
        <a href="../auths/logouts.php" class="btn-logins" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">Keluar</a>
      </div>
    </div>
  </header>

  <main class="page-containers">
    <div class="dashboard-headers">
      <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px;">Dashboard Karya Saya</h1>
        <p style="color: var(--text-bodys); font-size: 0.92rem;">Kelola naskah ilmiah, pantau alur verifikasi kurator, dan unggah revisi.</p>
      </div>
      <a href="creates.php" class="btn-primarys btn-golds" style="padding: 0.75rem 1.4rem;">
        <i class="fa-solid fa-plus"></i> Unggah Karya Baru
      </a>
    </div>

    <?php if (!empty($success_messages)): ?>
    <div class="alert-boxes alert-successs">
      <i class="fa-solid fa-circle-check"></i>
      <span><?= htmlspecialchars($success_messages); ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($error_messages)): ?>
    <div class="alert-boxes alert-errors">
      <i class="fa-solid fa-circle-exclamation"></i>
      <span><?= htmlspecialchars($error_messages); ?></span>
    </div>
    <?php endif; ?>

    <div class="table-cards">
      <?php if (empty($author_workss)): ?>
      <div style="text-align: center; padding: 4rem 1.5rem;">
        <div style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="fa-solid fa-folder-open"></i></div>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-titles); margin-bottom: 0.5rem;">Belum Ada Karya yang Diunggah</h3>
        <p style="color: var(--text-bodys); font-size: 0.9rem; margin-bottom: 1.5rem;">Mulai publikasikan skripsi, tesis, atau artikel jurnal Anda sekarang.</p>
        <a href="creates.php" class="btn-primarys"><i class="fa-solid fa-plus"></i> Unggah Naskah Pertama</a>
      </div>
      <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="custom-tables">
          <thead>
            <tr>
              <th>Judul Naskah</th>
              <th>Jenis & Prodi</th>
              <th>Status Kurasi</th>
              <th>Versi Dokumen</th>
              <th>Statistik</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($author_workss as $works): ?>
            <tr>
              <td style="max-width: 320px;">
                <strong style="color: var(--text-titles); font-size: 0.98rem; display: block; line-height: 1.4;">
                  <?= htmlspecialchars($works['title']); ?>
                </strong>
                <span style="font-size: 0.78rem; color: var(--text-mutes); margin-top: 0.35rem; display: block;">
                  Diajukan: <?= date('d M Y, H:i', strtotime($works['created_at'])); ?> WIB
                </span>
                <?php if (!empty($works['last_review_comments']) && in_array($works['status'], ['revision', 'rejected'])): ?>
                <div class="curator-note-boxes">
                  <strong>Catatan Kurator:</strong> <?= htmlspecialchars($works['last_review_comments']); ?>
                </div>
                <?php endif; ?>
              </td>
              <td>
                <span class="type-badges" style="font-size: 0.72rem;"><?= htmlspecialchars($works['type_name']); ?></span>
                <div style="font-size: 0.8rem; color: var(--text-bodys); margin-top: 0.3rem;">
                  <?= htmlspecialchars($works['prodi_name']); ?> (<?= htmlspecialchars($works['year']); ?>)
                </div>
              </td>
              <td>
                <?php
                  $badge_class = 'status-' . htmlspecialchars($works['status']) . 's';
                  $status_labels = [
                    'submitted'        => 'Menunggu Kurator',
                    'in_review'        => 'Sedang Diperiksa',
                    'revision'         => 'Perlu Revisi',
                    'waiting_payment'  => 'ACC — Bayar APC',
                    'published'        => 'Diterbitkan',
                    'rejected'         => 'Ditolak',
                    'draft'            => 'Draf',
                  ];
                ?>
                <span class="badge-statuss <?= $badge_class; ?>">
                  <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i>
                  <?= $status_labels[$works['status']] ?? htmlspecialchars($works['status']); ?>
                </span>
              </td>
              <td>
                <span style="font-weight: 700; color: var(--teal-primarys);">v<?= htmlspecialchars($works['file_version'] ?? 1); ?></span>
                <div style="font-size: 0.78rem; color: var(--text-mutes);"><?= htmlspecialchars($works['original_name'] ?? 'dokumen.pdf'); ?></div>
              </td>
              <td style="font-size: 0.85rem; color: var(--text-bodys);">
                <div><i class="fa-regular fa-eye"></i> <?= number_format($works['view_count']); ?> dibaca</div>
                <div style="margin-top: 0.2rem;"><i class="fa-solid fa-download"></i> <?= number_format($works['download_count']); ?> diunduh</div>
              </td>
              <td>
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                  <?php if ($works['status'] === 'waiting_payment'): ?>
                    <a href="payments.php?ids=<?= $works['id']; ?>" class="btn-primarys btn-golds" style="padding: 0.45rem 0.85rem; font-size: 0.8rem; font-weight: 800; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.3); text-align: center;">
                      <i class="fa-solid fa-qrcode"></i> Bayar & Terbitkan
                    </a>
                  <?php elseif ($works['status'] === 'revision'): ?>
                    <a href="revises.php?ids=<?= $works['id']; ?>" class="btn-primarys btn-golds" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; text-align: center;">
                      <i class="fa-solid fa-arrow-up-from-bracket"></i> Unggah Revisi
                    </a>
                  <?php endif; ?>

                  <a href="details.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; background: var(--teal-lights); color: var(--teal-primarys); box-shadow: none; text-align: center;">
                    <i class="fa-solid fa-circle-info"></i> Pratinjau
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
