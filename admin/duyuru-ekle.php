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

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $duyuruBaslik = htmlspecialchars($_POST["duyurubaslik"]);
                $duyuruIcerik = htmlspecialchars($_POST["duyuruicerik"]);
                $tarih = date("Y-m-d H:i");
                
                $insertDuyuruSQL = "INSERT INTO duyuru (baslik, icerik, tarih) VALUES ('$duyuruBaslik', '$duyuruIcerik', '$tarih')";
                
                if ($conn->query($insertDuyuruSQL) === TRUE) {
                    echo '<script>alert("Duyuru eklendi.");</script>';
                    setcookie("duyuruModalKapatildi", "false", time() + 3600, "/");
                } else {
                    echo '<script>alert("Duyuru eklenirken bir hata oluştu: ' . $conn->error . '");</script>';
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
                <title>ADMİN PANEL - Duyuru Ekle</title>
            </head>
            <body style="margin: 10px 10px 10px 10px;">
            <div class="admin-panel" style="margin-top: 100px">
                <h2>Duyuru Ekle</h2>
                <form method="post" action="">
                    <label for="duyurubaslik">Duyuru Başlık</label>
                    <input type="text" id="duyurubaslik" name="duyurubaslik" required><br>

                    <label for="duyuruicerik">Duyuru İçerik</label>
                    <textarea id="duyuruicerik" name="duyuruicerik" rows="10" required></textarea>


                    <button type="submit">Ekle</button>
                </form>
            </div>
            <style>
                #duyuruicerik {
                    width: 100%;
                    height: 150px; /* İstediğiniz yüksekliği ayarlayabilirsiniz */
                    resize: none;
                    margin-bottom: 10px;
                }
            </style>
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
