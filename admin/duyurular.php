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
            
            // Duyuruları veritabanından çekme
            $duyuruSQL = "SELECT * FROM duyuru";
            $duyuruResult = $conn->query($duyuruSQL);
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST["duyuruId"])) {
                    $duyuruId = $_POST["duyuruId"];
                    $duyuruBaslik = $_POST["duyuruBaslik"];
                    $duyuruIcerik = $_POST["duyuruIcerik"];

                    // Duyuru güncelleme işlemi
                    $updateDuyuruSQL = "UPDATE duyuru SET baslik = '$duyuruBaslik', icerik = '$duyuruIcerik' WHERE id = '$duyuruId'";
                    if ($conn->query($updateDuyuruSQL) === TRUE) {
                        echo '<script>alert("Duyuru güncellendi.");</script>';
                    } else {
                        echo '<script>alert("Duyuru güncellenirken bir hata oluştu: ' . $conn->error . '");</script>';
                    }
                }
            }

            if (htmlspecialchars(isset($_GET["sil"]))) {
                $duyuruId = htmlspecialchars($_GET["sil"]);

                // Duyuru silme işlemi
                $deleteDuyuruSQL = "DELETE FROM duyuru WHERE id = '$duyuruId'";
                if ($conn->query($deleteDuyuruSQL) === TRUE) {
                    echo '<script>alert("Duyuru silindi.");</script>';
                } else {
                    echo '<script>alert("Duyuru silinirken bir hata oluştu: ' . $conn->error . '");</script>';
                }
            }

            ?>
            <!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <link rel="stylesheet" type="text/css" href="/style/alertbox.css">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link rel="stylesheet" href="/style/header.css">
                <link rel="stylesheet" href="..//style/admin-uye-ekle.css"> 
                <title>ADMİN PANEL - Duyuru Ekle</title>
            </head>
            <body style="margin: 10px 10px 10px 10px;">
            <div class="admin-panel" style="margin-top: 100px">
                <h2>Duyurular</h2>
                <?php while ($row = $duyuruResult->fetch_assoc()) { ?>
                    <div class="duyuru" style="margin-top: 10px;">
                        <h3><?= $row["baslik"] ?></h3>
                        <p><?= $row["icerik"] ?></p>
                        <button class="sil-buton" style="margin-top: 10px;">
                            <a href="?sil=<?= $row["id"] ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" fill="red">
                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                            </svg>
                            </a>
                        </button>
                        <button class="edit-button" style="margin-top: 10px;" id="edit-button-<?= $row["id"] ?>">
                        <svg id="duyuru-duzenle" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" style="vertical-align: middle; cursor: pointer;"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                        </svg>
                        </button>
                    </div>
                <?php } ?>
            </div>
            <div id="duyuruModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Duyuru Düzenle</h2>
                    <label for="duyuru-baslik">Başlık:</label>
                    <input type="text" id="duyuru-baslik" name="duyuru-baslik" required><br>
                    
                    <label for="duyuru-icerik">İçerik:</label>
                    <textarea id="duyuru-icerik" name="duyuru-icerik" rows="10" required></textarea>
                    
                    <button id="duyuru-kaydet">Kaydet</button>
                </div>
            </div>
            <script>
                const duyuruDuzenleSVG = document.querySelectorAll(".duyuru .edit-button");
                const duyuruModal = document.getElementById("duyuruModal");
                const modalClose = document.querySelector(".modal-content .close");
                const modalBaslik = document.getElementById("duyuru-baslik");
                const modalIcerik = document.getElementById("duyuru-icerik");
                const modalButton = document.getElementById("duyuru-kaydet");
                
                duyuruDuzenleSVG.forEach(function(editButton) {
                    editButton.addEventListener("click", function() {
                        // Modalı aç
                        duyuruModal.style.display = "block";
                        
                        // Modal içerisine mevcut duyurunun bilgilerini yerleştir
                        const duyuruId = editButton.getAttribute("id").split("-")[2];
                        const duyuruBaslikElement = editButton.parentElement.querySelector("h3");
                        const duyuruIcerikElement = editButton.parentElement.querySelector("p");
                        
                        modalBaslik.value = duyuruBaslikElement.textContent;
                        modalIcerik.value = duyuruIcerikElement.textContent;

                        // Modal kaydet butonuna tıklanınca düzenleme işlemi yap
                        modalButton.addEventListener("click", function(event) {
                            // Duyuru güncelleme işlemi
                            const formData = new FormData();
                            formData.append("duyuruId", duyuruId);
                            formData.append("duyuruBaslik", modalBaslik.value);
                            formData.append("duyuruIcerik", modalIcerik.value);

                            fetch("duyurular.php", {
                                method: "POST",
                                body: formData
                            })
                            .then(response => response.text())
                            .then(result => {
                                alert("Duyuru güncellendi.")
                                duyuruBaslikElement.textContent = modalBaslik.value;
                                duyuruIcerikElement.textContent = modalIcerik.value;
                                duyuruModal.style.display = "none";
                            })
                            .catch(error => {
                                console.error("Error:", error);
                            });
                        });
                    });
                });
                
                duyuruDuzenleSVG.forEach(function(editButton) {
                    editButton.addEventListener("click", function() {
                        // Modalı aç
                        duyuruModal.style.display = "block";
                        
                        // Modal içerisine mevcut duyurunun bilgilerini yerleştir
                        modalBaslik.value = editButton.parentElement.querySelector("h3").textContent;
                        modalIcerik.value = editButton.parentElement.querySelector("p").textContent;
                    });
                });
                
                modalClose.addEventListener("click", function() {
                    // Modalı kapat
                    duyuruModal.style.display = "none";
                });
                
                window.addEventListener("click", function(event) {
                    // Modal dışına tıklanırsa modalı kapat
                    if (event.target === duyuruModal) {
                        duyuruModal.style.display = "none";
                    }
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
                    overflow: auto;
                    background-color: rgba(0, 0, 0, 0.4);
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
                }
                
                .modal-content label {
                    display: block;
                    margin-top: 10px;
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
                
                .duyuru {
                    border: 1px solid #ccc;
                    padding: 10px;
                    margin-bottom: 20px;
                    border-radius: 5px;
                }
                
                .duyuru h3 {
                    font-weight: bold;
                }
                
                .sil-buton {
                    background: none;
                    border: none;
                    cursor: pointer;
                    float: right;
                }
            </style>
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
