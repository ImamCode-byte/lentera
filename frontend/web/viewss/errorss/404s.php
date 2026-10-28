<?php
/**
 * 404s.php
 * Halaman Galat 404 (Halaman Tidak Ditemukan) Resmi Lentera
 */
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — Halaman Tidak Ditemukan | Lentera Universitas Madura</title>
  <link rel="stylesheet" href="/lentera/csss/styles.css?vs=1.0.1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .error-containers {
      min-height: 100vh;
      min-height: 100dvh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.5rem;
      background: radial-gradient(circle at 10% 20%, rgba(15, 76, 92, 0.08) 0%, transparent 40%),
                  radial-gradient(circle at 90% 80%, rgba(251, 139, 36, 0.08) 0%, transparent 40%),
                  #f8fafc;
      text-align: center;
    }
    .error-cards {
      background: #ffffff;
      border-radius: var(--radius-larges, 16px);
      padding: 3.5rem 2.5rem;
      max-width: 540px;
      width: 100%;
      border: 1px solid var(--border-colors, #e2e8f0);
      box-shadow: 0 10px 25px -5px rgba(15, 76, 92, 0.1);
    }
    .error-codes {
      font-size: 5.5rem;
      font-weight: 800;
      color: var(--gold-accents, #fb8b24);
      line-height: 1;
      margin-bottom: 1rem;
      letter-spacing: -2px;
    }
    .error-titles {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--teal-primarys, #0f4c5c);
      margin-bottom: 1rem;
    }
    .error-descs {
      color: var(--text-bodys, #475569);
      font-size: 0.95rem;
      line-height: 1.6;
      margin-bottom: 2rem;
    }
    .error-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      flex-wrap: wrap;
    }
  </style>
</head>
<body>

  <div class="error-containers">
    <div class="error-cards">
      <div style="width: 60px; height: 60px; background: rgba(251, 139, 36, 0.12); color: var(--gold-accents, #fb8b24); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.5rem;">
        <i class="fa-solid fa-compass"></i>
      </div>
      <div class="error-codes">404</div>
      <h1 class="error-titles">Halaman Tidak Ditemukan</h1>
      <p class="error-descs">
        Tautan naskah atau alamat URL yang Anda tuju mungkin telah dipindahkan, dihapus, atau belum pernah tersedia di repositori Lentera.
      </p>
      <div class="error-actions">
        <a href="/lentera/" class="btn-primarys" style="padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 8px; font-weight: 700;">
          <i class="fa-solid fa-house"></i> Kembali ke Beranda
        </a>
        <a href="/lentera/viewss/searchs/indexs.php" class="btn-logins" style="padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 8px; font-weight: 700;">
          <i class="fa-solid fa-magnifying-glass"></i> Cari Naskah Ilmiah
        </a>
      </div>
    </div>
  </div>

</body>
</html>
