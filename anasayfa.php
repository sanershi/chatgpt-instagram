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
    }
} else {
    header("Location: /index.php");
}

$postSQL = "SELECT * FROM post ORDER BY created_at DESC";
$postResult = $conn->query($postSQL);
$duyuruSQL = "SELECT * FROM duyuru";
$duyuruResult = $conn->query($duyuruSQL);
?>
<?php include("misc/header.php"); ?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>POST - Anasayfa</title>
    <link rel="stylesheet" type="text/css" href="style.css"> <!-- Özel stil dosyanızı ekleyin -->
</head>
<body class="body" style="margin: 10px 10px 10px 10px;"> 

<div class="post-container">
    <?php
    while ($row = $postResult->fetch_assoc()) {
        $postId = $row["id"];
        $kullaniciId = $row["kullanici_adi"];
        $aciklama = $row["description"];
        $resimYolu = $row["image"];
        $tarih = $row["created_at"];

        $userSQL = "SELECT kullanici_adi, profil_resmi FROM kullanicilar WHERE kullanici_adi = '$kullaniciId'";
        $userResult = $conn->query($userSQL);
        $userRow = $userResult->fetch_assoc();
        $pp = $userRow["profil_resmi"];

        echo '<div class="post">';
        echo '<div class="post-user-info">';
        echo '<span class="username" style="display: flex; text-align: center; align-items: center;"><img src="' . $pp . '" alt="Profil Resmi" class="profile-picture">' . $kullaniciId . '</span>';
        echo '</div>';
        echo '<img src="' . $resimYolu . '" alt="Gönderi Resmi" class="post-image">';
        echo '<div class="post-description"><strong>' . $kullaniciAd . '</strong> ' . $aciklama . '<br><span class="time">' . $tarih . '</span></div>';
        echo '</div>';
    }
    ?>
    
        <div id="duyuruModal" class="modal">
            <div class="modal-content">
            <span class="close">&times;</span>
            <h1 style="color: red; font-weight: bold; margin-bottom: 50px; font-size: 55px;">DUYURU!</h1>
            <?php while ($row = $duyuruResult->fetch_assoc()) { ?>
                <h2 class="baslik"><strong><?= $row["baslik"] ?></strong></h2>
                <span><?= $row["icerik"] ?></span>
                <br>
                <br>
                <br>
            <?php } ?>
            </div>
        </div>
    <script>
                const duyuruModal = document.getElementById("duyuruModal");
                const modalClose = document.querySelector(".modal-content .close");
                const modalBaslik = document.getElementById("duyuru-baslik");
                const modalIcerik = document.getElementById("duyuru-icerik");
                
                window.addEventListener("load", function() {
                        duyuruModal.style.display = "block";
                });
                
                modalClose.addEventListener("click", function() {
                    duyuruModal.style.display = "none";
                });
                
                window.addEventListener("click", function(event) {
                    if (event.target === duyuruModal) {
                        duyuruModal.style.display = "none";
                    }
                    // Modalı kapat ve çerez (cookie) oluştur
                });
            </script>
    <style>

                .modal {
                    display: none;
                    position: fixed;
                    z-index: 1;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.4);
                    overflow: auto;
                }
                
                .modal-content {
                    background-color: #fefefe;
                    margin: 10% auto;
                    padding: 20px;
                    border: 1px solid #ccc;
                    width: 60%;
                    border-radius: 5px;
                    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.2);
                }
                
                .close {
                    color: #aaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }
                
                .close:hover {
                    color: #000;
                }
                
                .modal-content h2 {
                    margin-top: 0;
                    margin-bottom: 0;
                    font-size: 50px;
                }
                
                .modal-content label {
                    display: block;
                }
                
                .modal-content input,
                .modal-content textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ccc;
                    border-radius: 3px;
                }
                
                .modal-content button {
                    display: block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: #fff;
                    border: none;
                    border-radius: 3px;
                    cursor: pointer;
                }
                
                .modal-content button:hover {
                    background-color: #0056b3;
                }






/* Gönderi stilleri */
.post-container {
    max-width: 800px;
    margin: 20px auto;
    font-family: Arial, sans-serif;
}

.post {
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 20px;
    background-color: white;
}

.post-header {
    display: flex;
    align-items: center;
    padding: 10px;
}

.profile-picture {
    width: 40px;
    height: 40px;
    margin-right: 10px;
    border-radius: 50%;
}

.post-user-info {
    display: flex;
    flex-direction: column;
}

.username {
    font-weight: bold;
}

.time {
    color: #999;
}

.post-image {
    max-width: 100%;
    height: 50%;
    display: block;
    margin: 0 auto; /* Resmi yatayda ortala */
    width: 500px; /* İstediğiniz genişliği burada belirleyebilirsiniz */
}

.post-actions {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-top: 1px solid #ddd;
}

.like-button, .comment-button {
    border: none;
    background-color: transparent;
    cursor: pointer;
    color: #333;
}

.post-description {
    padding: 10px;
}

.post-user-info {
    border-bottom: 1px solid #ddd;
}


    </style>

</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
?>