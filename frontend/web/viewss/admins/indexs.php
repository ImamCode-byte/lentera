<?php
/**
 * indexs.php
 * panel super administrator repositori karya ilmiah lenteras universitas madura
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/users.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/facultiess.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/study_programss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/work_typess.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

// verifikasi ketat hak akses: hanya role admin yang diizinkan
$user_roles = sessions::gets('user_roles');
if ($user_roles !== 'admin') {
    flashs::sets_error('Akses ditolak. Halaman ini memiliki proteksi ketat dan khusus untuk Super Administrator Repositori.');
    header('Location: ../auths/logins.php');
    exit;
}

$user_names = sessions::gets('user_names');
$user_emails = sessions::gets('user_emails');
$user_models = new users();
$work_models = new workss();
$faculty_models = new facultiess();
$prodi_models = new study_programss();
$type_models = new work_typess();

// penanganan aksi pengelolaan admin (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfs::validates($_POST['csrf_tokens'] ?? '')) {
        flashs::sets_error('Sesi keamanan kedaluwarsa. Silakan ulangi.');
        header('Location: indexs.php');
        exit;
    }

    $admin_actions = $_POST['admin_actions'] ?? '';

    if ($admin_actions === 'update_role') {
        $target_user_id = (int)($_POST['target_user_id'] ?? 0);
        $new_role = $_POST['new_role'] ?? '';
        if ($target_user_id && in_array($new_role, ['author', 'curator', 'admin'])) {
            $user_models->update_user_roles($target_user_id, $new_role);
            flashs::sets_success("Peran pengguna berhasil diperbarui menjadi '{$new_role}'.");
        }
    } elseif ($admin_actions === 'toggle_active') {
        $target_user_id = (int)($_POST['target_user_id'] ?? 0);
        if ($target_user_id) {
            $user_models->toggle_user_actives($target_user_id);
            flashs::sets_success("Status keaktifan akun pengguna berhasil diubah.");
        }
    }

    header('Location: indexs.php');
    exit;
}

// ambil data untuk seluruh panel
$user_stats = $user_models->get_admin_user_stats();
$repo_stats = $work_models->get_admin_repository_stats();
$all_users = $user_models->get_all_users_for_admin();
$all_faculties = $faculty_models->get_all_with_counts();
$all_prodis = $prodi_models->get_all_actives();
$all_types = $type_models->get_all_actives();
$download_logs = $work_models->get_recent_download_logs(15);

$success_messages = flashs::gets('success');
$error_messages = flashs::gets('error');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Super Admin — LENTERA Universitas Madura</title>
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
      max-width: 1340px;
      margin: 2rem auto 5rem;
      padding: 0 1.5rem;
    }
    .admin-headers {
      background: linear-gradient(135deg, #0f4c5c 0%, #06242c 100%);
      color: #ffffff;
      border-radius: var(--radius-larges);
      padding: 2.25rem 2rem;
      margin-bottom: 2rem;
      position: relative;
      overflow: hidden;
      box-shadow: 0 12px 30px rgba(15, 76, 92, 0.25);
    }
    .admin-headers::after {
      content: '';
      position: absolute;
      right: -30px;
      bottom: -30px;
      width: 260px;
      height: 260px;
      background: radial-gradient(circle, rgba(251, 139, 36, 0.22) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }
    .metric-grids {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
    }
    .metric-cards:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-mediums);
      border-color: var(--teal-primarys);
    }
    .metric-icons {
      width: 50px;
      height: 50px;
      border-radius: var(--radius-mediums);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
    }
    .metric-numbers {
      font-size: 1.75rem;
      font-weight: 800;
      color: var(--text-titles);
      line-height: 1.1;
    }
    .metric-titles {
      font-size: 0.8rem;
      color: var(--text-bodys);
      font-weight: 700;
      margin-top: 0.25rem;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .admin-nav-tabs {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid #e2e8f0;
      padding-bottom: 0.5rem;
      flex-wrap: wrap;
    }
    .nav-tab-items {
      padding: 0.65rem 1.25rem;
      border-radius: var(--radius-smalls);
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--text-bodys);
      background: transparent;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      transition: var(--transition-fasts);
    }
    .nav-tab-items:hover {
      background: #e2e8f0;
      color: var(--text-titles);
    }
    .nav-tab-items.actives {
      background: var(--teal-primarys);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(15, 76, 92, 0.2);
    }
    .table-cards {
      background: #ffffff;
      border-radius: var(--radius-larges);
      overflow: hidden;
      border: 1px solid var(--border-colors);
      box-shadow: var(--shadow-mediums);
      margin-bottom: 2rem;
    }
    .custom-tables {
      width: 100%;
      border-collapse: collapse;
    }
    .custom-tables th {
      background: #f8fafc;
      padding: 1rem 1.25rem;
      font-size: 0.8rem;
      font-weight: 800;
      color: var(--text-titles);
      border-bottom: 2px solid var(--border-colors);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      text-align: left;
    }
    .custom-tables td {
      padding: 1.15rem 1.25rem;
      border-bottom: 1px solid var(--border-colors);
      font-size: 0.88rem;
      vertical-align: middle;
    }
    .custom-tables tbody tr:hover {
      background-color: #fafcfe;
    }
    .role-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.25rem 0.65rem;
      border-radius: var(--radius-fulls);
      font-size: 0.74rem;
      font-weight: 800;
      text-transform: uppercase;
    }
    .role-admin { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
    .role-curator { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .role-author { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
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
          <span class="role-badge role-admin">
            <i class="fa-solid fa-crown"></i> SUPER ADMIN
          </span>
        </div>
        <a href="../../indexs.php" class="nav-items-links"><i class="fa-solid fa-globe"></i> Portal Publik</a>
        <a href="../reviews/indexs.php" class="nav-items-links"><i class="fa-solid fa-feather-pointed"></i> Panel Kurator</a>
        <a href="../auths/logouts.php" class="btn-logins" style="padding: 0.45rem 1rem; font-size: 0.82rem;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
      </div>
    </div>
  </header>

  <main class="page-containers">
    <!-- Admin Hero Banner -->
    <div class="admin-headers">
      <div style="max-width: 820px; position: relative; z-index: 1;">
        <span style="background: rgba(251, 139, 36, 0.25); color: #ffd166; font-size: 0.76rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: var(--radius-fulls); text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-shield-halved"></i> Master Control Panel
        </span>
        <h1 style="font-size: 2rem; font-weight: 800; margin-top: 0.6rem; letter-spacing: -0.5px;">Pusat Kontrol & Manajemen Repositori</h1>
        <p style="color: #e2e8f0; font-size: 0.95rem; margin-top: 0.4rem; line-height: 1.5;">
          Kelola seluruh master data akademik Universitas Madura, tetapkan dewan kurator, pantau pengguna civitas akademika, dan audit aktivitas unduhan naskah ilmiah.
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

    <!-- Master Stat Metrics -->
    <div class="metric-grids">
      <div class="metric-cards">
        <div class="metric-icons" style="background: #fdf2f8; color: #db2777;">
          <i class="fa-solid fa-users"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= $user_stats['total_users'] ?? 0; ?></div>
          <div class="metric-titles">Total Pengguna (<?= $user_stats['total_curators'] ?? 0; ?> Kurator)</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #e0f2fe; color: #0284c7;">
          <i class="fa-solid fa-building-columns"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= count($all_faculties); ?></div>
          <div class="metric-titles">Fakultas (<?= count($all_prodis); ?> Prodi)</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #dcfce7; color: #16a34a;">
          <i class="fa-solid fa-book-bookmark"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= $repo_stats['total_published'] ?? 0; ?></div>
          <div class="metric-titles">Naskah Terbit (<?= $repo_stats['total_works'] ?? 0; ?> Total)</div>
        </div>
      </div>

      <div class="metric-cards">
        <div class="metric-icons" style="background: #fef3c7; color: #d97706;">
          <i class="fa-solid fa-download"></i>
        </div>
        <div>
          <div class="metric-numbers"><?= $repo_stats['total_downloads'] ?? 0; ?></div>
          <div class="metric-titles">Total Unduhan Dokumen</div>
        </div>
      </div>
    </div>

    <!-- Admin Navigation Tabs -->
    <div class="admin-nav-tabs">
      <button type="button" class="nav-tab-items actives" onclick="switch_admin_panel('users')">
        <i class="fa-solid fa-user-gear"></i> Manajemen Pengguna & Peran
      </button>
      <button type="button" class="nav-tab-items" onclick="switch_admin_panel('faculties')">
        <i class="fa-solid fa-graduation-cap"></i> Master Fakultas & Prodi UNIRA
      </button>
      <button type="button" class="nav-tab-items" onclick="switch_admin_panel('types')">
        <i class="fa-solid fa-layer-group"></i> Master Tipe Karya
      </button>
      <button type="button" class="nav-tab-items" onclick="switch_admin_panel('logs')">
        <i class="fa-solid fa-clock-rotate-left"></i> Log Unduhan & Aktivitas
      </button>
    </div>

    <!-- PANEL 1: PENGGUNA & HAK AKSES -->
    <div id="panel_users" class="table-cards">
      <div style="padding: 1.25rem 1.5rem; background: #fafcfe; border-bottom: 1px solid var(--border-colors); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-titles); margin: 0;">
          <i class="fa-solid fa-users-gear" style="color: var(--teal-primarys);"></i> Daftar Civitas Akademika & Hak Akses
        </h3>
        <span style="font-size: 0.82rem; color: var(--text-mutes);">
          Total Terdaftar: <strong><?= count($all_users); ?> pengguna</strong>
        </span>
      </div>

      <div style="overflow-x: auto;">
        <table class="custom-tables">
          <thead>
            <tr>
              <th>Nama Pengguna & Gelar</th>
              <th>Username & Email</th>
              <th>NIM / NIP</th>
              <th>Program Studi</th>
              <th>Peran (Role)</th>
              <th>Status</th>
              <th style="text-align: right;">Ubah Hak Akses</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_users as $usr): ?>
            <tr>
              <td>
                <div style="font-weight: 800; color: var(--text-titles);"><?= htmlspecialchars($usr['name']); ?></div>
                <div style="font-size: 0.78rem; color: var(--text-mutes); margin-top: 0.2rem;">
                  <?= htmlspecialchars($usr['affiliation'] ?? 'Universitas Madura'); ?>
                  <?php if (!empty($usr['orcid_id'])): ?>
                    • <span style="color: var(--teal-primarys);">ORCID: <?= htmlspecialchars($usr['orcid_id']); ?></span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div style="font-weight: 600; color: var(--text-titles); font-family: monospace;">
                  @<?= htmlspecialchars($usr['username'] ?? '-'); ?>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-bodys);"><?= htmlspecialchars($usr['email']); ?></div>
              </td>
              <td>
                <span style="font-family: monospace; font-weight: 700; color: var(--text-titles);">
                  <?= htmlspecialchars($usr['nim_nip'] ?? '-'); ?>
                </span>
              </td>
              <td>
                <span style="font-size: 0.84rem; color: var(--teal-primarys); font-weight: 600;">
                  <?= htmlspecialchars($usr['prodi_name'] ?? 'Pusat / Umum'); ?>
                </span>
              </td>
              <td>
                <span class="role-badge role-<?= htmlspecialchars($usr['role']); ?>">
                  <?php if ($usr['role'] === 'admin'): ?>
                    <i class="fa-solid fa-crown"></i> Admin
                  <?php elseif ($usr['role'] === 'curator'): ?>
                    <i class="fa-solid fa-feather-pointed"></i> Kurator
                  <?php else: ?>
                    <i class="fa-solid fa-user-pen"></i> Penulis
                  <?php endif; ?>
                </span>
              </td>
              <td>
                <?php if (!empty($usr['is_active'])): ?>
                  <span style="background: #dcfce7; color: #15803d; font-size: 0.72rem; font-weight: 800; padding: 0.2rem 0.5rem; border-radius: 4px;">
                    AKTIF
                  </span>
                <?php else: ?>
                  <span style="background: #fee2e2; color: #b91c1c; font-size: 0.72rem; font-weight: 800; padding: 0.2rem 0.5rem; border-radius: 4px;">
                    NONAKTIF
                  </span>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                  <!-- Form Ubah Role -->
                  <form action="indexs.php" method="POST" style="display: inline-block;">
                    <?= csrfs::inputs(); ?>
                    <input type="hidden" name="admin_actions" value="update_role">
                    <input type="hidden" name="target_user_id" value="<?= $usr['id']; ?>">
                    <select name="new_role" onchange="this.form.submit()" style="padding: 0.35rem 0.6rem; font-size: 0.78rem; border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); background: #ffffff; cursor: pointer;">
                      <option value="author" <?= $usr['role'] === 'author' ? 'selected' : ''; ?>>Jadikan Penulis</option>
                      <option value="curator" <?= $usr['role'] === 'curator' ? 'selected' : ''; ?>>Jadikan Kurator</option>
                      <option value="admin" <?= $usr['role'] === 'admin' ? 'selected' : ''; ?>>Jadikan Admin</option>
                    </select>
                  </form>

                  <!-- Toggle Aktif -->
                  <form action="indexs.php" method="POST" style="display: inline-block;">
                    <?= csrfs::inputs(); ?>
                    <input type="hidden" name="admin_actions" value="toggle_active">
                    <input type="hidden" name="target_user_id" value="<?= $usr['id']; ?>">
                    <button type="submit" class="btn-logins" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;" title="Ubah status aktif/nonaktif">
                      <i class="fa-solid fa-power-off"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PANEL 2: MASTER FAKULTAS & PRODI -->
    <div id="panel_faculties" class="table-cards" style="display: none;">
      <div style="padding: 1.25rem 1.5rem; background: #fafcfe; border-bottom: 1px solid var(--border-colors);">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-titles); margin: 0;">
          <i class="fa-solid fa-building-columns" style="color: var(--teal-primarys);"></i> Struktur Fakultas & Program Studi Universitas Madura
        </h3>
      </div>

      <div style="overflow-x: auto;">
        <table class="custom-tables">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Fakultas</th>
              <th>Program Studi Binaan</th>
              <th>Karya Terbit</th>
              <th style="text-align: right;">Portal Fakultas</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_faculties as $fac): ?>
            <tr>
              <td>
                <span style="font-weight: 800; font-family: monospace; background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;">
                  <?= htmlspecialchars($fac['code']); ?>
                </span>
              </td>
              <td>
                <strong style="color: var(--text-titles); font-size: 0.95rem;"><?= htmlspecialchars($fac['name']); ?></strong>
              </td>
              <td>
                <span style="font-weight: 700; color: var(--teal-primarys);"><?= $fac['total_prodis']; ?> Program Studi</span>
              </td>
              <td>
                <span style="background: #dcfce7; color: #15803d; font-weight: 800; padding: 0.2rem 0.6rem; border-radius: var(--radius-fulls); font-size: 0.78rem;">
                  <?= $fac['total_published_works']; ?> Naskah
                </span>
              </td>
              <td style="text-align: right;">
                <a href="../facultiess/details.php?codes=<?= htmlspecialchars($fac['code']); ?>" target="_blank" class="btn-primarys" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: #f1f5f9; color: var(--text-titles); box-shadow: none; border: 1px solid var(--border-colors);">
                  <i class="fa-solid fa-arrow-up-right-from-square"></i> Kunjungi Portal
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PANEL 3: MASTER TIPE KARYA ILMIAH -->
    <div id="panel_types" class="table-cards" style="display: none;">
      <div style="padding: 1.25rem 1.5rem; background: #fafcfe; border-bottom: 1px solid var(--border-colors);">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-titles); margin: 0;">
          <i class="fa-solid fa-layer-group" style="color: var(--teal-primarys);"></i> Jenis / Nomenklatur Karya Ilmiah Repositori
        </h3>
      </div>

      <div style="overflow-x: auto;">
        <table class="custom-tables">
          <thead>
            <tr>
              <th style="width: 80px;">ID</th>
              <th>Kode Tipe</th>
              <th>Nama Tipe Karya Ilmiah</th>
              <th>Status Master</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($all_types as $tp): ?>
            <tr>
              <td>#<?= htmlspecialchars($tp['id']); ?></td>
              <td>
                <span class="type-badges" style="font-size: 0.75rem;">
                  <?= htmlspecialchars($tp['code']); ?>
                </span>
              </td>
              <td>
                <strong style="color: var(--text-titles);"><?= htmlspecialchars($tp['name']); ?></strong>
              </td>
              <td>
                <span style="color: #15803d; font-weight: 700; font-size: 0.8rem;">
                  <i class="fa-solid fa-check"></i> Aktif untuk Pengajuan Naskah
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PANEL 4: LOG AKTIVITAS & UNDUHAN -->
    <div id="panel_logs" class="table-cards" style="display: none;">
      <div style="padding: 1.25rem 1.5rem; background: #fafcfe; border-bottom: 1px solid var(--border-colors); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--text-titles); margin: 0;">
          <i class="fa-solid fa-clock-rotate-left" style="color: var(--teal-primarys);"></i> Audit Trail: Riwayat Unduhan Karya Ilmiah
        </h3>
        <span style="font-size: 0.82rem; color: var(--text-mutes);">15 Catatan Terkini</span>
      </div>

      <div style="overflow-x: auto;">
        <table class="custom-tables">
          <thead>
            <tr>
              <th>Waktu Unduh</th>
              <th>Judul Karya Ilmiah</th>
              <th>Tipe</th>
              <th>Pengunduh / Pengguna</th>
              <th>Alamat IP</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($download_logs)): ?>
            <tr>
              <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-mutes);">
                Belum ada rekaman log unduhan naskah.
              </td>
            </tr>
            <?php else: ?>
              <?php foreach ($download_logs as $lg): ?>
              <tr>
                <td style="font-size: 0.82rem; color: var(--text-mutes); white-space: nowrap;">
                  <?= date('d M Y, H:i', strtotime($lg['downloaded_at'])); ?> WIB
                </td>
                <td style="max-width: 420px;">
                  <strong style="color: var(--text-titles);"><?= htmlspecialchars($lg['work_title']); ?></strong>
                </td>
                <td>
                  <span class="type-badges" style="font-size: 0.7rem;"><?= htmlspecialchars($lg['type_code']); ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($lg['user_name'] ?? 'Publik (Guest)'); ?>
                </td>
                <td style="font-family: monospace; font-size: 0.8rem; color: var(--text-mutes);">
                  <?= htmlspecialchars($lg['ip_address']); ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script>
    function switch_admin_panel(panel_id) {
      const panels = ['users', 'faculties', 'types', 'logs'];
      panels.forEach(p => {
        const el = document.getElementById('panel_' + p);
        if (el) el.style.display = p === panel_id ? 'block' : 'none';
      });

      const buttons = document.querySelectorAll('.nav-tab-items');
      buttons.forEach(b => b.classList.remove('actives'));
      event.currentTarget.classList.add('actives');
    }
  </script>
</body>
</html>