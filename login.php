<?php
session_start();
// Eğer kullanıcı zaten giriş yapmışsa admin paneline yönlendir
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: admin.php');
    exit;
}
$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error_message = "Kullanıcı adı veya şifre hatalı!";
    } elseif ($_GET['error'] == 2) {
        $error_message = "Lütfen giriş yapınız.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TANYERİ - Admin Girişi</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <style>
        :root { --primary-color: #5D2B2C; --secondary-color: #C89B4F; }
        body { font-family: "Lato", sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; align-items: center; height: 100vh; margin:0; }
        .login-container { background-color: white; padding: 30px 40px; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align:center; }
        .login-container img { max-width: 150px; margin-bottom: 20px; }
        .login-container h2 { color: var(--primary-color); margin-bottom:25px; }
        .login-container input[type="text"], .login-container input[type="password"] { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .login-container button { background-color: var(--primary-color); color: white; padding: 12px 0; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 1.1em; transition: background-color 0.3s; }
        .login-container button:hover { background-color: var(--secondary-color); color: var(--primary-color); }
        .error-message { color: red; margin-bottom: 15px; font-size:0.9em; }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="logo.png" alt="TANYERİ Logo">
        <h2>Yönetim Paneli Girişi</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="login_handler.php" method="POST">
            <div>
                <input type="text" name="username" placeholder="Kullanıcı Adı" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Şifre" required>
            </div>
            <div>
                <button type="submit">Giriş Yap</button>
            </div>
        </form>
    </div>
</body>
</html>