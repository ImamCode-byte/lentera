<?php
/**
 * logins.php
 * halaman masuk (login) pengguna lenteras
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/controllerss/auth_controllers.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

// jika sudah login, alihkan sesuai peran
if (sessions::hass('user_ids')) {
    header('Location: ../../indexs.php');
    exit;
}

$error_messages = '';
$success_messages = flashs::gets('success');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth_controllers = new auth_controllers();
    $login_results = $auth_controllers->process_logins($_POST);
    if ($login_results) {
        $roles = sessions::gets('user_roles');
        if ($roles === 'admin') {
            header('Location: ../admins/indexs.php');
        } elseif ($roles === 'curator') {
            header('Location: ../reviews/indexs.php');
        } else {
            header('Location: ../workss/indexs.php');
        }
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
  <title>Masuk ke Akun — LENTERAS Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .auth-containers {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.5rem;
      background: radial-gradient(circle at 10% 20%, rgba(15, 76, 92, 0.08) 0%, transparent 40%),
                  radial-gradient(circle at 90% 80%, rgba(251, 139, 36, 0.08) 0%, transparent 40%),
                  #f8fafc;
    }
    .auth-cards {
      width: 100%;
      max-width: 460px;
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      padding: 2.5rem 2.25rem;
      box-shadow: var(--shadow-mediums);
    }
    .auth-headers {
      text-align: center;
      margin-bottom: 2rem;
    }
    .auth-logo-icons {
      width: 50px;
      height: 50px;
      margin: 0 auto 1rem;
      background: linear-gradient(135deg, var(--teal-primarys), var(--teal-darks));
      border-radius: var(--radius-mediums);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      font-size: 1.5rem;
      box-shadow: 0 4px 12px rgba(15, 76, 92, 0.25);
    }
    .form-groups {
      margin-bottom: 1.25rem;
      text-align: left;
    }
    .form-labels {
      display: block;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--text-titles);
      margin-bottom: 0.45rem;
    }
    .form-inputs {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      font-family: inherit;
      border: 1.5px solid var(--border-colors);
      border-radius: var(--radius-smalls);
      outline: none;
      transition: var(--transition-fasts);
    }
    .form-inputs:focus {
      border-color: var(--teal-primarys);
      box-shadow: 0 0 0 3px rgba(15, 76, 92, 0.12);
    }
    .password-wrappers {
      position: relative;
    }
    .password-toggles {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: var(--text-mutes);
      cursor: pointer;
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
    .alert-errors {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #b91c1c;
    }
    .alert-successs {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #15803d;
    }
    .auth-footers {
      text-align: center;
      margin-top: 2rem;
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
  </style>
</head>
<body>

  <div class="auth-containers">
    <div class="auth-cards">
      <div class="auth-headers">
        <a href="../../indexs.php" style="text-decoration: none;">
          <div class="auth-logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <h1 style="font-size: 1.6rem; font-weight: 800; color: var(--teal-primarys); letter-spacing: -0.5px;">lenteras</h1>
          <p style="font-size: 0.8rem; color: var(--gold-accents); font-weight: 700; text-transform: uppercase;">universitas madura</p>
        </a>
        <h2 style="font-size: 1.25rem; font-weight: 700; margin-top: 1.25rem; color: var(--text-titles);">Masuk ke Akun</h2>
        <p style="font-size: 0.88rem; color: var(--text-bodys);">Gunakan Email, Username, atau NIM/NIP Anda</p>
      </div>

      <?php if (!empty($error_messages)): ?>
      <div class="alert-boxes alert-errors">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars($error_messages); ?></span>
      </div>
      <?php endif; ?>

      <?php if (!empty($success_messages)): ?>
      <div class="alert-boxes alert-successs">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= htmlspecialchars($success_messages); ?></span>
      </div>
      <?php endif; ?>

      <form action="logins.php" method="POST">
        <?= csrfs::inputs(); ?>

        <div class="form-groups">
          <label class="form-labels" for="identifiers">Email, Username, atau NIM / NIP</label>
          <input type="text" id="identifiers" name="identifiers" class="form-inputs" placeholder="Contoh: imamdhespachito001@gmail.com atau imam_hambali_001" value="<?= htmlspecialchars($_POST['identifiers'] ?? $_POST['emails'] ?? ''); ?>" required autofocus>
        </div>

        <div class="form-groups">
          <label class="form-labels" for="passwords">Kata Sandi</label>
          <div class="password-wrappers">
            <input type="password" id="passwords" name="passwords" class="form-inputs" placeholder="Masukkan kata sandi akun Anda" required>
            <button type="button" class="password-toggles" id="toggle_passwords" aria-label="Lihat kata sandi">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-primarys" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 0.95rem; margin-top: 0.75rem;">
          <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
        </button>
      </form>

      <div class="auth-footers">
        <p>Belum memiliki akun? <a href="registers.php" class="auth-links">Daftar sebagai Penulis</a></p>
        <div style="margin-top: 1rem;">
          <a href="../../indexs.php" style="color: var(--text-mutes); text-decoration: none; font-size: 0.85rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
          </a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const toggle_btns = document.getElementById('toggle_passwords');
    const password_fields = document.getElementById('passwords');
    if (toggle_btns && password_fields) {
      toggle_btns.addEventListener('click', () => {
        const is_passwords = password_fields.getAttribute('type') === 'password';
        password_fields.setAttribute('type', is_passwords ? 'text' : 'password');
        toggle_btns.innerHTML = is_passwords ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
      });
    }
  </script>
</body>
</html>