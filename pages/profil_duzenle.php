<?php
require __DIR__ . '/../includes/header.php';
require_login();

$userID = current_user_id();

// Kullanıcı bilgisi
$stmt = $conn->prepare("SELECT AdSoyad, Email, KullaniciAdi, ProfilResmi FROM Kullanicilar WHERE KullaniciID = ?");
$stmt->execute([$userID]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) {
    flash('profil', 'Kullanıcı bilgisi bulunamadı!', 'err');
    redirect('/pages/kullanici_paneli.php');
}

$profilResmi = !empty($u['ProfilResmi'])
    ? SITE_URL . "/uploads/profil/" . $u['ProfilResmi']
    : SITE_URL . "/assets/default_avatar.png";

// FORM GÖNDERİLDİ Mİ?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('profil', 'Güvenlik doğrulaması başarısız.', 'err');
        redirect('/pages/profil_duzenle.php');
    }

    // FOTOĞRAF YÜKLENDİ Mİ?
    if (!empty($_FILES['profil']['name'])) {

        $dosyaAdi = time() . '_' . basename($_FILES['profil']['name']);
        $hedef = __DIR__ . '/../uploads/profil/' . $dosyaAdi;

        if (move_uploaded_file($_FILES['profil']['tmp_name'], $hedef)) {

            // önceki resmi sil
            if (!empty($u['ProfilResmi'])) {
                $eskiYol = __DIR__ . '/../uploads/profil/' . $u['ProfilResmi'];
                if (file_exists($eskiYol)) unlink($eskiYol);
            }

            // veritabanı güncelle
            $update = $conn->prepare("UPDATE Kullanicilar SET ProfilResmi = ? WHERE KullaniciID = ?");
            $update->execute([$dosyaAdi, $userID]);

            // session güncelle
            $_SESSION['ProfilResmi'] = $dosyaAdi;

            flash('profil', 'Profil fotoğrafı başarıyla güncellendi ✔', 'ok');
            redirect('/pages/kullanici_paneli.php');
        }
    }

    flash('profil', 'Fotoğraf yüklenemedi!', 'err');
    redirect('/pages/profil_duzenle.php');
}
?>

<h2 style="text-align:center;">📸 Profil Fotoğrafı Güncelle</h2>

<?php render_flash('profil'); ?>

<div style="max-width:500px; margin:25px auto; background:#fff; padding:25px; border-radius:12px; box-shadow:0 3px 10px rgba(0,0,0,0.15);">

    <div style="text-align:center; margin-bottom:20px;">
        <img src="<?= $profilResmi ?>" style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
    </div>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_input() ?>

        <label>Yeni Profil Fotoğrafı Seç</label>
        <input type="file" name="profil" required style="margin-bottom:15px;">

        <button class="btn" type="submit">Kaydet</button>
    </form>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
