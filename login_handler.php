<?php
session_start();

// ---- GÜVENLİK UYARISI: BU BİLGİLERİ ASLA CANLI SİTEDE BU ŞEKİLDE KULLANMAYIN! ----
// Gerçek bir uygulamada kullanıcı adı ve şifre veritabanında güvenli bir şekilde saklanmalıdır.
// Şifreler her zaman hash'lenerek (örn: password_hash()) saklanmalı ve password_verify() ile kontrol edilmelidir.
define('ADMIN_USERNAME', 'admin'); // Kullanıcı adınızı buraya yazın
define('ADMIN_PASSWORD', 'TanyeriKAFE2025!'); // Güçlü bir şifre belirleyin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // Giriş başarılı
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username; // İsteğe bağlı olarak kullanıcı adını da saklayabilirsiniz
        header('Location: admin.php'); // admin.php'ye yönlendir (önceden admin.html idi)
        exit;
    } else {
        // Giriş başarısız
        header('Location: login.php?error=1'); // Hata koduyla giriş sayfasına geri yönlendir
        exit;
    }
} else {
    // POST isteği değilse, direkt erişimi engelle ve giriş sayfasına yönlendir
    header('Location: login.php');
    exit;
}
?>