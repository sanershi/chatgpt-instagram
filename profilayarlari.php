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
    $sql = "SELECT ad, soyad, profil_resmi, kullanici_adi FROM kullanicilar WHERE sifre = '$sifre'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ad = $row["ad"];
        $soyad = $row["soyad"];
        $profilResmi = $row["profil_resmi"];
        $kullaniciAd = $row["kullanici_adi"];
    } else {
        header("Location: /index.php");
    }
} else {
    // Kullanıcı adının varlığını kontrol et
    if (empty($kullaniciAd)) {
        // Yeni bir kullanıcı adı oluştur ve kaydet
        $newUsername = generateRandomUsername($conn);
        if ($newUsername !== false) {
            // Yeni kullanıcı adını veritabanına kaydet
            $sql = "INSERT INTO kullanicilar (kullanici_adi) VALUES ('$newUsername')";
            if ($conn->query($sql) === TRUE) {
                $kullaniciAd = $newUsername; // Yeni kullanıcı adını atayın
            } else {
                header("Location: /index.php");
            }
        } else {
            header("Location: /index.php");
        }
    }
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newAd = isset($_POST["new_ad"]) ? htmlspecialchars($_POST["new_ad"]) : $ad;
        $newSoyad = isset($_POST["new_soyad"]) ? htmlspecialchars($_POST["new_soyad"]) : $soyad;
        $newKullaniciAd = isset($_POST["new_kullanici_adi"]) ? htmlspecialchars($_POST["new_kullanici_adi"]) : $kullaniciAd;
        // Sadece profil fotoğrafını güncelleme
        if (!empty($_FILES["new_profil_resmi"]["name"])) {
            $targetDirectory = "img/"; // Profil resimlerinin yükleneceği klasör yolu
            $targetFile = $targetDirectory . basename($_FILES["new_profil_resmi"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Resim dosyasının gerçek bir resim olup olmadığını kontrol et
            $check = getimagesize($_FILES["new_profil_resmi"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                showAlert("hata", "Dosya resim formatında değil.");
                $uploadOk = 0;
            }

            // Dosya boyutunu kontrol et (örneğin 5 MB)
            if ($_FILES["new_profil_resmi"]["size"] > 5 * 1024 * 1024) {
                showAlert("hata", "Üzgünüz, dosya boyutu çok büyük.");
                $uploadOk = 0;
            }

            // Belirli dosya tiplerine izin vermek isteyebilirsiniz
            if (
                $imageFileType != "jpg" && $imageFileType != "jpeg" && 
                $imageFileType != "png"
            ) {
                showAlert("hata", "Üzgünüz, sadece JPG, JPEG, PNG dosyalarına izin verilir.");
                $uploadOk = 0;
            }

            // Tüm kontrollerden geçtiyse dosyayı yükle
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["new_profil_resmi"]["tmp_name"], $targetFile)) {
                    // Yükleme başarılı, dosya yolunu kaydet
                    $newProfilResmi = $targetFile;

                    // Veritabanında profil resmini güncelleme
                    $sql = "UPDATE kullanicilar SET profil_resmi = '$newProfilResmi' WHERE kullanici_adi = '$kullaniciAd'";
                    if ($conn->query($sql) !== TRUE) {
                        showAlert("hata", "Profil resmi güncelleme hatası: " . $conn->error);
                    }
                } else {
                    showAlert("hata", "Üzgünüz, dosya yüklenirken bir hata oluştu.");
                }
            }
        }
    
        // Sadece kullanıcı adını güncelleme
        if (!empty($newKullaniciAd) && $newKullaniciAd != $kullaniciAd) {
            // ... Kullanıcı adını güncelleme işlemleri ...
    
            // Veritabanında kullanıcı adını güncelleme
            $stmt = $conn->prepare("UPDATE kullanicilar SET kullanici_adi = ? WHERE kullanici_adi = ?");
            $stmt->bind_param("ss", $newKullaniciAd, $kullaniciAd);
            if ($stmt->execute()) {
                $kullaniciAd = $newKullaniciAd; // Yeni kullanıcı adını güncelle
            } else {
                showAlert("hata", "Kullanıcı adı güncelleme hatası: " . $stmt->error);
            }
            $stmt->close();
        }
        // Ad ve soyad güncelleme
        if (!empty($newAd) || !empty($newSoyad)) {
            // Ad ve soyad güncelleme işlemleri
            // ...
    
            // Veritabanında ad ve soyad güncelleme
            $sql = "UPDATE kullanicilar SET ad = '$newAd', soyad = '$newSoyad' WHERE kullanici_adi = '$kullaniciAd'";
            if ($conn->query($sql) !== TRUE) {
                alert("hata", "Ad soyad değiştirme hatası: " . $conn->error);
            }
        }
        if (empty($newSoyad)) {
            // Ad ve soyad güncelleme işlemleri
            // ...
    
            // Veritabanında ad ve soyad güncelleme
            $sql = "UPDATE kullanicilar SET ad = '$newAd' WHERE kullanici_adi = '$kullaniciAd'";
            if ($conn->query($sql) !== TRUE) {
                alert("hata", "Ad soyad değiştirme hatası: " . $conn->error);
            }
        }

        if (empty($newAd)) {
            // Ad ve soyad güncelleme işlemleri
            // ...
    
            // Veritabanında ad ve soyad güncelleme
            $sql = "UPDATE kullanicilar SET soyad = '$newSoyad' WHERE kullanici_adi = '$kullaniciAd'";
            if ($conn->query($sql) !== TRUE) {
                alert("hata", "Ad soyad değiştirme hatası: " . $conn->error);
            }
        }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <title>@<?php echo $kullaniciAd; ?></title>
    <link rel="stylesheet" type="text/css" href="style/profilayarlari.css">
    <link rel="stylesheet" type="text/css" href="style/alertbox.css">
</head>
<body>
    <div class="center-container">
        <div class="center-content">
            <header>
                <?php include("misc/header.php"); ?>
            </header>
            <form action="profilayarlari.php" method="post" enctype="multipart/form-data">
                <!-- Profil fotoğrafı yükleme alanı -->
                <input type="file" name="new_profil_resmi">
                <!-- Diğer düzenleme alanları (ad, soyad, kullanıcı adı) -->
                <input type="text" name="new_ad" value="<?php echo $ad; ?>">
                <input type="text" name="new_soyad" value="<?php echo $soyad; ?>">
                <!-- Diğer düzenleme alanlarını da ekleyin -->
                <input type="text" name="new_kullanici_adi" value="<?php echo $kullaniciAd; ?>">
                <button type="submit">Kaydet</button>
            </form>
            
            <!-- Diğer içerikler burada yer alır -->
        </div>
    </div>
</body>
</html>

    <style>
        /* Genel stil ayarları */
.body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.center-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.center-content {
    width: 400px;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.container {
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 24px;
    margin-bottom: 20px;
}


form {
    margin-top: 20px;
}

input[type="text"], input[type="file"] {
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button[type="submit"] {
    padding: 10px 20px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.alert {
    padding: 10px;
    background-color: #f44336;
    color: white;
    margin-bottom: 10px;
    border-radius: 4px;
}


/* Diğer özel stillemeleri buraya ekleyebilirsiniz */

    </style>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
?>
