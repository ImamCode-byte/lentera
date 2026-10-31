<?php
/**
 * indexs.php
 * dashboard manajemen dan pemantauan status karya ilmiah penulis
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';

sessions::starts();

if (!sessions::hass('user_ids')) {
    flashs::sets_error('Silakan masuk terlebih dahulu untuk mengakses dashboard penulis.');
    header('Location: ../auths/logins.php');
    exit;
}

$author_ids = (int)sessions::gets('user_ids');
$user_names = sessions::gets('user_names');
$user_roles = sessions::gets('user_roles');

$work_models = new workss();
$author_workss = $work_models->get_by_authors($author_ids);

// Hitung metrik naskah penulis
$cnt_total = count($author_workss);
$cnt_in_review = 0;
$cnt_revision = 0;
$cnt_payment = 0;
$cnt_published = 0;

foreach ($author_workss as $aw) {
    $st = $aw['status'] ?? '';
    if ($st === 'submitted' || $st === 'in_review') $cnt_in_review++;
    elseif ($st === 'revision') $cnt_revision++;
    elseif ($st === 'waiting_payment') $cnt_payment++;
    elseif ($st === 'published') $cnt_published++;
}

$success_messages = flashs::gets('success');
$error_messages = flashs::gets('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Karya Saya — Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers {
      max-width: 1200px;
      margin: 2rem auto 5rem;
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
    .curator-note-boxes {
      margin-top: 0.65rem;
      background: #fff7ed;
      border-left: 3px solid #f97316;
      padding: 0.55rem 0.8rem;
      border-radius: 4px;
      font-size: 0.8rem;
      color: #9a3412;
      line-height: 1.5;
    }
  </style>
</head>
<body>

  <!-- Navigasi Terpadu -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">Lentera</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>

      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 0.88rem; font-weight: 700; color: var(--teal-primarys); display: flex; align-items: center; gap: 0.4rem;">
          <i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($user_names); ?>
        </span>
        <?php if (in_array($user_roles, ['curator', 'admin'])): ?>
          <a href="../reviews/indexs.php" class="btn-primarys" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">
            <i class="fa-solid fa-feather-pointed"></i> Kurator
          </a>
        <?php endif; ?>
        <?php if ($user_roles === 'admin'): ?>
          <a href="../admins/indexs.php" class="btn-primarys" style="padding: 0.4rem 0.85rem; font-size: 0.8rem; background: #831843;">
            <i class="fa-solid fa-crown"></i> Admin
          </a>
        <?php endif; ?>
        <a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-house"></i> Beranda</a>
        <a href="../auths/logouts.php" class="btn-logins" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;" title="Keluar">
          <i class="fa-solid fa-right-from-bracket"></i>
        </a>
        <button type="button" class="nav-mobile-toggles" onclick="toggle_mobile_drawer()" aria-label="Menu navigasi">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile_drawer" class="nav-mobile-drawers">
      <a href="../../indexs.php"><i class="fa-solid fa-house"></i> Beranda Repositori</a>
      <a href="../searchs/indexs.php"><i class="fa-solid fa-magnifying-glass"></i> Katalog Pencarian</a>
      <a href="indexs.php" style="color: var(--teal-primarys);"><i class="fa-solid fa-folder-open"></i> Dashboard Karya Saya</a>
      <a href="creates.php" style="color: var(--gold-accents);"><i class="fa-solid fa-plus"></i> Unggah Karya Baru</a>
      <?php if (in_array($user_roles, ['curator', 'admin'])): ?>
        <a href="../reviews/indexs.php"><i class="fa-solid fa-feather-pointed"></i> Panel Kurator</a>
      <?php endif; ?>
      <?php if ($user_roles === 'admin'): ?>
        <a href="../admins/indexs.php"><i class="fa-solid fa-crown"></i> Panel Admin Repositori</a>
      <?php endif; ?>
      <a href="../auths/logouts.php" style="color: #b91c1c;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
    </div>
  </header>

  <main class="page-containers">
    <div class="dashboard-headers">
      <div>
        <span style="font-size: 0.78rem; font-weight: 800; color: var(--gold-accents); text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-id-badge"></i> Ruang Kerja Penulis
        </span>
        <h1 style="font-size: 1.85rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px; margin-top: 0.25rem;">
          Dashboard Karya Saya
        </h1>
        <p style="color: var(--text-bodys); font-size: 0.92rem; margin-top: 0.25rem;">
          Pantau status naskah ilmiah Anda, periksa catatan telaah kurator, selesaikan tagihan publikasi, dan unggah revisi.
        </p>
      </div>
      <a href="creates.php" class="btn-primarys btn-golds" style="padding: 0.65rem 1.4rem;">
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

    <!-- Kartu Metrik Penulis -->
    <div class="metric-grids">
      <div class="metric-cards">
        <div class="metric-icons" style="background: var(--teal-lights); color: var(--teal-primarys);">
          <i class="fa-solid fa-book"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= $cnt_total; ?></div>
          <div class="metric-titles">Total Karya Diajukan</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #e0f2fe; color: #0284c7;">
          <i class="fa-solid fa-feather-pointed"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= $cnt_in_review; ?></div>
          <div class="metric-titles">Dalam Telaah Kurator</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #ffedd5; color: #c2410c;">
          <i class="fa-solid fa-pen-ruler"></i>
        </div>
        <div>
          <div class="metric-numbers" style="color: <?= $cnt_revision > 0 ? '#c2410c' : 'inherit'; ?>;"><?= $cnt_revision; ?></div>
          <div class="metric-titles">Perlu Tindakan Revisi</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #fef3c7; color: #b45309;">
          <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
          <div class="metric-numbers" style="color: <?= $cnt_payment > 0 ? '#b45309' : 'inherit'; ?>;"><?= $cnt_payment; ?></div>
          <div class="metric-titles">Menunggu Bayar APC</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #dcfce7; color: #15803d;">
          <i class="fa-solid fa-check-double"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= $cnt_published; ?></div>
          <div class="metric-titles">Resmi Terbit Publik</div>
        </div>
      </div>
    </div>

    <!-- Tabel Daftar Karya -->
    <div class="table-cards">
      <?php if (empty($author_workss)): ?>
        <div class="empty-state-boxes">
          <div class="empty-state-icons"><i class="fa-solid fa-folder-open"></i></div>
          <h3 class="empty-state-titles">Belum Ada Karya yang Diunggah</h3>
          <p class="empty-state-descriptions">
            Mulai publikasikan skripsi, tesis, artikel jurnal, atau laporan penelitian Anda untuk diarsipkan secara digital di LENTERA Universitas Madura.
          </p>
          <a href="creates.php" class="btn-primarys btn-golds" style="padding: 0.65rem 1.5rem;">
            <i class="fa-solid fa-plus"></i> Unggah Naskah Pertama Anda
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsives">
          <table class="custom-tables">
            <thead>
              <tr>
                <th>Judul Naskah</th>
                <th>Jenis & Prodi</th>
                <th>Status Kurasi & Publikasi</th>
                <th>Versi Berkas</th>
                <th>Statistik Pembaca</th>
                <th style="text-align: right; min-width: 140px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($author_workss as $works): ?>
              <tr>
                <td style="max-width: 320px;">
                  <strong style="color: var(--text-titles); font-size: 0.95rem; display: block; line-height: 1.4;">
                    <?= htmlspecialchars($works['title']); ?>
                  </strong>
                  <span style="font-size: 0.76rem; color: var(--text-mutes); margin-top: 0.35rem; display: block;">
                    Diajukan: <?= date('d M Y, H:i', strtotime($works['created_at'])); ?> WIB
                  </span>

                  <?php if (!empty($works['last_review_comments']) && in_array($works['status'], ['revision', 'rejected'])): ?>
                    <div class="curator-note-boxes">
                      <strong>Catatan Kurator:</strong> <?= htmlspecialchars($works['last_review_comments']); ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($works['status'] === 'waiting_payment' && ($works['payment_status'] ?? '') === 'rejected' && !empty($works['payment_admin_notes'])): ?>
                    <div style="margin-top: 0.5rem; background: #fef2f2; border-left: 3px solid #ef4444; padding: 0.45rem 0.65rem; border-radius: 4px; font-size: 0.78rem; color: #991b1b;">
                      <strong>Catatan Penolakan Admin:</strong> <?= htmlspecialchars($works['payment_admin_notes']); ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="type-badges"><?= htmlspecialchars($works['type_name']); ?></span>
                  <div style="font-size: 0.8rem; color: var(--text-bodys); margin-top: 0.35rem; font-weight: 600;">
                    <?= htmlspecialchars($works['prodi_name']); ?> (<?= htmlspecialchars($works['year']); ?>)
                  </div>
                </td>
                <td>
                  <?php if ($works['status'] === 'waiting_payment'): ?>
                    <?php if (($works['payment_status'] ?? '') === 'pending' && !empty($works['payment_proof_file'])): ?>
                      <span class="badge-statuss" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">
                        <i class="fa-solid fa-hourglass-half"></i> Verifikasi Admin
                      </span>
                      <div style="font-size: 0.74rem; color: var(--text-mutes); margin-top: 0.25rem;">
                        Struk pembayaran dikirim
                      </div>
                    <?php elseif (($works['payment_status'] ?? '') === 'rejected'): ?>
                      <span class="badge-statuss" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Bukti Ditolak
                      </span>
                      <div style="font-size: 0.74rem; color: #b91c1c; margin-top: 0.25rem;">
                        Unggah bukti sah
                      </div>
                    <?php else: ?>
                      <span class="badge-statuss status-waiting_payments">
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> ACC — Bayar APC
                      </span>
                      <div style="font-size: 0.74rem; color: #b45309; margin-top: 0.25rem;">
                        Menunggu pelunasan
                      </div>
                    <?php endif; ?>
                  <?php else: ?>
                    <?php
                      $badge_class = 'status-' . htmlspecialchars($works['status']) . 's';
                      $status_labels = [
                        'submitted'        => 'Menunggu Kurator',
                        'in_review'        => 'Sedang Diperiksa',
                        'revision'         => 'Perlu Revisi',
                        'published'        => 'Diterbitkan',
                        'rejected'         => 'Ditolak',
                        'draft'            => 'Draf',
                      ];
                    ?>
                    <span class="badge-statuss <?= $badge_class; ?>">
                      <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i>
                      <?= $status_labels[$works['status']] ?? htmlspecialchars($works['status']); ?>
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <span style="font-weight: 700; color: var(--teal-primarys);">v<?= htmlspecialchars($works['file_version'] ?? 1); ?></span>
                  <div style="font-size: 0.78rem; color: var(--text-mutes);"><?= htmlspecialchars($works['original_name'] ?? 'dokumen.pdf'); ?></div>
                </td>
                <td style="font-size: 0.85rem; color: var(--text-bodys);">
                  <div><i class="fa-regular fa-eye" style="color: var(--text-mutes);"></i> <?= number_format($works['view_count']); ?> dilihat</div>
                  <div style="margin-top: 0.2rem;"><i class="fa-solid fa-download" style="color: #15803d;"></i> <?= number_format($works['download_count']); ?> diunduh</div>
                </td>
                <td style="text-align: right;">
                  <div style="display: flex; flex-direction: column; gap: 0.35rem; align-items: flex-end;">
                    <?php if ($works['status'] === 'waiting_payment'): ?>
                      <?php if (($works['payment_status'] ?? '') === 'pending' && !empty($works['payment_proof_file'])): ?>
                        <a href="payments.php?ids=<?= $works['id']; ?>" class="btn-primarys" style="min-height: 32px; padding: 0.35rem 0.75rem; font-size: 0.78rem; background: #0284c7; color: #fff;">
                          <i class="fa-solid fa-receipt"></i> Cek Pembayaran
                        </a>
                      <?php elseif (($works['payment_status'] ?? '') === 'rejected'): ?>
                        <a href="payments.php?ids=<?= $works['id']; ?>" class="btn-primarys btn-golds" style="min-height: 32px; padding: 0.35rem 0.75rem; font-size: 0.78rem; background: #dc2626; color: #fff;">
                          <i class="fa-solid fa-arrow-up-from-bracket"></i> Unggah Ulang
                        </a>
                      <?php else: ?>
                        <a href="payments.php?ids=<?= $works['id']; ?>" class="btn-primarys btn-golds" style="min-height: 32px; padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                          <i class="fa-solid fa-qrcode"></i> Bayar APC
                        </a>
                      <?php endif; ?>
                    <?php elseif ($works['status'] === 'revision'): ?>
                      <a href="revises.php?ids=<?= $works['id']; ?>" class="btn-primarys btn-golds" style="min-height: 32px; padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> Unggah Revisi
                      </a>
                    <?php endif; ?>

                    <a href="details.php?ids=<?= $works['id']; ?>" class="btn-logins" style="min-height: 32px; padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                      <i class="fa-solid fa-circle-info"></i> Detail
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

  <script>
    function toggle_mobile_drawer() {
      const drawer = document.getElementById('mobile_drawer');
      if (drawer) drawer.classList.toggle('opens');
    }
  </script>
</body>
</html>
