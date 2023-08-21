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
            
            if (isset($_GET["sil_username"])) {
                $sil_username = $_GET["sil_username"];
                
                // Kullanıcıyı silme işlemi
                $sil_sql = "DELETE FROM kullanicilar WHERE kullanici_adi = '$sil_username'";
                if ($conn->query($sil_sql) === TRUE) {
                    // Üye silindi uyarısı
                    showAlert("success", "Üye Silindi");
                } else {
                    // Hata uyarısı
                    showAlert("error", "Üye silinirken bir hata oluştu: " . $conn->error);
                }
            }
            
            // Kullanıcı listesini al
            $kullanici_listesi = array();
            $listele_sql = "SELECT kullanici_adi FROM kullanicilar";
            $listele_result = $conn->query($listele_sql);
            if ($listele_result->num_rows > 0) {
                while ($row = $listele_result->fetch_assoc()) {
                    $kullanici_listesi[] = $row["kullanici_adi"];
                }
            }
            ?>

            <!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="/style/header.css"> <!-- Header stil dosyası -->
                <link rel="stylesheet" href="../style/admin-uye-sil.css"> <!-- Header stil dosyası -->
                <title>ADMİN PANEL - Üye sil</title>
            </head>
            <body>
                <div class="admin-panel" style="margin-top: 100px">
                    <h2>Üye Sil</h2>
                    <form method="get" action="">
                        <label for="sil_username">Silinecek Üye Kullanıcı Adı:</label>
                        <select name="sil_username" id="sil_username" required>
                            <option value="" selected disabled>Üye seçiniz</option>
                            <?php foreach ($kullanici_listesi as $kullanici) { ?>
                                <option value="<?php echo $kullanici; ?>"><?php echo $kullanici; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit">Üyeyi Sil</button>
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
