<?php
/**
 * creates.php
 * Formulir unggah naskah karya ilmiah baru oleh penulis
 * Seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/controllerss/work_controllers.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/work_typess.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/study_programss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

// Pastikan sudah masuk ke akun
if (!sessions::hass('user_ids')) {
    flashs::sets_error('Silakan masuk terlebih dahulu untuk mengunggah naskah karya ilmiah.');
    header('Location: ../auths/logins.php');
    exit;
}

$user_ids = sessions::gets('user_ids');
$user_names = sessions::gets('user_names');
$user_prodi_ids = sessions::gets('user_prodi_ids');

$work_type_models = new work_typess();
$work_type_lists = $work_type_models->get_all_actives();

$prodi_models = new study_programss();
$prodi_lists = $prodi_models->get_all_actives();

$error_messages = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_controllers = new work_controllers();
    $submit_results = $work_controllers->stores($_POST, $_FILES);
    if ($submit_results) {
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
  <title>Unggah Naskah Karya Ilmiah — Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers {
      max-width: 900px;
      margin: 2.5rem auto 5rem;
      padding: 0 1.5rem;
    }
    .form-cards {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      padding: 2.5rem;
      box-shadow: 0 10px 30px rgba(15, 76, 92, 0.06);
    }
    .form-headers {
      border-bottom: 1px solid var(--border-colors);
      padding-bottom: 1.5rem;
      margin-bottom: 2rem;
    }
    .form-grid-twos {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
    }
    .form-groups {
      margin-bottom: 1.4rem;
      text-align: left;
    }
    .form-labels {
      display: block;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--text-titles);
      margin-bottom: 0.45rem;
    }
    .form-labels .required-stars {
      color: #ef4444;
      margin-left: 2px;
    }
    .form-inputs, .form-selects, .form-textareas {
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
    .form-inputs:focus, .form-selects:focus, .form-textareas:focus {
      border-color: var(--teal-primarys);
      box-shadow: 0 0 0 3px rgba(15, 76, 92, 0.12);
    }
    .upload-boxes {
      border: 2px dashed #cbd5e1;
      border-radius: var(--radius-mediums);
      padding: 2.25rem 1.5rem;
      text-align: center;
      background: #fafcfe;
      cursor: pointer;
      transition: var(--transition-fasts);
    }
    .upload-boxes:hover {
      border-color: var(--teal-primarys);
      background: #f0fdfa;
    }
    .upload-icons {
      font-size: 2.75rem;
      color: var(--teal-primarys);
      margin-bottom: 0.75rem;
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
    .alert-boxes {
      padding: 0.85rem 1.1rem;
      border-radius: var(--radius-smalls);
      font-size: 0.88rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.6rem;
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #b91c1c;
    }
    @media (max-width: 680px) {
      .page-containers {
        padding: 0 1rem;
        margin: 1.5rem auto 3rem;
      }
      .form-cards {
        padding: 1.75rem 1.25rem;
      }
      .form-grid-twos {
        grid-template-columns: 1fr;
        gap: 0;
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
      <div class="form-headers">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
          <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px; margin: 0;">Unggah Naskah Karya Ilmiah</h1>
            <p style="color: var(--text-bodys); font-size: 0.88rem; margin: 0.35rem 0 0; line-height: 1.5;">Naskah akan melalui proses kurasi editorial dan validasi kelayakan sebelum diterbitkan secara resmi.</p>
          </div>
          <span class="type-badges" style="background: #fef3c7; color: #b45309; font-weight: 700; padding: 0.35rem 0.8rem; border-radius: var(--radius-fulls); font-size: 0.8rem;">
            <i class="fa-solid fa-user-pen"></i> Penulis: <?= htmlspecialchars($user_names); ?>
          </span>
        </div>
      </div>

      <?php if (!empty($error_messages)): ?>
      <div class="alert-boxes">
        <i class="fa-solid fa-circle-exclamation" style="flex-shrink: 0;"></i>
        <span><?= htmlspecialchars($error_messages); ?></span>
      </div>
      <?php endif; ?>

      <form action="creates.php" method="POST" enctype="multipart/form-data">
        <?= csrfs::inputs(); ?>

        <div class="form-groups">
          <label class="form-labels" for="titles">Judul Lengkap Karya Ilmiah <span class="required-stars">*</span></label>
          <input type="text" id="titles" name="titles" class="form-inputs" placeholder="Masukkan judul lengkap naskah karya ilmiah Anda..." value="<?= htmlspecialchars($_POST['titles'] ?? ''); ?>" required autofocus>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="work_type_ids">Jenis Karya Ilmiah <span class="required-stars">*</span></label>
            <select id="work_type_ids" name="work_type_ids" class="form-selects" required>
              <option value="">-- Pilih Jenis Karya --</option>
              <?php foreach ($work_type_lists as $types): ?>
                <option value="<?= htmlspecialchars($types['id']); ?>" <?= (isset($_POST['work_type_ids']) && $_POST['work_type_ids'] == $types['id']) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars("{$types['name']} ({$types['code']})"); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-groups">
            <label class="form-labels" for="prodi_ids">Program Studi Civitas <span class="required-stars">*</span></label>
            <select id="prodi_ids" name="prodi_ids" class="form-selects" required>
              <option value="">-- Pilih Program Studi --</option>
              <?php foreach ($prodi_lists as $prodis): ?>
                <option value="<?= htmlspecialchars($prodis['id']); ?>" <?= ((isset($_POST['prodi_ids']) && $_POST['prodi_ids'] == $prodis['id']) || (!isset($_POST['prodi_ids']) && $user_prodi_ids == $prodis['id'])) ? 'selected' : ''; ?>>
                  <?= htmlspecialchars("{$prodis['name']} ({$prodis['degree']}) — {$prodis['faculty_name']}"); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="years">Tahun Penyelesaian / Terbit <span class="required-stars">*</span></label>
            <input type="number" id="years" name="years" class="form-inputs" min="1990" max="2030" value="<?= htmlspecialchars($_POST['years'] ?? '2026'); ?>" required>
          </div>

          <div class="form-groups">
            <label class="form-labels" for="supervisors">Dosen Pembimbing</label>
            <input type="text" id="supervisors" name="supervisors" class="form-inputs" placeholder="Nama dosen pembimbing lengkap dengan gelar" value="<?= htmlspecialchars($_POST['supervisors'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="keywordss">Kata Kunci (Keywords) <span class="required-stars">*</span></label>
          <input type="text" id="keywordss" name="keywordss" class="form-inputs" placeholder="Contoh: Repositori Digital, MVC, Web, Algoritma Genetika" value="<?= htmlspecialchars($_POST['keywordss'] ?? ''); ?>" required>
          <div style="font-size: 0.78rem; color: var(--text-mutes); margin-top: 0.35rem;">Pisahkan tiap kata kunci dengan tanda koma (,).</div>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="abstracts">Abstrak Naskah Lengkap <span class="required-stars">*</span></label>
          <textarea id="abstracts" name="abstracts" rows="6" class="form-textareas" placeholder="Tuliskan intisari abstrak karya ilmiah (latar belakang, rumusan masalah, metode, dan kesimpulan)..." required><?= htmlspecialchars($_POST['abstracts'] ?? ''); ?></textarea>
        </div>

        <div class="form-groups">
          <label class="form-labels">Berkas Dokumen Lengkap (Format PDF, Maksimal 20 MB) <span class="required-stars">*</span></label>
          <div class="upload-boxes" onclick="document.getElementById('document_pdfs').click();" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ') document.getElementById('document_pdfs').click();">
            <i class="fa-solid fa-cloud-arrow-up upload-icons"></i>
            <p style="font-weight: 700; color: var(--text-titles); margin: 0 0 0.3rem;">Klik untuk memilih berkas dokumen PDF</p>
            <p style="font-size: 0.8rem; color: var(--text-mutes); margin: 0;">Pastikan dokumen naskah utuh dan telah disetujui dosen pembimbing.</p>
            <input type="file" id="document_pdfs" name="document_pdfs" accept=".pdf,application/pdf" style="display: none;" required onchange="handle_file_selects(this)">
            <div id="file_name_displays"></div>
          </div>
        </div>

        <div class="form-actions-bars" style="display: flex; gap: 1rem; justify-content: flex-end; align-items: center; margin-top: 2rem;">
          <a href="indexs.php" class="btn-logins">Batal</a>
          <button type="submit" class="btn-primarys btn-golds" style="padding: 0.8rem 2rem;">
            <i class="fa-solid fa-paper-plane"></i> Kirim untuk Dikurasi
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
