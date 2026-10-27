<?php
// Script untuk menguji login ke OJS omahtabing dengan CSRF dan Cookie Session
$cookie_file = __DIR__ . '/ojs_cookie.txt';

// 1. Ambil halaman login KNJ
$ch = curl_init('https://ejournal.omahtabing.com/knj/id/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$html = curl_exec($ch);
curl_close($ch);

// Ekstrak csrfToken
preg_match('/name="csrfToken" value="([^"]+)"/', $html, $matches);
$token = $matches[1] ?? '';

echo "CSRF Token KNJ: " . $token . "<br>";

if ($token) {
    // 2. Submit Login KNJ
    $ch2 = curl_init('https://ejournal.omahtabing.com/knj/id/login/signIn');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query([
        'csrfToken' => $token,
        'username'  => 'imam_hambali_001',
        'password'  => 'imam_hambali_001',
        'remember'  => 1
    ]));
    curl_setopt($ch2, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch2, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $login_result = curl_exec($ch2);
    $final_url = curl_getinfo($ch2, CURLINFO_EFFECTIVE_URL);
    curl_close($ch2);

    echo "Final URL KNJ: " . $final_url . "<br>";
    if (strpos($login_result, 'pkp_form_error') !== false) {
        echo "KNJ Login Result: <strong>Gagal (Invalid credentials)</strong><br>";
        preg_match('/<div class="pkp_form_error">(.*?)<\/div>/s', $login_result, $err);
        echo "Pesan Error: " . strip_tags($err[1] ?? '') . "<br>";
    } else {
        echo "KNJ Login Result: <strong>BERHASIL!</strong><br>";
        file_put_contents(__DIR__ . '/knj_dashboard.html', $login_result);
    }
}

// 3. Coba juga di portal induk https://ejournal.omahtabing.com/index/id/login
$ch3 = curl_init('https://ejournal.omahtabing.com/index/id/login');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_COOKIEJAR, $cookie_file);
curl_setopt($ch3, CURLOPT_COOKIEFILE, $cookie_file);
curl_setopt($ch3, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch3, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$html_index = curl_exec($ch3);
curl_close($ch3);

preg_match('/name="csrfToken" value="([^"]+)"/', $html_index, $matches_index);
$token_index = $matches_index[1] ?? '';

echo "<hr>CSRF Token Index: " . $token_index . "<br>";

if ($token_index) {
    $ch4 = curl_init('https://ejournal.omahtabing.com/index/id/login/signIn');
    curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch4, CURLOPT_POST, true);
    curl_setopt($ch4, CURLOPT_POSTFIELDS, http_build_query([
        'csrfToken' => $token_index,
        'username'  => 'imam_hambali_001',
        'password'  => 'imam_hambali_001',
        'remember'  => 1
    ]));
    curl_setopt($ch4, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch4, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch4, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch4, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $login_result_index = curl_exec($ch4);
    $final_url_index = curl_getinfo($ch4, CURLINFO_EFFECTIVE_URL);
    curl_close($ch4);

    echo "Final URL Index: " . $final_url_index . "<br>";
    if (strpos($login_result_index, 'pkp_form_error') !== false) {
        echo "Index Login Result: <strong>Gagal (Invalid credentials)</strong><br>";
        preg_match('/<div class="pkp_form_error">(.*?)<\/div>/s', $login_result_index, $err_index);
        echo "Pesan Error: " . strip_tags($err_index[1] ?? '') . "<br>";
    } else {
        echo "Index Login Result: <strong>BERHASIL!</strong><br>";
        file_put_contents(__DIR__ . '/index_dashboard.html', $login_result_index);
    }
}
