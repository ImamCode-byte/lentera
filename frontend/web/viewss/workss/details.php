<?php
/**
 * details.php
 * halaman detail karya ilmiah publik dengan pratinjau naskah, generator sitasi instan, lisensi, dan kata kunci interaktif
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';

sessions::starts();

$work_ids = (int)($_GET['ids'] ?? 0);
$work_models = new workss();
$works = $work_models->find_by_ids($work_ids);

if (!$works) {
    header('Location: /lentera/indexs.php');
    exit;
}

// naikkan counter pembaca (view count)
$work_models->increment_views($work_ids);

// buat format sitasi otomatis dalam berbagai standar akademik
$author_names = htmlspecialchars($works['author_name']);
$years = htmlspecialchars($works['year']);
$titles = htmlspecialchars($works['title']);
$prodis = htmlspecialchars($works['prodi_name']);
$faculties = htmlspecialchars($works['faculty_name']);
$types = htmlspecialchars($works['type_name']);
$app_urls = get_configs('app_urls', 'http://localhost/lentera');
$work_urls = "{$app_urls}/viewss/workss/details.php?ids={$works['id']}";

// 1. APA 7th Edition
$citation_apas = "{$author_names} ({$years}). {$titles}. [{$types}, {$faculties}, Universitas Madura]. Repositori Lentera. {$work_urls}";

// 2. IEEE
$citation_ieees = "{$author_names}, \"{$titles},\" {$types}, {$prodis}, {$faculties}, Universitas Madura, Pamekasan, Indonesia, {$years}. [Online]. Tersedia: {$work_urls}";

// 3. Harvard
$citation_harvards = "{$author_names}, {$years}. {$titles}. {$types}, {$faculties}, Universitas Madura. Tersedia di: <{$work_urls}> [Diakses " . date('d M Y') . "].";

// 4. BibTeX
$bibtex_keys = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', substr($works['author_name'], 0, 8))) . $works['year'];
$citation_bibtexs = "@misc{{$bibtex_keys},\n  author = {{$author_names}},\n  title = {{$titles}},\n  year = {{$years}},\n  institution = {Universitas Madura},\n  url = {{$work_urls}}\n}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($works['title']); ?> — Lentera Universitas Madura</title>
  <meta name="description" content="<?= htmlspecialchars(mb_strimwidth($works['abstract'], 0, 160, '...')); ?>">
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers { max-width: 1200px; margin: 2rem auto 5rem; padding: 0 1.5rem; }
    .detail-grids { display: grid; grid-template-columns: 2.2fr 1fr; gap: 2.5rem; }
    .content-cards { background: #ffffff; border-radius: var(--radius-larges); padding: 2.5rem; border: 1px solid var(--border-colors); box-shadow: var(--shadow-smalls); }
    .meta-cards { background: #ffffff; border-radius: var(--radius-larges); padding: 1.75rem; border: 1px solid var(--border-colors); box-shadow: var(--shadow-smalls); margin-bottom: 1.5rem; }
    
    .keyword-badges {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.4rem 0.9rem;
      background: var(--bg-mains);
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-fulls);
      font-size: 0.82rem;
      margin: 0.25rem;
      color: var(--teal-primarys);
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition-fasts);
    }
    .keyword-badges:hover {
      background: var(--teal-lights);
      border-color: var(--teal-primarys);
    }

    /* citation generator styling */
    .citation-containers {
      background: #f8fafc;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-mediums);
      padding: 1.25rem;
      margin-top: 1rem;
    }
    .citation-format-tabs {
      display: flex;
      gap: 0.4rem;
      margin-bottom: 0.75rem;
      border-bottom: 1px solid var(--border-colors);
      padding-bottom: 0.5rem;
      flex-wrap: wrap;
    }
    .citation-tab-btn {
      background: none;
      border: 1px solid transparent;
      padding: 0.35rem 0.75rem;
      font-size: 0.8rem;
      font-weight: 700;
      border-radius: var(--radius-smalls);
      cursor: pointer;
      color: var(--text-bodys);
      font-family: inherit;
      transition: var(--transition-fasts);
    }
    .citation-tab-btn.actives, .citation-tab-btn:hover {
      background: var(--teal-primarys);
      color: #ffffff;
    }
    .citation-display-boxes {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-smalls);
      padding: 1rem;
      font-size: 0.88rem;
      line-height: 1.6;
      color: var(--text-titles);
      margin-bottom: 0.75rem;
      word-break: break-word;
      font-family: monospace;
      white-space: pre-wrap;
    }
    .btn-copy-citations {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      background: var(--teal-lights);
      color: var(--teal-primarys);
      border: 1px solid var(--teal-primarys);
      padding: 0.45rem 0.9rem;
      border-radius: var(--radius-smalls);
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      transition: var(--transition-fasts);
    }
    .btn-copy-citations:hover {
      background: var(--teal-primarys);
      color: #ffffff;
    }

    /* lisensi card */
    .license-boxes {
      display: flex;
      gap: 1rem;
      align-items: center;
      background: var(--bg-mains);
      border: 1px solid var(--border-colors);
      padding: 1.25rem;
      border-radius: var(--radius-mediums);
      margin-top: 2rem;
    }

    @media (max-width: 992px) {
      .detail-grids { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- navigasi utamas -->
  <header class="nav-containers">
    <div class="nav-wrappers">
      <a href="/lentera/indexs.php" class="logo-links">
        <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
        <div>
          <span class="logo-texts">Lentera</span>
          <span class="logo-subtexts">universitas madura</span>
        </div>
      </a>

      <ul class="nav-menuss">
        <li><a href="/lentera/indexs.php" class="nav-items-links">Beranda</a></li>
        <li><a href="/lentera/viewss/searchs/indexs.php" class="nav-items-links">Katalog Karya</a></li>
        <li><a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($works['faculty_code'] ?? 'FT'); ?>" class="nav-items-links" style="color: var(--teal-primarys); font-weight: 700;">Fakultas</a></li>
        <li><a href="/lentera/viewss/statisticss/indexs.php" class="nav-items-links">Statistik</a></li>
      </ul>

      <div class="nav-auth-buttons">
        <?php if (sessions::hass('user_ids')): ?>
          <a href="/lentera/viewss/workss/indexs.php" class="nav-items-links" style="color: var(--teal-primarys); font-weight: 700;">
            <i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars(sessions::gets('user_names')); ?>
          </a>
          <a href="/lentera/viewss/auths/logouts.php" class="btn-logins" style="padding: 0.45rem 0.85rem; font-size: 0.8rem;">Keluar</a>
        <?php else: ?>
          <a href="/lentera/viewss/auths/logins.php" class="btn-logins">Masuk</a>
          <a href="/lentera/viewss/auths/registers.php" class="btn-primarys">Unggah Karya</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="page-containers">
    
    <!-- breadcrumb -->
    <div style="font-size: 0.85rem; color: var(--text-mutes); margin-bottom: 1.5rem;">
      <a href="/lentera/indexs.php" style="color: inherit; text-decoration: none;">Beranda</a> &rsaquo; 
      <a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($works['faculty_code'] ?? 'FT'); ?>" style="color: inherit; text-decoration: none;">
        <?= htmlspecialchars($works['faculty_name']); ?>
      </a> &rsaquo; 
      <a href="/lentera/viewss/searchs/indexs.php?prodis=<?= (int)$works['prodi_id']; ?>" style="color: inherit; text-decoration: none;">
        <?= htmlspecialchars($works['prodi_name']); ?>
      </a> &rsaquo; 
      <span style="color: var(--teal-primarys); font-weight: 600;">Detail Karya</span>
    </div>

    <?php if ($works['status'] === 'waiting_payment' && sessions::gets('user_ids') == $works['author_id']): ?>
      <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: var(--radius-mediums); padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
          <div style="width: 42px; height: 42px; background: #f59e0b; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
            <i class="fa-solid fa-qrcode"></i>
          </div>
          <div>
            <h4 style="margin: 0 0 0.2rem; font-size: 1.05rem; font-weight: 800; color: #92400e;">Naskah Anda Telah di-ACC — Menunggu Pembayaran APC</h4>
            <p style="margin: 0; font-size: 0.86rem; color: #b45309;">Selesaikan pembayaran melalui QRIS/DANA sebesar Rp 150.000 dan unggah bukti transfer agar Admin Repositori memverifikasi dan menerbitkan naskah ini ke publik.</p>
          </div>
        </div>
        <a href="payments.php?ids=<?= $works['id']; ?>" class="btn-primarys btn-golds" style="padding: 0.65rem 1.25rem; font-size: 0.9rem; font-weight: 800; text-decoration: none;">
          <i class="fa-solid fa-credit-card"></i> Selesaikan Pembayaran APC
        </a>
      </div>
    <?php endif; ?>

    <div class="detail-grids">

      <!-- kolom konten naskah utama -->
      <article class="content-cards">
        
        <div style="display: flex; gap: 0.6rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
          <span class="type-badges" style="background: var(--teal-lights); color: var(--teal-primarys); font-weight: 700;">
            <?= htmlspecialchars($works['type_name']); ?>
          </span>
          <span style="font-size: 0.85rem; color: var(--text-mutes); font-weight: 600;">
            <i class="fa-regular fa-calendar"></i> Tahun <?= htmlspecialchars($works['year']); ?>
          </span>
          <span style="font-size: 0.82rem; background: #e0f2fe; color: #0369a1; padding: 0.2rem 0.6rem; border-radius: var(--radius-smalls); font-weight: 600;">
            <i class="fa-solid fa-lock-open"></i> Akses Terbuka
          </span>
        </div>

        <h1 style="font-size: 1.9rem; font-weight: 800; color: var(--text-titles); line-height: 1.35; margin-bottom: 1.5rem;">
          <?= htmlspecialchars($works['title']); ?>
        </h1>

        <!-- info penulis & institusi -->
        <div style="display: flex; align-items: center; gap: 1rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-colors); margin-bottom: 2rem;">
          <div class="author-avatars" style="width: 48px; height: 48px; font-size: 1.15rem; font-weight: 800;">
            <?= htmlspecialchars(substr($works['author_name'], 0, 2)); ?>
          </div>
          <div>
            <div style="font-weight: 700; color: var(--text-titles); font-size: 1.05rem;"><?= htmlspecialchars($works['author_name']); ?></div>
            <div style="font-size: 0.85rem; color: var(--text-mutes); margin-top: 0.2rem;">
              <span><?= htmlspecialchars($works['prodi_name']); ?> (<?= htmlspecialchars($works['prodi_degree']); ?>)</span> &bull; 
              <span><?= htmlspecialchars($works['faculty_name']); ?> Universitas Madura</span>
            </div>
            <?php if (!empty($works['author_nim_nip'])): ?>
              <div style="font-size: 0.78rem; color: var(--text-mutes);">NIM/NIP: <?= htmlspecialchars($works['author_nim_nip']); ?></div>
            <?php endif; ?>
          </div>
        </div>

        <!-- abstrak -->
        <section style="margin-bottom: 2rem;">
          <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--teal-primarys); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-align-left"></i> Abstrak
          </h2>
          <div style="font-size: 0.95rem; line-height: 1.85; color: var(--text-bodys); text-align: justify;">
            <?= nl2br(htmlspecialchars($works['abstract'])); ?>
          </div>
        </section>

        <!-- kata kunci interaktif -->
        <section style="margin-bottom: 2.5rem;">
          <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-titles); margin-bottom: 0.65rem;">
            Kata Kunci (Keywords)
          </h3>
          <div>
            <?php if (!empty($works['keywordss'])): ?>
              <?php foreach ($works['keywordss'] as $keyword_items): ?>
                <a href="/lentera/viewss/searchs/indexs.php?qs=<?= urlencode($keyword_items); ?>" class="keyword-badges" title="Cari karya dengan kata kunci ini">
                  <i class="fa-solid fa-tag" style="color: var(--gold-accents); font-size: 0.72rem;"></i> 
                  <?= htmlspecialchars($keyword_items); ?>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <span style="color: var(--text-mutes); font-size: 0.88rem;">Tidak ada kata kunci terdaftar.</span>
            <?php endif; ?>
          </div>
        </section>

        <!-- generator sitasi otomatis (how to cite) -->
        <section style="margin-bottom: 2rem;">
          <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-titles); margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-quote-left" style="color: var(--gold-accents);"></i> Cara Mengutip (How to Cite)
          </h3>
          <p style="font-size: 0.82rem; color: var(--text-mutes); margin-bottom: 0.5rem;">
            Pilih format sitasi standar untuk mengutip naskah ini ke dalam penelitian Anda:
          </p>

          <div class="citation-containers">
            <div class="citation-format-tabs">
              <button class="citation-tab-btn actives" onclick="switchCitation('apa', this)">APA (7th Ed.)</button>
              <button class="citation-tab-btn" onclick="switchCitation('ieee', this)">IEEE</button>
              <button class="citation-tab-btn" onclick="switchCitation('harvard', this)">Harvard</button>
              <button class="citation-tab-btn" onclick="switchCitation('bibtex', this)">BibTeX</button>
            </div>

            <div id="citationText" class="citation-display-boxes"><?= htmlspecialchars($citation_apas); ?></div>

            <button class="btn-copy-citations" onclick="copyCitation()">
              <i class="fa-regular fa-copy"></i> <span id="copyBtnLabel">Salin Sitasi</span>
            </button>
          </div>
        </section>

        <!-- lisensi hak cipta -->
        <div class="license-boxes">
          <div style="font-size: 2.2rem; color: var(--teal-primarys);">
            <i class="fa-brands fa-creative-commons"></i>
          </div>
          <div>
            <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-titles); margin-bottom: 0.25rem;">
              Lisensi Distribusi Karya Ilmiah: CC BY 4.0
            </div>
            <p style="font-size: 0.82rem; color: var(--text-bodys); line-height: 1.5;">
              Karya ini dilisensikan di bawah <strong>Creative Commons Attribution 4.0 International License</strong>. Publik diizinkan membaca, mengutip, dan membagikan karya ini dengan tetap mencantumkan atribusi kepada penulis asli dan Universitas Madura.
            </p>
          </div>
        </div>

      </article>

      <!-- kolom kanan: aksi berkas & statistik -->
      <aside>
        
        <!-- kartu unduh naskah -->
        <div class="meta-cards" style="border-top: 4px solid var(--gold-accents);">
          <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--text-titles); margin-bottom: 1rem;">
            <i class="fa-solid fa-file-arrow-down" style="color: var(--gold-accents);"></i> Berkas Naskah Penuh
          </h3>

          <?php if (!empty($works['file_path'])): ?>
            <a href="downloads.php?ids=<?= (int)$works['id']; ?>" class="btn-primarys" style="display: flex; align-items: center; justify-content: center; gap: 0.6rem; width: 100%; padding: 0.9rem; font-size: 0.92rem; font-weight: 800; text-decoration: none; margin-bottom: 0.75rem;">
              <i class="fa-solid fa-file-pdf" style="font-size: 1.2rem;"></i> Unduh Naskah Lengkap (PDF)
            </a>
            <div style="font-size: 0.78rem; color: var(--text-mutes); text-align: center; margin-bottom: 1.5rem;">
              <?= htmlspecialchars($works['original_name'] ?? 'dokumen_skripsi.pdf'); ?> &bull; Versi <?= htmlspecialchars($works['file_version'] ?? 1); ?>
            </div>
          <?php else: ?>
            <div style="background: var(--bg-mains); padding: 1.25rem; border-radius: var(--radius-mediums); text-align: center; color: var(--text-mutes); font-size: 0.88rem; margin-bottom: 1.5rem;">
              <i class="fa-solid fa-clock" style="margin-bottom: 0.5rem; font-size: 1.5rem; display: block;"></i>
              Berkas digital naskah sedang dalam sinkronisasi repositori.
            </div>
          <?php endif; ?>

          <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--teal-primarys); margin-bottom: 0.75rem; border-top: 1px solid var(--border-colors); padding-top: 1rem;">
            Statistik Pemanfaatan
          </h4>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
            <div style="background: var(--bg-mains); padding: 0.75rem; border-radius: var(--radius-smalls); text-align: center;">
              <div style="font-size: 1.25rem; font-weight: 800; color: var(--teal-primarys);"><?= number_format($works['view_count']); ?></div>
              <div style="font-size: 0.75rem; color: var(--text-mutes);">Total Dilihat</div>
            </div>
            <div style="background: var(--bg-mains); padding: 0.75rem; border-radius: var(--radius-smalls); text-align: center;">
              <div style="font-size: 1.25rem; font-weight: 800; color: var(--gold-accents);"><?= number_format($works['download_count']); ?></div>
              <div style="font-size: 0.75rem; color: var(--text-mutes);">Total Diunduh</div>
            </div>
          </div>
          <div style="font-size: 0.75rem; color: var(--text-mutes); text-align: center;">
            <i class="fa-solid fa-shield-check"></i> Setiap pengunduhan tercatat pada audit log repositori.
          </div>
        </div>

        <!-- kartu identitas metadata -->
        <div class="meta-cards">
          <h4 style="font-size: 0.95rem; font-weight: 800; color: var(--text-titles); margin-bottom: 1rem; border-bottom: 2px solid var(--teal-lights); padding-bottom: 0.5rem;">
            Metadata Akademik
          </h4>
          <ul style="list-style: none; font-size: 0.88rem; line-height: 2.1; color: var(--text-bodys);">
            <li><strong>Fakultas:</strong> <?= htmlspecialchars($works['faculty_name']); ?></li>
            <li><strong>Program Studi:</strong> <?= htmlspecialchars($works['prodi_name']); ?></li>
            <li><strong>Jenjang:</strong> Strata-<?= $works['prodi_degree'] === 'S2' ? '2 (Magister)' : '1 (Sarjana)'; ?></li>
            <li><strong>Dosen Pembimbing:</strong> <?= htmlspecialchars($works['supervisor'] ?: 'Tim Dosen Pembimbing'); ?></li>
            <li><strong>Status Verifikasi:</strong> <span class="type-badges" style="background: #dcfce7; color: #15803d; font-size: 0.75rem;"><?= htmlspecialchars($works['status']); ?></span></li>
            <li><strong>Institusi:</strong> Universitas Madura</li>
          </ul>
        </div>

        <!-- kartu jelajah fakultas -->
        <div class="meta-cards" style="background: var(--teal-lights); border-color: rgba(15, 76, 92, 0.15);">
          <h4 style="font-size: 0.92rem; font-weight: 800; color: var(--teal-primarys); margin-bottom: 0.5rem;">
            <i class="fa-solid fa-landmark"></i> Repositori Fakultas
          </h4>
          <p style="font-size: 0.82rem; color: var(--text-bodys); line-height: 1.6; margin-bottom: 1rem;">
            Jelajahi koleksi riset, skripsi, dan karya ilmiah lainnya dari <?= htmlspecialchars($works['faculty_name']); ?>.
          </p>
          <a href="/lentera/viewss/facultiess/details.php?codes=<?= urlencode($works['faculty_code'] ?? 'FT'); ?>" class="btn-primarys" style="display: block; text-align: center; padding: 0.55rem; font-size: 0.82rem;">
            Buka Portal Fakultas &rarr;
          </a>
        </div>

      </aside>

    </div>

  </main>

  <!-- footers -->
  <footer class="footer-containers">
    <div class="footer-wrappers">
      <div>
        <div class="logo-links" style="margin-bottom: 1rem;">
          <div class="logo-icons"><i class="fa-solid fa-fire-flame-curved"></i></div>
          <div>
            <span class="logo-texts" style="color: #ffffff;">Lentera</span>
            <span class="logo-subtexts">universitas madura</span>
          </div>
        </div>
        <p style="font-size: 0.9rem; line-height: 1.7;">
          Laman Etalase Karya Tulis Ilmiah Terpadu Akademik — platform repositori resmi pengarsipan dan publikasi riset civitas akademika Universitas Madura.
        </p>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Tautan Cepat</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9rem;">
          <li><a href="/lentera/indexs.php" style="color: inherit; text-decoration: none;">Beranda</a></li>
          <li><a href="/lentera/viewss/searchs/indexs.php" style="color: inherit; text-decoration: none;">Katalog & Pencarian</a></li>
          <li><a href="/lentera/viewss/statisticss/indexs.php" style="color: inherit; text-decoration: none;">Statistik & Akreditasi</a></li>
          <li><a href="/lentera/viewss/workss/creates.php" style="color: inherit; text-decoration: none;">Unggah Karya</a></li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; margin-bottom: 1rem;">Sekretariat</h4>
        <p style="font-size: 0.88rem; line-height: 1.7;">
          Universitas Madura (UNIRA)<br>
          Jl. Raya Panglegur No. 3, Pamekasan, Madura<br>
          Surel: <span style="color: var(--gold-accents);">lentera@unira.ac.id</span>
        </p>
      </div>
    </div>

    <div class="footer-bottoms">
      &copy; 2026 Lentera Universitas Madura. Seluruh hak cipta dilindungi undang-undang.
    </div>
  </footer>

  <script>
    const citations = {
      apa: <?= json_encode($citation_apas); ?>,
      ieee: <?= json_encode($citation_ieees); ?>,
      harvard: <?= json_encode($citation_harvards); ?>,
      bibtex: <?= json_encode($citation_bibtexs); ?>
    };

    function switchCitation(format, btn) {
      document.querySelectorAll('.citation-tab-btn').forEach(b => b.classList.remove('actives'));
      btn.classList.add('actives');
      document.getElementById('citationText').innerText = citations[format];
    }

    function copyCitation() {
      const text = document.getElementById('citationText').innerText;
      navigator.clipboard.writeText(text).then(() => {
        const label = document.getElementById('copyBtnLabel');
        label.innerText = 'Sitasi Berhasil Disalin!';
        setTimeout(() => {
          label.innerText = 'Salin Sitasi';
        }, 2500);
      });
    }
  </script>
</body>
</html>
