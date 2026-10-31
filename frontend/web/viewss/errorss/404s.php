<?php
/**
 * 404s.php
 * Halaman Galat 404 (Halaman Tidak Ditemukan) Resmi Lentera Universitas Madura
 */
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — Halaman Tidak Ditemukan | Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="../../csss/styles.css?vs=1.0.2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .error-containers {
      min-height: 100vh;
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem 1.25rem;
      background: radial-gradient(circle at 10% 20%, rgba(15, 76, 92, 0.08) 0%, transparent 45%),
                  radial-gradient(circle at 90% 80%, rgba(217, 119, 6, 0.08) 0%, transparent 45%),
                  var(--surface-canvass, #f8fafc);
      text-align: center;
    }
    .error-cards {
      background: #ffffff;
      border-radius: var(--radius-larges, 16px);
      padding: 3.5rem 2.25rem;
      max-width: 520px;
      width: 100%;
      border: 1px solid var(--border-colors, #e2e8f0);
      box-shadow: 0 12px 32px rgba(15, 76, 92, 0.08);
      box-sizing: border-box;
    }
    .error-icons-frame {
      width: 64px;
      height: 64px;
      background: #fef3c7;
      color: var(--gold-accents, #d97706);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.85rem;
      margin: 0 auto 1.5rem;
    }
    .error-codes {
      font-size: 5rem;
      font-weight: 800;
      color: var(--gold-accents, #d97706);
      line-height: 1;
      margin-bottom: 0.75rem;
      letter-spacing: -2px;
    }
    .error-titles {
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--teal-primarys, #0f4c5c);
      margin-bottom: 0.75rem;
      letter-spacing: -0.4px;
    }
    .error-descs {
      color: var(--text-bodys, #475569);
      font-size: 0.92rem;
      line-height: 1.6;
      margin: 0 0 2rem 0;
    }
    .error-actions {
      display: flex;
      gap: 0.85rem;
      justify-content: center;
      flex-wrap: wrap;
    }
    @media (max-width: 480px) {
      .error-cards {
        padding: 2.25rem 1.5rem;
      }
      .error-actions a {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>

  <div class="error-containers">
    <div class="error-cards">
      <div class="error-icons-frame">
        <i class="fa-solid fa-compass"></i>
      </div>
      <div class="error-codes">404</div>
      <h1 class="error-titles">Halaman Tidak Ditemukan</h1>
      <p class="error-descs">
        Tautan naskah atau halaman repositori yang Anda tuju mungkin telah dipindahkan, diperbarui, atau belum pernah tersedia di Lentera Universitas Madura.
      </p>
      <div class="error-actions">
        <a href="../../indexs.php" class="btn-primarys">
          <i class="fa-solid fa-house"></i> Kembali ke Beranda
        </a>
        <a href="../searchs/indexs.php" class="btn-logins">
          <i class="fa-solid fa-magnifying-glass"></i> Cari Naskah Ilmiah
        </a>
      </div>
    </div>
  </div>

</body>
</html>
