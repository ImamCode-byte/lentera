<?php
/**
 * revises.php
 * formulir unggah revisi dokumen karya ilmiah
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/controllerss/work_controllers.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

if (!sessions::hass('user_ids')) {
    header('Location: ../auths/logins.php');
    exit;
}

$work_ids = (int)($_GET['ids'] ?? 0);
$work_models = new workss();
$works = $work_models->find_by_ids($work_ids);

if (!$works || $works['author_id'] != sessions::gets('user_ids')) {
    flashs::sets_error('karya tidak ditemukan atau anda tidak memiliki akses.');
    header('Location: indexs.php');
    exit;
}

$error_messages = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_controllers = new work_controllers();
    $revision_results = $work_controllers->process_revisions($work_ids, $_POST, $_FILES);
    if ($revision_results) {
        header('Location: indexs.php');
        exit;
    }
    $error_messages = flashs::gets('error');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unggah Revisi Karya — LENTERAS</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers { max-width: 800px; margin: 3rem auto; padding: 0 1.5rem; }
    .form-cards { background: #ffffff; border-radius: var(--radius-larges); padding: 2.5rem; border: 1px solid var(--border-colors); box-shadow: var(--shadow-mediums); }
    .curator-note-banners { background: #fff7ed; border-left: 4px solid #f97316; padding: 1.25rem; border-radius: var(--radius-smalls); margin-bottom: 2rem; }
  </style>
</head>
<body>
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div><span class="logo-texts">lenteras</span><span class="logo-subtexts">universitas madura</span></div>
      </a>
      <a href="indexs.php" class="btn-logins" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">Kembali ke Dashboard</a>
    </div>
  </header>

  <main class="page-containers">
    <div class="form-cards">
      <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-titles); margin-bottom: 0.5rem;">Unggah Berkas Revisi</h1>
      <p style="color: var(--text-bodys); font-size: 0.9rem; margin-bottom: 1.5rem;">
        Karya: <strong style="color: var(--teal-primarys);"><?= htmlspecialchars($works['title']); ?></strong>
      </p>

      <?php if (!empty($works['reviewss'])): ?>
      <div class="curator-note-banners">
        <h4 style="color: #c2410c; font-size: 0.95rem; margin-bottom: 0.5rem;">
          <i class="fa-solid fa-clipboard-list"></i> Catatan Terakhir dari Kurator (<?= htmlspecialchars($works['reviewss'][0]['curator_name']); ?>):
        </h4>
        <p style="font-size: 0.9rem; color: #7c2d12; line-height: 1.6;">
          <?= nl2br(htmlspecialchars($works['reviewss'][0]['comment'])); ?>
        </p>
      </div>
      <?php endif; ?>

      <?php if (!empty($error_messages)): ?>
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.85rem; border-radius: var(--radius-smalls); margin-bottom: 1.5rem;">
        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error_messages); ?>
      </div>
      <?php endif; ?>

      <form action="revises.php?ids=<?= $work_ids; ?>" method="POST" enctype="multipart/form-data">
        <?= csrfs::inputs(); ?>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Pilih Dokumen PDF Perbaikan (Maksimal 20 MB) *</label>
          <input type="file" name="document_pdfs" accept=".pdf,application/pdf" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-colors); border-radius: var(--radius-smalls);">
        </div>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-weight: 700; margin-bottom: 0.5rem;">Catatan Tanggapan Penulis (Opsional)</label>
          <textarea name="revision_notess" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-colors); border-radius: var(--radius-smalls);" placeholder="Jelaskan bagian apa saja yang telah Anda perbaiki sesuai masukan kurator..."></textarea>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
          <a href="indexs.php" class="btn-logins">Batal</a>
          <button type="submit" class="btn-primarys btn-golds">
            <i class="fa-solid fa-arrow-up-from-bracket"></i> Kirim Revisi
          </button>
        </div>
      </form>
    </div>
  </main>
</body>
</html>
