<?php
// XSS açıklarını kapatan fonksiyon
function secureInput($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Güvenli bir şekilde SQL sorgularını çalıştıran fonksiyon
function runSafeQuery($conn, $sql, $params = [], $types = "") {
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        return false; // Sorgu hazırlanamadı
    }

    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    if ($stmt->execute()) {
        return true; // Sorgu başarıyla çalıştı
    } else {
        return false; // Sorgu çalıştırılamadı
    }

    $stmt->close();
}

// Otomatik yönlendirmeyi engelleyen fonksiyon
function preventAutoRedirect() {
    header("Refresh: 0;"); // Tarayıcıya 0 saniye süresince sayfayı yenilemesini söyle
    echo '<script type="text/javascript">window.stop();</script>'; // JavaScript ile sayfa yükleme işlemini durdur
    exit;
}

?>
