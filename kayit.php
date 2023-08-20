<?php
// Veritabanı bağlantısı için gerekli bilgileri burada ayarlayın
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



if(isset($_COOKIE["kullanici_giris"]) == 1) {
    header("Location: /profil.php");
    exit;
}
include("fonksiyonlar/alert.php");
include("fonksiyonlar/username.php");

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "SELECT kullanici_adi FROM kullanicilar WHERE kullanici_adi = '$username'";
    $result = $conn->query($sql);
    if (isset($result) === 1) {
        $ad = $_POST["ad"];
        $soyad = $_POST["soyad"];
        $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT); // Şifreyi hashle
        $username = $_POST["kullaniciadi"];
        
        $allowedTime = 5;

        // Aynı IP'den izin verilen süre içinde yapılabilecek maksimum istek sayısı
        $maxRequests = 5;

        // İstekleri takip edeceğimiz dizi
        $ipRequests = array();

        // Kullanıcının IP adresini al
        $clientIP = $_SERVER["REMOTE_ADDR"];

        // Kullanıcının IP'si zaten kaydedilmiş mi diye kontrol et
        if (isset($ipRequests[$clientIP])) {
            // Kaydedilen istek zamanlarını al
            $requestTimes = $ipRequests[$clientIP];
            
            // Geçerli zamanı al
            $currentTime = time();
            
            // İstenen süre içinde yapılan istek sayısını kontrol et
            $count = 0;
            foreach ($requestTimes as $time) {
                if ($currentTime - $time <= $allowedTime) {
                    $count++;
                }
            }
            
            // Maksimum istek sayısını aştıysa hata ver
            if ($count >= $maxRequests) {
                die("Çok fazla istek gönderdiniz. Lütfen biraz bekleyin.");
            }
        }

        // İstek zamanlarını güncelle
        $ipRequests[$clientIP][] = time();

        // Kullanıcının IP adresini al
        $ip_adresi = $_SERVER["REMOTE_ADDR"];
        // Kullanıcının adını, soyadını, şifresini ve IP adresini veritabanına kaydet
        $sql = "INSERT INTO kullanicilar (ad, soyad, sifre, ip, profil_resmi, kullanici_adi) VALUES ('$ad', '$soyad', '$sifre', '$ip_adresi', 'https://creazilla-store.fra1.digitaloceanspaces.com/icons/7916016/basic-icon-md.png', '$kullaniciadi')";
        
        $ipRequests[$clientIP] = array_slice($ipRequests[$clientIP], -$maxRequests);
        if ($conn->query($sql) === TRUE) {
            // Çerez oluştur ve kullanıcıyı giriş yapmış gibi işaretle
            setcookie("kullanici_giris", $sifre, time() + 3600, "/"); // 1 saat geçerli
            
            // Yeni kayıt başarıyla eklendi, kullanıcı profil sayfasına yönlendir
            $kullanici_id = $conn->insert_id;
            header("Location: /profil.php");
            exit;
        } else {
            // echo "Hata: " . $sql . "<br>" . $conn->error;
            showAlert("hata", $sql);
        }
    } else {
        $ad = htmlspecialchars($_POST["ad"]);
        $soyad = htmlspecialchars($_POST["soyad"]);
        $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT); // Şifreyi hashle
        $kullaniciadi = generateRandomUsername($conn);
        
        // Kullanıcının IP adresini al
        $ip_adresi = $_SERVER["REMOTE_ADDR"];
        // Kullanıcının adını, soyadını, şifresini ve IP adresini veritabanına kaydet
        $sql = "INSERT INTO kullanicilar (ad, soyad, sifre, ip, profil_resmi, kullanici_adi) VALUES ('$ad', '$soyad', '$sifre', '$ip_adresi', 'https://creazilla-store.fra1.digitaloceanspaces.com/icons/7916016/basic-icon-md.png', '$kullaniciadi')";
        
        if ($conn->query($sql) === TRUE) {
            // Çerez oluştur ve kullanıcıyı giriş yapmış gibi işaretle
            setcookie("kullanici_giris", $sifre, time() + 3600, "/"); // 1 saat geçerli
            
            // Yeni kayıt başarıyla eklendi, kullanıcı profil sayfasına yönlendir
            $kullanici_id = $conn->insert_id;
            header("Location: /profil.php");
            exit;
        } else {
            // echo "Hata: " . $sql . "<br>" . $conn->error;
            showAlert("hata", $sql);
        }
    }
}



// Veritabanı bağlantısını kapat
$conn->close();
?>



<!DOCTYPE html>
<html>
<head>
    <title>Profil Oluşturma</title>
    <link rel="stylesheet" type="text/css" href="style/g-k.css">
    <link rel="stylesheet" type="text/css" href="style/alertbox.css">
</head>
<body>
    <div class="container">
        <h2>Profil Oluşturma</h2>
        <form method="post" action="">

            <div class="form-group">
                <label for="ad">Adınız:</label>
                <input type="text" id="ad" name="ad" required placeholder="Adınızı yazını"><br>
            </div>

            <div class="form-group">
                <label for="soyad">Soyad:</label>
                <input type="text" id="soyad" name="soyad" required placeholder="Soyadınızı yazını"><br>
            </div>

            <div class="form-group">
                <label for="kullaniciadi">Kullanıcı adı:</label>
                <input type="text" id="kullaniciadi" name="kullaniciadi" required placeholder="Kullanıcı adınızı yazın"><br>
            </div>
            
           <div class="form-group">
                <label for="sifre">Şifre:</label>
                <input type="password" id="sifre" name="sifre" required placeholder="Şifrenizi yazını"><br><br>
           </div>
            
            <input type="submit" value="Profil Oluştur">
            <a href="/giris.php">Giriş yap</a>
        </form>
    </div>
    <script src="javascript/alert.js"></>

</body>
</html>

