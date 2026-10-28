<?php
/**
 * creates.php
 * formulir unggah naskah karya ilmiah baru oleh penulis
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/controllerss/work_controllers.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/work_typess.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/study_programss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

// pastikan sudah masuk ke akun
if (!sessions::hass('user_ids')) {
    flashs::sets_error('silakan masuk terlebih dahulu untuk mengunggah naskah karya ilmiah.');
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
  <title>Unggah Naskah Karya Ilmiah — LENTERAS Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
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
      box-shadow: var(--shadow-mediums);
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
      margin-bottom: 1.5rem;
    }
    .form-labels {
      display: block;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--text-titles);
      margin-bottom: 0.5rem;
    }
    .form-inputs, .form-selects, .form-textareas {
      width: 100%;
      padding: 0.8rem 1rem;
      font-size: 0.95rem;
      font-family: inherit;
      border: 1.5px solid var(--border-colors);
      border-radius: var(--radius-smalls);
      outline: none;
      transition: var(--transition-fasts);
      background-color: #ffffff;
    }
    .form-inputs:focus, .form-selects:focus, .form-textareas:focus {
      border-color: var(--teal-primarys);
      box-shadow: 0 0 0 3px rgba(15, 76, 92, 0.12);
    }
    .upload-boxes {
      border: 2px dashed var(--border-colors);
      border-radius: var(--radius-mediums);
      padding: 2rem;
      text-align: center;
      background: var(--bg-mains);
      cursor: pointer;
      transition: var(--transition-fasts);
    }
    .upload-boxes:hover {
      border-color: var(--teal-primarys);
      background: var(--teal-lights);
    }
    .upload-icons {
      font-size: 2.5rem;
      color: var(--teal-primarys);
      margin-bottom: 0.75rem;
    }
    .file-namess {
      margin-top: 0.75rem;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--teal-primarys);
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
    @media (max-width: 600px) {
      .form-grid-twos { grid-template-columns: 1fr; gap: 0; }
    }
  </style>
</head>
<body>

  <!-- navigasi atas ringkas -->
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
        <a href="indexs.php" class="nav-items-links"><i class="fa-solid fa-table-list"></i> Karya Saya</a>
        <a href="../auths/logouts.php" class="btn-logins" style="padding: 0.4rem 0.9rem; font-size: 0.82rem;">Keluar</a>
      </div>
    </div>
  </header>

  <main class="page-containers">
    <div class="form-cards">
      <div class="form-headers">
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--text-titles); letter-spacing: -0.5px;">Unggah Naskah Karya Ilmiah</h1>
            <p style="color: var(--text-bodys); font-size: 0.9rem; margin-top: 0.25rem;">Naskah akan melalui proses kurasi sebelum dipublikasikan secara resmi.</p>
          </div>
          <span class="type-badges" style="background: var(--gold-lights); color: #c46809;">Penulis: <?= htmlspecialchars($user_names); ?></span>
        </div>
      </div>

      <?php if (!empty($error_messages)): ?>
      <div class="alert-boxes">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars($error_messages); ?></span>
      </div>
      <?php endif; ?>

      <form action="creates.php" method="POST" enctype="multipart/form-data">
        <?= csrfs::inputs(); ?>

        <div class="form-groups">
          <label class="form-labels" for="titles">Judul Lengkap Karya Ilmiah *</label>
          <input type="text" id="titles" name="titles" class="form-inputs" placeholder="Masukkan judul lengkap karya ilmiah..." value="<?= htmlspecialchars($_POST['titles'] ?? ''); ?>" required autofocus>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="work_type_ids">Jenis Karya Ilmiah *</label>
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
            <label class="form-labels" for="prodi_ids">Program Studi *</label>
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
            <label class="form-labels" for="years">Tahun Penyelesaian *</label>
            <input type="number" id="years" name="years" class="form-inputs" min="1990" max="2030" value="<?= htmlspecialchars($_POST['years'] ?? '2026'); ?>" required>
          </div>

          <div class="form-groups">
            <label class="form-labels" for="supervisors">Dosen Pembimbing</label>
            <input type="text" id="supervisors" name="supervisors" class="form-inputs" placeholder="Nama dosen pembimbing beserta gelar" value="<?= htmlspecialchars($_POST['supervisors'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="keywordss">Kata Kunci (Pisahkan dengan tanda koma) *</label>
          <input type="text" id="keywordss" name="keywordss" class="form-inputs" placeholder="Contoh: Repositori Digital, MVC, Web, PHP" value="<?= htmlspecialchars($_POST['keywordss'] ?? ''); ?>" required>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="abstracts">Abstrak Naskah Lengkap *</label>
          <textarea id="abstracts" name="abstracts" rows="6" class="form-textareas" placeholder="Tuliskan intisari abstrak karya ilmiah (latar belakang, metodologi, hasil)..." required><?= htmlspecialchars($_POST['abstracts'] ?? ''); ?></textarea>
        </div>

        <div class="form-groups">
          <label class="form-labels">Dokumen Naskah Lengkap (Format PDF, Maksimal 20 MB) *</label>
          <div class="upload-boxes" onclick="document.getElementById('document_pdfs').click();">
            <i class="fa-solid fa-cloud-arrow-up upload-icons"></i>
            <p style="font-weight: 700; color: var(--text-titles); margin-bottom: 0.25rem;">Klik untuk memilih berkas dokumen PDF</p>
            <p style="font-size: 0.8rem; color: var(--text-mutes);">Pastikan dokumen naskah telah disetujui pembimbing.</p>
            <input type="file" id="document_pdfs" name="document_pdfs" accept=".pdf,application/pdf" style="display: none;" required onchange="handle_file_selects(this)">
            <div id="file_name_displays" class="file-namess"></div>
          </div>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
          <a href="indexs.php" class="btn-logins">Batal</a>
          <button type="submit" class="btn-primarys btn-golds" style="padding: 0.75rem 2rem;">
            <i class="fa-solid fa-paper-plane"></i> Kirim untuk Dikurasi
          </button>
        </div>
      </form>
    </div>
  </main>

  <script>
    function handle_file_selects(inputs) {
      const display_elements = document.getElementById('file_name_displays');
      if (inputs.files && inputs.files[0]) {
        const files = inputs.files[0];
        const size_in_mbs = (files.size / (1024 * 1024)).toFixed(2);
        display_elements.innerHTML = `<i class="fa-solid fa-file-pdf"></i> Terpilih: ${files.name} (${size_in_mbs} MB)`;
      } else {
        display_elements.innerHTML = '';
      }
    }
  </script>
</body>
</html>
