<?php
// Veritabanı bağlantısı için gerekli bilgileri burada ayarlayın
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "saner_projesi";

include("fonksiyonlar/alert.php");

// Veritabanı bağlantısını oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Veritabanı bağlantı hatasını kontrol et
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Çıkış işlemi sadece oturum açmış kullanıcılar için geçerli olmalı
if(isset($_COOKIE["kullanici_giris"]) == 1) {
    header("Location: /profil.php");
    exit;
}
// if (isset($_COOKIE["kullanici_giris"])) {
//     // Çerez süresini geçersiz kıl ve boş değer vererek sil
//     setcookie("kullanici_giris", "", time() - 3600, "/");

//     // Anasayfaya yönlendir
//     header("Location: /profil.php");
//     exit;
// } 

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = $_POST["ad"];
    $sifre = $_POST["sifre"];
    
    // Kullanıcının bilgilerini veritabanından al
    $sql = "SELECT id, ad, sifre FROM kullanicilar WHERE kullanici_adi = '$ad'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $kullaniciId = $row["id"];
        $kullaniciAd = $row["kullanici_adi"];
        $hashliSifre = $row["sifre"];
        
        // Kullanıcının girdiği şifreyi veritabanındaki hash ile karşılaştır
        if (password_verify($sifre, $hashliSifre)) {
            // Giriş başarılı, kullanıcıyı profil sayfasına yönlendir
            setcookie("kullanici_giris", $hashliSifre, time() + (86400 * 30), "/"); // Örnek: 30 gün süreyle çerez oluşturuluyor
            header("Location: /profil.php");
            exit;
        } else {
            showAlert("hata", "Hatalı şifre");
        }
    } else {
        showAlert("hata", "Böyle bir kullanıcı bulunamadı.");
    }
}

// Veritabanı bağlantısını kapat
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Giriş Yap</title>
    <link rel="stylesheet" type="text/css" href="style/g-k.css">
    <link rel="stylesheet" type="text/css" href="style/alertbox.css">
</head>
<body>
    <div class="container">
        <h2>Giriş Yap</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="ad">Ad:</label>
                <input type="text" id="ad" name="ad" required>
            </div>
            
            <div class="form-group">
                <label for="sifre">Şifre:</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>
            
            <div>
                <input type="submit" value="Giriş Yap">
                <a href="/kayit.php">Kayıt ol</a>
            </div>
        </form>
    </div>
    <script src="javascript/alert.js"></script>
</body>
</html>
