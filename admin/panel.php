<?php
session_start();

if (isset($_COOKIE["kullanici_giris"])) {
    $userPassword =  $_COOKIE["kullanici_giris"];

    // Veritabanı bağlantısı
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "saner_projesi";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Veritabanı bağlantı hatası: " . $conn->connect_error);
    }

    // Kullanıcıyı veritabanından bul
    $sql = "SELECT * FROM kullanicilar WHERE sifre = '$userPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc();

        if ($userInfo["rutbe"] === "Admin" || $userInfo["rutbe"] === "Kurucu") {
        include("../fonksiyonlar/alert.php");
        include("../misc/header.php")
            ?> 
            <!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="/style/header.css"> <!-- Header stil dosyası -->
                <link rel="stylesheet" href="..//style/admin.css"> <!-- Header stil dosyası -->
                <title>ADMİN PANEL</title>
            </head>
            <body>
            <div class="admin-panel" style="margin-top: 100px">
                    <h2>Admin Panel</h2>
                    <ul>
                        <li><a href="/admin/uye-ekle.php">Üye Ekle</a></li>
                        <li><a href="/admin/uye-sil.php">Üye Sil</a></li>
                        <li><a href="/admin/uye-rutbe-degistir.php">Üye Rütbe Değiştir</a></li>
                        <li><a href="/admin/duyuru-ekle.php">Duyuru Ekle</a></li>
                        <li><a href="/admin/duyurular.php">Duyurular</a></li>
                    </ul>
                </div>
            </body>
            </html>
            <?php
        } else {
            header("Location: /index.php"); // Rütbesi uygun değilse ana sayfaya yönlendir
            exit();
        }
    } else {
        header("Location: /index.php"); // Şifre yanlışsa ana sayfaya yönlendir
        exit();
    }

    $conn->close();
} else {
    header("Location: /index.php"); // Çerez yoksa ana sayfaya yönlendir
    exit();
}
?>

