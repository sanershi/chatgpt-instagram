<?php
// Veritabanı bağlantısı ve gerekli işlemler

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


include("fonksiyonlar/alert.php");
include("fonksiyonlar/username.php");

if (isset($_COOKIE["kullanici_giris"])) {
    $sifre = $_COOKIE["kullanici_giris"];

    // Kullanıcının bilgilerini veritabanından al
    $sql = "SELECT ad, soyad, profil_resmi, kullanici_adi, rutbe FROM kullanicilar WHERE sifre = '$sifre'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ad = $row["ad"];
        $soyad = $row["soyad"];
        $profilResmi = $row["profil_resmi"];
        $kullaniciAd = $row["kullanici_adi"];
        $rutbe = $row["rutbe"];
    } else {
        header("Location: /index.php");
        exit;
    }
} else {
    header("Location: /index.php");
    exit;
}
?>
<?php include("misc/header.php"); ?>

<html lang="tr">
<head>
    <link rel="stylesheet" type="text/css" href="style/profil.css">
    <title>@<?php echo $kullaniciAd; ?></title>
</head>
<body class="body" style="margin: 10px 10px 10px 10px;">
    <div class="profile-container">
        <div class="profile">
            <div class="prof">
                <div class="text">
                <div class="profile-picture">
                    <img style="height: 300px; width: 300px;" src="<?php echo $profilResmi; ?>" alt="Profil Resmi">
                </div>
                    <h1 class="profile-name"><?php echo $ad . ' ' . $soyad; ?></h1>
                    <p class="profile-username">@<span class="<?php echo strtolower($rutbe); ?>"><?php echo $kullaniciAd; ?></span></p>
                    <div class="other" style="padding-top: 15px !important;">
                        <a href="/profilayarlari.php" class="button">Profil Ayarları</a>
                        <a href="/cikis.php" class="button">Çıkış yap</a>
                    </div>
                </div>
            </div>

            <!-- <div class="settings">
                <a href="#">Profil Ayarları</a>
            </div> -->
            <div class="banner">
            </div>
        </div>
    </div>
    <style> 
        .üye {
            color: #999 !important;
        }

        .kurucu {
            color: red !important;
            font-weight: bold;
        }

        .admin {
            color: orange !important;
        }
    </style>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
?>
