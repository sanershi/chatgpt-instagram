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

include("misc/header.php");

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
    }
} else {
    header("Location: /index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aciklama = $_POST["aciklama"];
    
    // Resim yükleme işlemleri (bu kısmı özelleştirin)
    $resimYolu = "img/" . $_FILES["resim"]["name"];
    move_uploaded_file($_FILES["resim"]["tmp_name"], $resimYolu);
    
    $tarih = date("Y-m-d H:i");

    $sql = "SELECT ad, soyad, profil_resmi, kullanici_adi, rutbe FROM kullanicilar WHERE sifre = '$sifre'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $id = $row["kullanici_adi"];

    // Postu veritabanına ekle
    $insertSQL = "INSERT INTO post (kullanici_adi, image, description, created_at) VALUES ('$id', '$resimYolu', '$aciklama', '$tarih')";
    if ($conn->query($insertSQL) === TRUE) {
        echo '<script>alert("Gönderi başarıyla eklendi.");</script>';
    } else {
        echo '<script>alert("Gönderi eklenirken bir hata oluştu: ' . $conn->error . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>POST - Gönderi Ekle</title>
</head>
<body class="body" style="margin: 10px 10px 10px 10px;">

    <div class="post-form">
        <h2>Gönderi Ekle</h2>
        <form method="POST" enctype="multipart/form-data">
            
            <label for="aciklama">Açıklama:</label>
            <textarea id="aciklama" name="aciklama" rows="4" required></textarea><br>
            
            <label for="resim">Resim Seçin:</label>
            <input type="file" id="resim" name="resim" accept="image/*" required><br>
            
            <button type="submit" id="button">Gönderi Ekle</button>
        </form>
    </div> <!-- Özel JavaScript dosyanızı ekleyin -->
    <style>
        .body {
            font-family: Arial, sans-serif;
        }
        /* Gönderi ekle formu stil */
        .post-form {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .post-form h2 {
        margin-top: 0;
        }

        .post-form label {
        display: block;
        margin-top: 10px;
        }

        .post-form input,
        .post-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
        }

        .post-form button {
        display: block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        }

        .post-form button:hover {
        background-color: #0056b3;
        }

    </style>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
?>
