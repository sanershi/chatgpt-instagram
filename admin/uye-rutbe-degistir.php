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
            include("../misc/header.php");

            if (isset($_POST["kullanici_adi"]) && isset($_POST["uyeRutbe"])) {
                $kullanici_adi = $_POST["kullanici_adi"];
                $uyeRutbe = $_POST["uyeRutbe"];
                
                // Üye rütbe değiştirme işlemi
                $update_sql = "UPDATE kullanicilar SET rutbe = '$uyeRutbe' WHERE kullanici_adi = '$kullanici_adi'";
                if ($conn->query($update_sql) === TRUE) {
                    echo '<script>alert("Üyenin rütbesi değiştirildi.");</script>';
                } else {
                    echo '<script>alert("Üyenin rütbesi değiştirilirken bir hata oluştu: ' . $conn->error . '");</script>';
                }
            }
            ?>

            <!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <link rel="stylesheet" type="text/css" href="/style/alertbox.css">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="/style/header.css">
                <link rel="stylesheet" href="..//style/admin-uye-ekle.css"> 
                <title>ADMİN PANEL - Üye Rütbe Değiştir</title>
            </head>
            <body style="margin: 10px 10px 10px 10px;">
            <div class="admin-panel" style="margin-top: 100px">
                <h2>Üye Rütbe Değiştir</h2>
                <form method="post" action="">
                    <label for="kullanici_adi">Kullanıcı Adı:</label>
                    <input type="text" id="kullanici_adi" name="kullanici_adi" required><br>

                    <label for="uyeRutbe">Rütbe:</label>
                    <select id="uyeRutbe" name="uyeRutbe">
                        <option value="Admin">Admin</option>
                        <option value="Üye">Üye</option>
                    </select>

                    <button type="submit">Değiştir</button>
                </form>
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
