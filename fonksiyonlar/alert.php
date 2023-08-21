<?php

function showAlert($type, $message) {
    $class = "";
    if ($type === "hata") {
        $class = "hata";
    } elseif ($type === "uyari") {
        $class = "uyarı";
    } elseif ($type === "basarili") {
        $class = "basarili";
    } elseif ($type === "bilgi") {
        $class = "bilgi";
    }

    echo '<div class="' . $class . '">
        <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
        <strong>' . ucfirst($type) . '!</strong> ' . $message . '
    </div>';
}

?>
