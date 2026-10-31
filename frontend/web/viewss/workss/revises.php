<?php
/**
 * revises.php
 * Formulir unggah revisi dokumen karya ilmiah oleh penulis
 * Seluruh fungsi dan variabel konsisten lowercase dan akhiran s
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
    flashs::sets_error('Karya tidak ditemukan atau Anda tidak memiliki akses.');
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
  <title>Unggah Revisi Karya — Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers {
      max-width: 840px;
      margin: 2.5rem auto 5rem;
      padding: 0 1.5rem;
    }
    .form-cards {
      background: #ffffff;
      border-radius: var(--radius-larges);
      padding: 2.5rem;
      border: 1px solid var(--border-colors);
      box-shadow: 0 10px 30px rgba(15, 76, 92, 0.06);
    }
    .curator-note-banners {
      background: #fff7ed;
      border-left: 4px solid #f97316;
      padding: 1.25rem 1.4rem;
      border-radius: var(--radius-smalls);
      margin-bottom: 2rem;
    }
    .upload-boxes {
      border: 2px dashed #cbd5e1;
      border-radius: var(--radius-mediums);
      padding: 2rem 1.5rem;
      text-align: center;
      background: #fafcfe;
      cursor: pointer;
      transition: var(--transition-fasts);
    }
    .upload-boxes:hover {
      border-color: var(--teal-primarys);
      background: #f0fdfa;
    }
    .file-namess {
      margin-top: 0.85rem;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--teal-primarys);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #f0fdfa;
      padding: 0.35rem 0.85rem;
      border-radius: var(--radius-smalls);
      border: 1px solid #ccfbf1;
    }
    .form-inputs, .form-textareas {
      width: 100%;
      padding: 0.78rem 1rem;
      font-size: 0.92rem;
      font-family: inherit;
      border: 1.5px solid var(--border-colors);
      border-radius: var(--radius-smalls);
      outline: none;
      transition: var(--transition-fasts);
      background-color: #ffffff;
      color: var(--text-titles);
      box-sizing: border-box;
    }
    .form-inputs:focus, .form-textareas:focus {
      border-color: var(--teal-primarys);
      box-shadow: 0 0 0 3px rgba(15, 76, 92, 0.12);
    }
    @media (max-width: 680px) {
      .page-containers {
        padding: 0 1rem;
        margin: 1.5rem auto 3rem;
      }
      .form-cards {
        padding: 1.75rem 1.25rem;
      }
      .form-actions-bars {
        flex-direction: column-reverse;
      }
      .form-actions-bars .btn-primarys,
      .form-actions-bars .btn-logins {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
    }
  </style>
</head>
<body>

  <!-- Navigasi Atas Responsif -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="../../indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">Lentera</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>

      <!-- Desktop Nav -->
      <nav class="nav-links-wrappers" style="display: flex; align-items: center; gap: 1rem;">
        <a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-globe"></i> Beranda</a>
        <a href="indexs.php" class="nav-items-links"><i class="fa-solid fa-table-list"></i> Karya Saya</a>
        <a href="../auths/logouts.php" class="btn-logins" style="padding: 0.45rem 1rem; font-size: 0.84rem;">
          <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
      </nav>

      <!-- Mobile Hamburger Button -->
      <button type="button" class="nav-mobile-toggles" onclick="toggle_mobile_drawer()" aria-label="Buka Menu Navigasi">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="nav-mobile-drawers" id="mobile_drawer">
      <a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-globe"></i> Beranda Utama</a>
      <a href="indexs.php" class="nav-items-links"><i class="fa-solid fa-table-list"></i> Karya Saya</a>
      <a href="../auths/logouts.php" class="btn-logins" style="display: inline-flex; justify-content: center; margin-top: 0.5rem;">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar Akun
      </a>
    </div>
  </header>

  <main class="page-containers">
    <div class="form-cards">
      <div style="border-bottom: 1px solid var(--border-colors); padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; font-weight: 800; background: #fff7ed; color: #c2410c; padding: 0.25rem 0.65rem; border-radius: var(--radius-fulls); text-transform: uppercase; margin-bottom: 0.5rem;">
          <i class="fa-solid fa-rotate"></i> Permintaan Perbaikan Naskah
        </div>
        <h1 style="font-size: 1.55rem; font-weight: 800; color: var(--text-titles); margin: 0 0 0.35rem; letter-spacing: -0.4px;">
          Unggah Berkas Revisi Karya Ilmiah
        </h1>
        <p style="color: var(--text-bodys); font-size: 0.88rem; margin: 0; line-height: 1.5;">
          Judul Karya: <strong style="color: var(--teal-primarys);"><?= htmlspecialchars($works['title']); ?></strong>
        </p>
      </div>

      <?php if (!empty($works['reviewss'])): ?>
      <div class="curator-note-banners">
        <h4 style="color: #c2410c; font-size: 0.92rem; font-weight: 800; margin: 0 0 0.45rem;">
          <i class="fa-solid fa-clipboard-list"></i> Catatan Evaluasi Kurator (<?= htmlspecialchars($works['reviewss'][0]['curator_name']); ?>):
        </h4>
        <p style="font-size: 0.88rem; color: #7c2d12; line-height: 1.6; margin: 0;">
          <?= nl2br(htmlspecialchars($works['reviewss'][0]['comment'])); ?>
        </p>
      </div>
      <?php endif; ?>

      <?php if (!empty($error_messages)): ?>
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.85rem 1.1rem; border-radius: var(--radius-smalls); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
        <i class="fa-solid fa-circle-exclamation" style="flex-shrink: 0;"></i>
        <span><?= htmlspecialchars($error_messages); ?></span>
      </div>
      <?php endif; ?>

      <form action="revises.php?ids=<?= $work_ids; ?>" method="POST" enctype="multipart/form-data">
        <?= csrfs::inputs(); ?>

        <div style="margin-bottom: 1.5rem;">
          <label style="display: block; font-weight: 700; font-size: 0.88rem; color: var(--text-titles); margin-bottom: 0.5rem;">
            Pilih Dokumen PDF Perbaikan (Maksimal 20 MB) <span style="color: #ef4444;">*</span>
          </label>
          <div class="upload-boxes" onclick="document.getElementById('document_pdfs').click();" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') document.getElementById('document_pdfs').click();">
            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.5rem; color: var(--teal-primarys); margin-bottom: 0.6rem;"></i>
            <p style="font-weight: 700; color: var(--text-titles); margin: 0 0 0.25rem;">Klik untuk memilih berkas dokumen PDF perbaikan</p>
            <p style="font-size: 0.8rem; color: var(--text-mutes); margin: 0;">Versi dokumen akan otomatis di-update ke versi berikutnya.</p>
            <input type="file" id="document_pdfs" name="document_pdfs" accept=".pdf,application/pdf" required style="display: none;" onchange="handle_file_selects(this)">
            <div id="file_name_displays"></div>
          </div>
        </div>

        <div style="margin-bottom: 1.75rem;">
          <label style="display: block; font-weight: 700; font-size: 0.88rem; color: var(--text-titles); margin-bottom: 0.5rem;">
            Catatan Tanggapan Penulis (Author Response / Correction Notes)
          </label>
          <textarea name="revision_notess" rows="4" class="form-textareas" placeholder="Jelaskan bagian apa saja yang telah Anda perbaiki sesuai masukan kurator di atas (misal: penambahan metodologi, perbaikan sitasi IEEE, dsb.)..."></textarea>
          <div style="font-size: 0.78rem; color: var(--text-mutes); margin-top: 0.35rem;">Catatan ini akan langsung dibaca oleh kurator saat menelaah ulang naskah Anda.</div>
        </div>

        <div class="form-actions-bars" style="display: flex; gap: 1rem; justify-content: flex-end; align-items: center; border-top: 1px solid var(--border-colors); padding-top: 1.5rem;">
          <a href="indexs.php" class="btn-logins">Batal</a>
          <button type="submit" class="btn-primarys btn-golds" style="padding: 0.8rem 2rem;">
            <i class="fa-solid fa-arrow-up-from-bracket"></i> Kirim Revisi Naskah
          </button>
        </div>
      </form>
    </div>
  </main>

  <script>
    function toggle_mobile_drawer() {
      const drawer = document.getElementById('mobile_drawer');
      if (drawer) {
        drawer.classList.toggle('opens');
      }
    }

    function handle_file_selects(inputs) {
      const display_elements = document.getElementById('file_name_displays');
      if (inputs.files && inputs.files[0]) {
        const files = inputs.files[0];
        const size_in_mbs = (files.size / (1024 * 1024)).toFixed(2);
        display_elements.innerHTML = `<span class="file-namess"><i class="fa-solid fa-file-pdf"></i> Terpilih: ${files.name} (${size_in_mbs} MB)</span>`;
      } else {
        display_elements.innerHTML = '';
      }
    }
  </script>
</body>
</html>
