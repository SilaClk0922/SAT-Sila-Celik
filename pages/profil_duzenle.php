<?php
require __DIR__ . '/../includes/header.php';
require_login();

$mesaj = "";
$mesajTipi = "";

// Kullanıcı bilgileri
$userID  = current_user_id();
$adSoyad = current_user_name();
$email   = current_user_email();
$avatar  = current_user_photo();


// FORM GÖNDERİLDİ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*  AD SOYAD & EMAIL GÜNCELLEME*/
    if (isset($_POST['bilgi_guncelle'])) {

        $yeniAd    = trim($_POST['adsoyad']);
        $yeniEmail = trim($_POST['email']);

        if ($yeniAd === "" || $yeniEmail === "") {
            $mesaj = "Alanlar boş olamaz!";
            $mesajTipi = "err";
        } else {

            // Email başkası tarafından kullanılıyor mu?
            $stmt = $conn->prepare("SELECT * FROM Kullanicilar WHERE Email = ? AND KullaniciID != ?");
            $stmt->execute([$yeniEmail, $userID]);

            if ($stmt->fetch()) {
                $mesaj = "Bu e-mail adresi başka bir kullanıcı tarafından kullanılıyor!";
                $mesajTipi = "err";

            } else {

                // Veritabanı güncelle
                $stmt = $conn->prepare("UPDATE Kullanicilar SET AdSoyad = ?, Email = ? WHERE KullaniciID = ?");
                $stmt->execute([$yeniAd, $yeniEmail, $userID]);

                // SESSION güncelle
                $_SESSION['user']['AdSoyad'] = $yeniAd;
                $_SESSION['user']['Email']   = $yeniEmail;

                // SAYFA ÜSTÜNDE GÜNCEL GÖZÜKMESİ İÇİN
                $adSoyad = $yeniAd;
                $email   = $yeniEmail;

                $mesaj = "Bilgiler başarıyla güncellendi.";
                $mesajTipi = "ok";
            }
        }
    }

    /*  ŞİFRE DEĞİŞTİRME */
    if (isset($_POST['sifre_guncelle'])) {

        $eski = $_POST['eski_sifre'] ?? '';
        $yeni = $_POST['yeni_sifre'] ?? '';
        $tekrar = $_POST['yeni_sifre2'] ?? '';

        $stmt = $conn->prepare("SELECT Sifre FROM Kullanicilar WHERE KullaniciID = ?");
        $stmt->execute([$userID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($eski, $row['Sifre'])) {
            $mesaj = "Eski şifre yanlış!";
            $mesajTipi = "err";

        } elseif ($yeni !== $tekrar) {
            $mesaj = "Yeni şifreler eşleşmiyor!";
            $mesajTipi = "err";

        } elseif (strlen($yeni) < 6) {
            $mesaj = "Yeni şifre en az 6 karakter olmalıdır!";
            $mesajTipi = "err";

        } else {
            $yeniHash = password_hash($yeni, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Kullanicilar SET Sifre = ? WHERE KullaniciID = ?");
            $stmt->execute([$yeniHash, $userID]);

            $mesaj = "Şifre başarıyla güncellendi!";
            $mesajTipi = "ok";
        }
    }



    /* PROFİL FOTOĞRAFI GÜNCELLEME */
    if (isset($_POST['foto_guncelle']) && !empty($_FILES['avatar']['name'])) {

        $klasor = __DIR__ . '/../uploads/';
        if (!is_dir($klasor)) mkdir($klasor, 0777, true);

        $dosyaAdi = 'avatar_' . $userID . '_' . time() . '.jpg';
        $hedef = $klasor . $dosyaAdi;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $hedef)) {

            $stmt = $conn->prepare("UPDATE Kullanicilar SET ProfilResmi = ? WHERE KullaniciID = ?");
            $stmt->execute(["uploads/" . $dosyaAdi, $userID]);

            $_SESSION['user']['Avatar'] = "uploads/" . $dosyaAdi;
            $avatar = "uploads/" . $dosyaAdi;

            $mesaj = "Profil fotoğrafı başarıyla güncellendi!";
            $mesajTipi = "ok";

        } else {
            $mesaj = "Fotoğraf yüklenemedi!";
            $mesajTipi = "err";
        }
    }
}

?>
<style>
.profil-card {
    max-width: 750px;
    margin: 40px auto;
    padding: 35px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 22px;
    color: #7b4bbe;
    font-weight: 700;
    margin: 20px 0 10px;
}

.save-btn {
    background: #b58bff;
    color: white;
    padding: 10px 22px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
}
.save-btn:hover {
    background: #9b60e7;
}

.avatar-preview {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #d3baff;
    margin-bottom: 12px;
}
.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>


<h2 style="text-align:center; margin:20px 0; color:#7b4bbe;">Profili Düzenle</h2>

<div class="profil-card">

    <?php if ($mesaj): ?>
        <div class="alert <?= $mesajTipi === 'ok' ? 'alert-success' : 'alert-danger' ?>">
            <?= e($mesaj) ?>
        </div>
    <?php endif; ?>


    <!--AD SOYAD & EMAIL -->
    <form method="post">
        <div class="section-title">Kişisel Bilgiler</div>

        <label><strong>Ad Soyad:</strong></label>
        <input type="text" name="adsoyad" class="form-control" value="<?= e($adSoyad) ?>">

        <label style="margin-top:10px;"><strong>E-posta:</strong></label>
        <input type="email" name="email" class="form-control" value="<?= e($email) ?>">

        <button type="submit" name="bilgi_guncelle" class="save-btn" style="margin-top:15px;">Bilgileri Kaydet</button>
    </form>


    <!-- ŞİFRE DEĞİŞTİR  -->
    <form method="post" style="margin-top:30px;">
        <div class="section-title">Şifre Değiştir</div>

        <label><strong>Eski Şifre:</strong></label>
        <input type="password" name="eski_sifre" class="form-control" required>

        <label style="margin-top:10px;"><strong>Yeni Şifre:</strong></label>
        <input type="password" name="yeni_sifre" class="form-control" required>

        <label style="margin-top:10px;"><strong>Yeni Şifre (Tekrar):</strong></label>
        <input type="password" name="yeni_sifre2" class="form-control" required>

        <button type="submit" name="sifre_guncelle" class="save-btn" style="margin-top:15px;">Şifreyi Güncelle</button>
    </form>

    <!--  FOTOĞRAF GÜNCELLE -->
    <form method="post" enctype="multipart/form-data" style="margin-top:30px;">
        <div class="section-title">Profil Fotoğrafı</div>

        <div class="avatar-preview">
            <img src="<?= $avatar ? SITE_URL . '/' . $avatar : SITE_URL . '/assets/avatar-default.png' ?>">
        </div>

        <label><strong>Yeni Profil Fotoğrafı:</strong></label>
        <input type="file" name="avatar" accept="image/*" class="form-control">

        <button type="submit" name="foto_guncelle" class="save-btn" style="margin-top:15px;">Fotoğrafı Güncelle</button>
    </form>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
