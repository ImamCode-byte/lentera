<?php
/**
 * registers.php
 * Halaman registrasi penulis dan civitas akademika repositori Lentera Universitas Madura
 * Seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/controllerss/auth_controllers.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/study_programss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

// Jika sudah login, alihkan ke beranda
if (sessions::hass('user_ids')) {
    header('Location: ../../indexs.php');
    exit;
}

$error_messages = '';

// Ambil daftar prodi dari database dan kelompokkan berdasarkan fakultas
$grouped_prodis = [];
try {
    $db_conns = databases::get_connections();
    $sql_prodis = "
        SELECT sp.id, sp.name AS prodi_name, sp.degree, f.name AS faculty_name, f.code AS faculty_code
        FROM study_programs sp
        JOIN faculties f ON sp.faculty_id = f.id
        WHERE sp.is_active = 1
        ORDER BY f.id ASC, sp.name ASC
    ";
    $stmt_prodis = $db_conns->query($sql_prodis);
    $all_prodis = $stmt_prodis->fetchAll(PDO::FETCH_ASSOC);

    foreach ($all_prodis as $item) {
        $grouped_prodis[$item['faculty_name']][] = $item;
    }
} catch (Exception $exceptions) {
    $grouped_prodis = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth_controllers = new auth_controllers();
    $register_results = $auth_controllers->process_registers($_POST);
    if ($register_results) {
        header('Location: logins.php');
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
  <title>Registrasi Akun Baru — Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .auth-containers {
      min-height: 100vh;
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem 1.25rem;
      background: radial-gradient(circle at 10% 20%, rgba(15, 76, 92, 0.08) 0%, transparent 45%),
                  radial-gradient(circle at 90% 80%, rgba(217, 119, 6, 0.08) 0%, transparent 45%),
                  var(--surface-canvass, #f8fafc);
    }
    .auth-cards {
      width: 100%;
      max-width: 680px;
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      padding: 2.75rem 2.5rem;
      box-shadow: 0 12px 36px rgba(15, 76, 92, 0.08);
    }
    .auth-headers {
      text-align: center;
      margin-bottom: 2.25rem;
    }
    .auth-logo-icons {
      width: 52px;
      height: 52px;
      margin: 0 auto 0.85rem;
      background: linear-gradient(135deg, var(--teal-primarys), var(--teal-darks));
      border-radius: var(--radius-mediums);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      font-size: 1.55rem;
      box-shadow: 0 6px 16px rgba(15, 76, 92, 0.22);
    }
    .section-titles {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.95rem;
      font-weight: 800;
      color: var(--teal-primarys);
      border-bottom: 2px solid #f1f5f9;
      padding-bottom: 0.5rem;
      margin-top: 1.75rem;
      margin-bottom: 1.25rem;
    }
    .section-titles i {
      color: var(--gold-accents);
    }
    .form-grid-twos {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.1rem;
    }
    .form-groups {
      margin-bottom: 1.25rem;
      text-align: left;
    }
    .form-labels {
      display: block;
      font-size: 0.86rem;
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
    .password-wrappers {
      position: relative;
    }
    .password-toggles {
      position: absolute;
      right: 0.5rem;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: var(--text-mutes);
      cursor: pointer;
      width: 44px;
      height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 4px;
    }
    .password-toggles:hover {
      color: var(--text-titles);
    }
    .form-hints {
      font-size: 0.78rem;
      color: var(--text-mutes);
      margin-top: 0.35rem;
      line-height: 1.4;
    }
    .checkbox-cards {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: var(--radius-smalls);
      padding: 0.9rem 1.1rem;
      margin-bottom: 0.85rem;
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      transition: var(--transition-fasts);
      cursor: pointer;
    }
    .checkbox-cards:hover {
      border-color: var(--teal-primarys);
      background: #f0fdfa;
    }
    .checkbox-cards input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-top: 2px;
      accent-color: var(--teal-primarys);
      cursor: pointer;
    }
    .checkbox-labels {
      font-size: 0.86rem;
      color: var(--text-titles);
      line-height: 1.45;
      cursor: pointer;
      font-weight: 500;
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
      line-height: 1.45;
    }
    .auth-footers {
      text-align: center;
      margin-top: 2.25rem;
      font-size: 0.9rem;
      color: var(--text-bodys);
    }
    .auth-links {
      color: var(--teal-primarys);
      font-weight: 700;
      text-decoration: none;
    }
    .auth-links:hover {
      text-decoration: underline;
    }
    @media (max-width: 650px) {
      .auth-cards {
        padding: 2rem 1.25rem;
        border-radius: var(--radius-mediums);
      }
      .form-grid-twos {
        grid-template-columns: 1fr;
        gap: 0;
      }
    }
  </style>
</head>
<body>

  <div class="auth-containers">
    <div class="auth-cards">
      <div class="auth-headers">
        <a href="../../indexs.php" style="text-decoration: none; display: inline-block;">
          <div class="auth-logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <h1 style="font-size: 1.65rem; font-weight: 800; color: var(--teal-primarys); letter-spacing: -0.5px; margin: 0;">Lentera</h1>
          <p style="font-size: 0.76rem; color: var(--gold-accents); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin: 0.2rem 0 0;">universitas madura</p>
        </a>
        <h2 style="font-size: 1.25rem; font-weight: 800; margin-top: 1.35rem; margin-bottom: 0.25rem; color: var(--text-titles);">Pendaftaran Akun Peneliti & Penulis</h2>
        <p style="font-size: 0.86rem; color: var(--text-bodys); margin: 0;">Lengkapi data diri akademik Anda untuk memulai pengajuan karya ilmiah di Lentera</p>
      </div>

      <?php if (!empty($error_messages)): ?>
      <div class="alert-boxes">
        <i class="fa-solid fa-circle-exclamation" style="flex-shrink: 0;"></i>
        <span><?= htmlspecialchars($error_messages); ?></span>
      </div>
      <?php endif; ?>

      <form action="registers.php" method="POST" id="form_registers">
        <?= csrfs::inputs(); ?>

        <!-- BAGIAN 1: IDENTITAS CIVITAS AKADEMIKA -->
        <div class="section-titles">
          <i class="fa-solid fa-graduation-cap"></i>
          <span>1. Profil & Identitas Civitas Akademika</span>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="names">Nama Lengkap & Gelar Akademik <span class="required-stars">*</span></label>
          <input type="text" id="names" name="names" class="form-inputs" placeholder="Contoh: Achmad Fauzi, S.Kom. atau Imam Hambali" value="<?= htmlspecialchars($_POST['names'] ?? ''); ?>" required autofocus>
          <div class="form-hints">Nama akan dicantumkan secara resmi pada berkas sitasi dan metadata publikasi repositori.</div>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="usernames">Nama Pengguna (Username) <span class="required-stars">*</span></label>
            <input type="text" id="usernames" name="usernames" class="form-inputs" placeholder="Contoh: imam_hambali_001" value="<?= htmlspecialchars($_POST['usernames'] ?? ''); ?>" required>
            <div class="form-hints">Huruf kecil, angka, atau garis bawah (3-60 karakter).</div>
          </div>
          <div class="form-groups">
            <label class="form-labels" for="nim_nips">Nomor Induk (NIM / NIP / NIDN) <span class="required-stars">*</span></label>
            <input type="text" id="nim_nips" name="nim_nips" class="form-inputs" placeholder="Contoh: 2022510001 atau 19920815..." value="<?= htmlspecialchars($_POST['nim_nips'] ?? ''); ?>" required>
            <div class="form-hints">Verifikasi status civitas akademika Universitas Madura.</div>
          </div>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="prodi_ids">Fakultas & Program Studi <span class="required-stars">*</span></label>
          <select id="prodi_ids" name="prodi_ids" class="form-selects" required>
            <option value="">-- Pilih Program Studi Anda --</option>
            <?php foreach ($grouped_prodis as $faculty_name => $prodi_items): ?>
              <optgroup label="FAKULTAS: <?= htmlspecialchars($faculty_name); ?>">
                <?php foreach ($prodi_items as $prodis): ?>
                  <option value="<?= htmlspecialchars($prodis['id']); ?>" <?= (isset($_POST['prodi_ids']) && $_POST['prodi_ids'] == $prodis['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars("{$prodis['prodi_name']} ({$prodis['degree']})"); ?>
                  </option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="affiliations">Afiliasi / Institusi Asal</label>
            <input type="text" id="affiliations" name="affiliations" class="form-inputs" value="<?= htmlspecialchars($_POST['affiliations'] ?? 'Universitas Madura'); ?>">
          </div>
          <div class="form-groups">
            <label class="form-labels" for="countries">Negara Asal</label>
            <input type="text" id="countries" name="countries" class="form-inputs" value="<?= htmlspecialchars($_POST['countries'] ?? 'Indonesia'); ?>">
          </div>
        </div>

        <!-- BAGIAN 2: KONTAK & IDENTITAS RISET -->
        <div class="section-titles">
          <i class="fa-solid fa-microscope"></i>
          <span>2. Kontak & Minat Riset Peneliti</span>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="emails">Alamat Surel (Email) Aktif <span class="required-stars">*</span></label>
            <input type="email" id="emails" name="emails" class="form-inputs" placeholder="nama@unira.ac.id atau surel aktif" value="<?= htmlspecialchars($_POST['emails'] ?? ''); ?>" required>
            <div class="form-hints">Digunakan untuk konfirmasi publikasi dan korespondensi kurator.</div>
          </div>
          <div class="form-groups">
            <label class="form-labels" for="phones">Nomor Kontak / WhatsApp</label>
            <input type="tel" id="phones" name="phones" class="form-inputs" placeholder="Contoh: 081234567890" value="<?= htmlspecialchars($_POST['phones'] ?? ''); ?>">
            <div class="form-hints">Untuk pemberitahuan status naskah cepat.</div>
          </div>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="orcid_ids">ORCID iD (Opsional)</label>
          <input type="text" id="orcid_ids" name="orcid_ids" class="form-inputs" placeholder="https://orcid.org/0009-0004-xxxx-xxxx" value="<?= htmlspecialchars($_POST['orcid_ids'] ?? ''); ?>">
          <div class="form-hints">Identifikasi peneliti internasional untuk indeksasi publikasi.</div>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="bios">Minat Riset & Bidang Keahlian Ilmiah (Reviewing Interests)</label>
          <textarea id="bios" name="bios" rows="2" class="form-textareas" placeholder="Contoh: Jaringan Komputer, Mobile Ad Hoc Network, IoT, Kecerdasan Buatan, Rekayasa Perangkat Lunak..."><?= htmlspecialchars($_POST['bios'] ?? ''); ?></textarea>
          <div class="form-hints">Pisahkan dengan koma. Membantu kurator mengalokasikan naskah yang relevan.</div>
        </div>

        <!-- BAGIAN 3: KATA SANDI -->
        <div class="section-titles">
          <i class="fa-solid fa-shield-halved"></i>
          <span>3. Kredensial Keamanan Akun</span>
        </div>

        <div class="form-grid-twos">
          <div class="form-groups">
            <label class="form-labels" for="passwords">Kata Sandi (Min. 8 Karakter) <span class="required-stars">*</span></label>
            <div class="password-wrappers">
              <input type="password" id="passwords" name="passwords" class="form-inputs" style="padding-right: 3rem;" placeholder="Minimal 8 karakter" required>
              <button type="button" class="password-toggles" id="toggle_reg_passwords" aria-label="Lihat kata sandi">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
          <div class="form-groups">
            <label class="form-labels" for="password_confirms">Ulangi Kata Sandi <span class="required-stars">*</span></label>
            <div class="password-wrappers">
              <input type="password" id="password_confirms" name="password_confirms" class="form-inputs" style="padding-right: 3rem;" placeholder="Ketik ulang kata sandi" required>
              <button type="button" class="password-toggles" id="toggle_reg_confirms" aria-label="Lihat konfirmasi kata sandi">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- BAGIAN 4: KESEDIAAN & PERNYATAAN INTEGRITAS -->
        <div class="section-titles">
          <i class="fa-solid fa-certificate"></i>
          <span>4. Kesediaan Peran & Pakta Integritas</span>
        </div>

        <label class="checkbox-cards">
          <input type="checkbox" name="is_reviewer_candidates" value="1" <?= (!empty($_POST['is_reviewer_candidates'])) ? 'checked' : ''; ?>>
          <div class="checkbox-labels">
            <strong>Kesediaan Menjadi Reviewer / Mitra Bestari</strong><br>
            <span style="color: var(--text-bodys); font-size: 0.82rem;">Ya, saya bersedia dihubungi untuk menelaah naskah yang diajukan ke repositori ini sesuai dengan bidang minat riset dan keahlian saya.</span>
          </div>
        </label>

        <label class="checkbox-cards">
          <input type="checkbox" name="consent_privacys" value="1" <?= (!empty($_POST['consent_privacys'])) ? 'checked' : ''; ?> required>
          <div class="checkbox-labels">
            <strong>Pernyataan Privasi & Perlindungan Data <span class="required-stars">*</span></strong><br>
            <span style="color: var(--text-bodys); font-size: 0.82rem;">Ya, saya menyetujui bahwa data pribadi saya disimpan dan dikelola untuk keperluan administrasi publikasi karya ilmiah sesuai Kebijakan Privasi LENTERA & Universitas Madura.</span>
          </div>
        </label>

        <label class="checkbox-cards">
          <input type="checkbox" name="consent_integritys" value="1" <?= (!empty($_POST['consent_integritys'])) ? 'checked' : ''; ?> required>
          <div class="checkbox-labels">
            <strong>Pakta Integritas Akademik & Bebas Plagiasi <span class="required-stars">*</span></strong><br>
            <span style="color: var(--text-bodys); font-size: 0.82rem;">Saya menyatakan bahwa seluruh karya tulis ilmiah yang saya unggah nantinya adalah orisinal, bebas dari fabrikasi/plagiarisme, dan mematuhi etika akademik Universitas Madura.</span>
          </div>
        </label>

        <button type="submit" class="btn-primarys btn-golds" style="width: 100%; justify-content: center; padding: 0.95rem; font-size: 1rem; margin-top: 1.25rem;">
          <i class="fa-solid fa-user-plus"></i> Selesaikan Pendaftaran Akun
        </button>
      </form>

      <div class="auth-footers">
        <p>Sudah memiliki akun terdaftar? <a href="logins.php" class="auth-links">Masuk di sini</a></p>
        <div style="margin-top: 1.25rem;">
          <a href="../../indexs.php" style="color: var(--text-mutes); text-decoration: none; font-size: 0.88rem; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda Utama
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    function setup_toggle(btn_id, field_id) {
      const btn = document.getElementById(btn_id);
      const field = document.getElementById(field_id);
      if (btn && field) {
        btn.addEventListener('click', () => {
          const is_pass = field.getAttribute('type') === 'password';
          field.setAttribute('type', is_pass ? 'text' : 'password');
          btn.innerHTML = is_pass ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
        });
      }
    }
    setup_toggle('toggle_reg_passwords', 'passwords');
    setup_toggle('toggle_reg_confirms', 'password_confirms');
  </script>
</body>
</html>
