<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saner_projesi";

// Veritabanı bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Veritabanı bağlantı hatasını kontrol et
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

if (isset($_COOKIE["kullanici_giris"])) {
    $kullaniciAd = $_COOKIE["kullanici_giris"];
    // Tam URL kullanımına dikkat edin ve düzenlenmiş URL'yi oluşturun
    $profileURL = "http://localhost/profil.php";
    header("Location: $profileURL");
    exit;
} else {
    // Kullanıcı giriş yapmamış, giriş sayfasına yönlendir
    header("Location: http://localhost/giris.php");
    exit;
}

?>