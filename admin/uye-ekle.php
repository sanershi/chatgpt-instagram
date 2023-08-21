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

            // Üye ekleme formunun işlenmesi
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $newUsername = $_POST["kullanici_adi"];
                $newPassword = password_hash($_POST["sifre"], PASSWORD_DEFAULT);
                $newName = $_POST["ad"];
                $newSurname = $_POST["soyad"];
                $newRole = $_POST["uyeRutbe"];

                // Veritabanına üye ekleme işlemi
                $sqlCheckUsername = "SELECT * FROM kullanicilar WHERE kullanici_adi = '$newUsername'";
                $resultCheckUsername = $conn->query($sqlCheckUsername);
            
                if ($resultCheckUsername->num_rows > 0) {
                    echo '<script>alert("Bu kullanıcı adı zaten mevcut.");</script>';
                } else {
                    // Kullanıcı adı daha önce kullanılmamışsa üyeyi ekle
                    $sqlAddUser = "INSERT INTO kullanicilar (ad, soyad, kullanici_adi, sifre, rutbe) VALUES ('$newName', '$newSurname', '$newUsername', '$newPassword', '$newRole')";
                    if ($conn->query($sqlAddUser) === TRUE) {
                        echo '<script>alert("Üye eklendi: ' . $newUsername . '");</script>';
                    } else {
                        echo '<script>alert("Üye eklenirken bir hata oluştu: ' . $conn->error . '");</script>';
                    }
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
                <title>ADMİN PANEL - Üye Ekle</title>
            </head>
            <body style="margin: 10px 10px 10px 10px;">
            <div class="admin-panel" style="margin-top: 100px">
                <h2>Üye Ekle</h2>
                <form method="post" action="">
                    <label for="kullanici_adi">Kullanıcı Adı:</label>
                    <input type="text" id="kullanici_adi" name="kullanici_adi" required><br>

                    <label for="sifre">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" required><br>

                    <label for="ad">Ad:</label>
                    <input type="text" id="ad" name="ad" required><br>

                    <label for="soyad">Soyad:</label>
                    <input type="text" id="soyad" name="soyad" required><br>

                    <label for="uyeRutbe">Rütbe:</label>
                    <select id="uyeRutbe" name="uyeRutbe">
                        <option value="Admin">Admin</option>
                        <option value="Üye">Üye</option>
                    </select>

                    <button type="submit">Ekle</button>
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
