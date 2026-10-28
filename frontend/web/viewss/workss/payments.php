<?php
/**
 * payments.php
 * halaman pembayaran biaya publikasi (apc) dengan qr code dana dan auto-publish
 * seluruh fungsi dan variabel konsisten lowercase dan akhiran s
 */

require_once dirname(__DIR__, 4) . '/backend/configs/apps.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/workss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/modelss/paymentss.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/sessions.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/flashs.php';
require_once dirname(__DIR__, 4) . '/backend/apps/helperss/csrfs.php';

sessions::starts();

if (!sessions::hass('user_ids')) {
    flashs::sets_error('Silakan masuk terlebih dahulu untuk mengakses pembayaran publikasi.');
    header('Location: ../auths/logins.php');
    exit;
}

$user_ids = (int)sessions::gets('user_ids');
$work_ids = (int)($_GET['ids'] ?? 0);

$work_models = new workss();
$works = $work_models->find_by_ids($work_ids);

if (!$works || $works['author_id'] != $user_ids) {
    flashs::sets_error('Naskah ilmiah tidak ditemukan atau Anda tidak memiliki akses.');
    header('Location: indexs.php');
    exit;
}

// ATURAN KETAT: Jika masih revisi atau belum di-ACC kurator, TIDAK BISA BAYAR!
if ($works['status'] === 'revision') {
    flashs::sets_error('Naskah Anda masih dalam tahap revisi. Anda belum bisa melakukan pembayaran sebelum naskah dinyatakan lolos kurasi (di-ACC) oleh kurator.');
    header('Location: indexs.php');
    exit;
}

if ($works['status'] === 'submitted' || $works['status'] === 'in_review') {
    flashs::sets_error('Naskah Anda masih dalam proses penelaahan kurator. Pembayaran hanya dapat diproses setelah naskah disetujui (ACC).');
    header('Location: indexs.php');
    exit;
}

if ($works['status'] === 'published') {
    flashs::sets_success('Naskah ini sudah berhasil dibayar dan resmi diterbitkan di etalase repositori!');
    header('Location: details.php?ids=' . $work_ids);
    exit;
}

$payment_models = new paymentss();
$invoices = $payment_models->get_or_creates_invoices($work_ids, $user_ids, 150000.00);

$error_messages = '';

// Proses konfirmasi pembayaran -> AUTO PUBLISH!
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfs::validates($_POST['csrf_tokens'] ?? '')) {
        $error_messages = 'Sesi keamanan kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $sender_names = trim($_POST['sender_names'] ?? '');
        $reference_numbers = trim($_POST['reference_numbers'] ?? '');

        if (empty($sender_names) || empty($reference_numbers)) {
            $error_messages = 'Nama pengirim dan nomor referensi transaksi DANA wajib diisi.';
        } else {
            // tangani upload bukti jika ada
            $proof_path = null;
            if (!empty($_FILES['proof_files']['name'])) {
                $ext = strtolower(pathinfo($_FILES['proof_files']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
                    $new_name = 'proof_' . $invoices['id'] . '_' . time() . '.' . $ext;
                    $dest_dir = dirname(__DIR__, 4) . '/uploads/';
                    if (!is_dir($dest_dir)) mkdir($dest_dir, 0777, true);
                    if (move_uploaded_file($_FILES['proof_files']['tmp_name'], $dest_dir . $new_name)) {
                        $proof_path = 'uploads/' . $new_name;
                    }
                }
            }

            // Jalankan auto-publish
            $published_success = $payment_models->complete_and_auto_publish($invoices['id'], $work_ids, [
                'sender_names'      => $sender_names,
                'reference_numbers' => $reference_numbers,
                'proof_files'       => $proof_path
            ]);

            if ($published_success) {
                flashs::sets_success('Alhamdulillah! Pembayaran Anda telah terkonfirmasi. Naskah Anda kini telah OTOMATIS RESMI DITERBITKAN ke etalase publik LENTERA Universitas Madura!');
                header('Location: details.php?ids=' . $work_ids);
                exit;
            } else {
                $error_messages = 'Terjadi kendala saat menerbitkan naskah otomatis. Silakan coba kembali.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pembayaran Publikasi (APC) — LENTERA Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .page-containers {
      max-width: 980px;
      margin: 2.5rem auto 5rem;
      padding: 0 1.5rem;
    }
    .payment-layout-grids {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 2rem;
    }
    .payment-cards {
      background: #ffffff;
      border: 1px solid var(--border-colors);
      border-radius: var(--radius-larges);
      padding: 2.25rem 2rem;
      box-shadow: 0 8px 24px rgba(15, 76, 92, 0.08);
    }
    .qr-showcase-boxes {
      background: #fafcfe;
      border: 2px dashed #cbd5e1;
      border-radius: var(--radius-mediums);
      padding: 1.75rem;
      text-align: center;
      margin-bottom: 1.5rem;
    }
    .dana-brand-badges {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #118eea;
      color: #ffffff;
      font-weight: 800;
      font-size: 0.82rem;
      padding: 0.35rem 0.85rem;
      border-radius: var(--radius-fulls);
      margin-bottom: 1rem;
    }
    .qr-image-frames {
      max-width: 290px;
      margin: 0 auto 1rem;
      background: #ffffff;
      padding: 6px;
      border-radius: var(--radius-mediums);
      border: 1px solid #e2e8f0;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .qr-image-frames img {
      width: 100%;
      height: auto;
      border-radius: var(--radius-smalls);
      display: block;
    }
    .price-tag-bigs {
      font-size: 1.85rem;
      font-weight: 800;
      color: var(--teal-primarys);
      margin: 0.25rem 0;
    }
    .form-groups {
      margin-bottom: 1.25rem;
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
      font-size: 0.92rem;
      border: 1.5px solid var(--border-colors);
      border-radius: var(--radius-smalls);
      outline: none;
      transition: var(--transition-fasts);
    }
    .form-inputs:focus {
      border-color: var(--teal-primarys);
      box-shadow: 0 0 0 3px rgba(15, 76, 92, 0.12);
    }
    .alert-acc-boxes {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: var(--radius-smalls);
      padding: 1.25rem;
      margin-bottom: 1.75rem;
      display: flex;
      gap: 0.85rem;
      align-items: flex-start;
    }
    @media (max-width: 820px) {
      .payment-layout-grids {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <!-- Header Navigation -->
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
      <div>
        <a href="indexs.php" class="btn-logins" style="font-size: 0.84rem; padding: 0.4rem 0.9rem;">
          <i class="fa-solid fa-arrow-left"></i> Kembali ke Karya Saya
        </a>
      </div>
    </div>
  </header>

  <main class="page-containers">
    <!-- Notice ACC Kurator -->
    <div class="alert-acc-boxes">
      <div style="width: 40px; height: 40px; background: #22c55e; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
        <i class="fa-solid fa-check"></i>
      </div>
      <div>
        <h3 style="font-size: 1.05rem; font-weight: 800; color: #15803d; margin: 0 0 0.35rem;">
          Selamat! Naskah Anda Telah di-ACC oleh Tim Kurator
        </h3>
        <p style="font-size: 0.88rem; color: #166534; line-height: 1.5; margin: 0;">
          Naskah ilmiah Anda telah dinyatakan memenuhi standar mutu dan siap diterbitkan ke repositori resmi Universitas Madura. Silakan selesaikan biaya pemeliharaan publikasi (APC) di bawah ini. <strong>Setelah konfirmasi pembayaran, naskah Anda akan OTOMATIS TERBIT ke publik secara instan tanpa perlu menunggu lagi!</strong>
        </p>
      </div>
    </div>

    <?php if (!empty($error_messages)): ?>
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem 1.25rem; border-radius: var(--radius-smalls); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= htmlspecialchars($error_messages); ?></span>
      </div>
    <?php endif; ?>

    <div class="payment-layout-grids">
      <!-- SISI KIRI: INSTRUKSI PEMBAYARAN & QR DANA -->
      <div class="payment-cards">
        <span style="font-size: 0.78rem; font-weight: 800; color: var(--gold-accents); text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-qrcode"></i> QRIS & DANA Payment Gateway
        </span>
        <h2 style="font-size: 1.45rem; font-weight: 800; color: var(--text-titles); margin-top: 0.35rem; margin-bottom: 1.25rem;">
          Biaya Publikasi Naskah Ilmiah (APC)
        </h2>

        <div style="background: #f8fafc; border: 1px solid var(--border-colors); border-radius: var(--radius-smalls); padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
          <div style="font-size: 0.78rem; color: var(--text-mutes); text-transform: uppercase; font-weight: 700;">Naskah yang Diterbitkan:</div>
          <div style="font-size: 0.95rem; font-weight: 800; color: var(--text-titles); margin-top: 0.25rem; line-height: 1.4;">
            <?= htmlspecialchars($works['title']); ?>
          </div>
          <div style="font-size: 0.82rem; color: var(--teal-primarys); font-weight: 700; margin-top: 0.4rem;">
            <?= htmlspecialchars($works['type_name']); ?> • <?= htmlspecialchars($works['prodi_name']); ?>
          </div>
        </div>

        <!-- Box QR DANA / QRIS -->
        <div class="qr-showcase-boxes">
          <div class="dana-brand-badges" style="background: linear-gradient(135deg, #e11d48, #be123c); margin-bottom: 0.5rem;">
            <i class="fa-solid fa-qrcode"></i> QRIS STANDAR PEMBAYARAN NASIONAL
          </div>
          <div style="font-size: 1.05rem; font-weight: 800; color: var(--text-titles); margin-bottom: 0.2rem;">
            Dhes Mart / Imam
          </div>
          <div style="font-size: 0.78rem; color: var(--text-mutes); margin-bottom: 1rem;">
            NMID: <strong style="font-family: monospace; color: var(--text-titles);">ID1026533208330</strong> (A01)
          </div>

          <!-- Official QRIS Image from User -->
          <div class="qr-image-frames">
            <a href="/lentera/uploads/payment/QRIS%20IMAM.jpeg" target="_blank" title="Klik untuk melihat ukuran penuh">
              <img src="/lentera/uploads/payment/QRIS%20IMAM.jpeg" alt="QRIS Resmi Dhes Mart / Imam">
            </a>
          </div>

          <div style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
            <a href="/lentera/uploads/payment/QRIS%20IMAM.jpeg" download="QRIS_DhesMart_LENTERA.jpeg" class="btn-logins" style="font-size: 0.78rem; padding: 0.4rem 0.8rem; text-decoration: none; border: 1px solid var(--border-colors);">
              <i class="fa-solid fa-download"></i> Simpan Gambar QRIS
            </a>
            <a href="/lentera/uploads/payment/QRIS%20IMAM.jpeg" target="_blank" class="btn-logins" style="font-size: 0.78rem; padding: 0.4rem 0.8rem; text-decoration: none; border: 1px solid var(--border-colors);">
              <i class="fa-solid fa-up-right-from-square"></i> Buka Gambar Penuh
            </a>
          </div>

          <div style="font-size: 0.84rem; color: var(--text-mutes);">Total Tagihan Publikasi:</div>
          <div class="price-tag-bigs">Rp <?= number_format($invoices['amount'], 0, ',', '.'); ?></div>
          <div style="font-size: 0.8rem; color: var(--text-mutes); margin-top: 0.2rem;">
            No. Invoice: <strong style="font-family: monospace; color: var(--text-titles);"><?= htmlspecialchars($invoices['invoice_number']); ?></strong>
          </div>
        </div>

        <!-- Detail Rekening Tujuan -->
        <div style="border-top: 1px solid var(--border-colors); padding-top: 1.25rem;">
          <h4 style="font-size: 0.88rem; font-weight: 800; color: var(--text-titles); text-transform: uppercase; margin-bottom: 0.75rem;">
            <i class="fa-solid fa-circle-info" style="color: var(--teal-primarys);"></i> Petunjuk Pembayaran:
          </h4>
          <div style="background: #f1f5f9; padding: 1rem 1.15rem; border-radius: var(--radius-smalls); font-size: 0.86rem; line-height: 1.6; color: var(--text-titles);">
            <div style="margin-bottom: 0.85rem; padding-bottom: 0.85rem; border-bottom: 1px dashed #cbd5e1;">
              <strong style="color: #be123c;"><i class="fa-solid fa-qrcode"></i> Metode 1 — Scan QRIS (Semua E-Wallet & Bank):</strong><br>
              • Buka <strong>DANA, BCA, BRI, Mandiri, BNI, GoPay, OVO, ShopeePay</strong> atau m-Banking Anda.<br>
              • Arahkan scanner ke QRIS <strong>Dhes Mart</strong> di atas.<br>
              • <em>Jika menggunakan HP yang sama:</em> Klik tombol <strong>"Simpan Gambar QRIS"</strong> di atas, lalu pada scanner DANA klik ikon <strong>Galeri Foto</strong> di pojok kanan atas untuk memilih gambar QRIS.<br>
              • Masukkan nominal tepat <strong>Rp 150.000</strong> dan selesaikan pembayaran.
            </div>

            <div>
              <strong style="color: #0284c7;"><i class="fa-solid fa-mobile-screen-button"></i> Metode 2 — Transfer Langsung ke Nomor DANA:</strong><br>
              • Buka aplikasi DANA &rarr; pilih menu <strong>Kirim</strong> &rarr; <strong>Kirim ke Teman</strong>.<br>
              • Masukkan Nomor DANA Penerima:
              <div style="display: flex; align-items: center; gap: 0.5rem; margin: 0.4rem 0;">
                <span id="dana_number_display" style="font-family: monospace; font-size: 1.05rem; font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 0.25rem 0.65rem; border-radius: 4px;">087849264452</span>
                <button type="button" onclick="copy_dana_number()" id="btn_copy_dana" style="background: #0284c7; color: #fff; border: none; padding: 0.35rem 0.8rem; border-radius: 4px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                  <i class="fa-regular fa-copy"></i> Salin Nomor
                </button>
              </div>
              • Nama Akun: <strong>Imam</strong> (Pengelola Repositori UNIRA)<br>
              • Jumlah: <strong>Rp 150.000</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- SISI KANAN: FORM KONFIRMASI & AUTO-PUBLISH -->
      <div class="payment-cards" style="align-self: flex-start;">
        <span style="font-size: 0.78rem; font-weight: 800; color: #16a34a; text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-bolt"></i> Instant Auto-Publish
        </span>
        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--text-titles); margin-top: 0.35rem; margin-bottom: 0.5rem;">
          Konfirmasi Pembayaran
        </h3>
        <p style="font-size: 0.84rem; color: var(--text-bodys); line-height: 1.5; margin-bottom: 1.5rem;">
          Isi detail bukti pembayaran Anda di bawah ini. Sistem kami akan <strong>seketika menerbitkan naskah Anda ke publik secara otomatis</strong> tanpa verifikasi manual lagi.
        </p>

        <form action="payments.php?ids=<?= $work_ids; ?>" method="POST" enctype="multipart/form-data">
          <?= csrfs::inputs(); ?>

          <div class="form-groups">
            <label class="form-labels" for="sender_names">
              Nama Akun Pengirim DANA / Rekening <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" id="sender_names" name="sender_names" class="form-inputs" placeholder="Contoh: Achmad Fauzi atau Imam" value="<?= htmlspecialchars($_POST['sender_names'] ?? ''); ?>" required autofocus>
          </div>

          <div class="form-groups">
            <label class="form-labels" for="reference_numbers">
              Nomor Referensi Transaksi DANA <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" id="reference_numbers" name="reference_numbers" class="form-inputs" placeholder="Contoh: 20261022100845..." value="<?= htmlspecialchars($_POST['reference_numbers'] ?? ''); ?>" required>
            <div style="font-size: 0.76rem; color: var(--text-mutes); margin-top: 0.3rem;">
              Dapat dilihat pada menu Riwayat / Detail Transaksi di aplikasi DANA Anda.
            </div>
          </div>

          <div class="form-groups">
            <label class="form-labels" for="proof_files">
              Bukti Tangkapan Layar / Struk (Opsional)
            </label>
            <input type="file" id="proof_files" name="proof_files" class="form-inputs" accept="image/*,application/pdf" style="padding: 0.5rem;">
            <div style="font-size: 0.76rem; color: var(--text-mutes); margin-top: 0.3rem;">
              Format JPG, PNG, atau PDF (maks. 5MB).
            </div>
          </div>

          <button type="submit" class="btn-primarys btn-golds" style="width: 100%; justify-content: center; padding: 0.95rem; font-size: 0.95rem; margin-top: 1rem;">
            <i class="fa-solid fa-cloud-arrow-up"></i> Konfirmasi & Terbitkan Naskah Otomatis
          </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
          <a href="indexs.php" style="color: var(--text-mutes); font-size: 0.84rem; text-decoration: none;">
            <i class="fa-solid fa-clock"></i> Bayar nanti melalui dashboard saya
          </a>
        </div>
      </div>
    </div>
  </main>

  <script>
    function copy_dana_number() {
      const num = '087849264452';
      navigator.clipboard.writeText(num).then(() => {
        const btn = document.getElementById('btn_copy_dana');
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
        btn.style.background = '#16a34a';
        setTimeout(() => {
          btn.innerHTML = '<i class="fa-regular fa-copy"></i> Salin Nomor';
          btn.style.background = '#0284c7';
        }, 2500);
      }).catch(() => {
        alert('Nomor DANA: 087849264452');
      });
    }
  </script>
</body>
</html>
