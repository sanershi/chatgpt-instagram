<?php
// Çıkış işlemi sadece oturum açmış kullanıcılar için geçerli olmalı
if (isset($_COOKIE["kullanici_giris"])) {
    // Çerez süresini geçersiz kıl ve boş değer vererek sil
    setcookie("kullanici_giris", "", time() - 3600, "/");

    // Anasayfaya yönlendir
    header("Location: /index.php");
    exit;
} else {
    // Zaten çıkış yapmış kullanıcıları tekrar çıkış yapmaya yönlendirme
    header("Location: /index.php");
    exit;
}
?>
