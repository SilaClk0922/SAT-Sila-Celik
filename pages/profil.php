<?php
require __DIR__ . '/../includes/header.php';

if (!is_logged_in()) {
    redirect('/pages/login.php');
}

$adSoyad = $_SESSION['user']['AdSoyad'] ?? 'Bilinmiyor';
$email   = $_SESSION['user']['Email'] ?? 'Bilinmiyor';
$rol     = $_SESSION['user']['rol'] ?? 'Kullanıcı';
$avatar  = $_SESSION['user']['Avatar'] ?? '';
?>
<style>
/*  Profil Kartı */
.profil-wrapper {
    max-width: 950px;
    margin: 40px auto;
    padding: 40px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    display: flex;
    gap: 40px;
    align-items: flex-start;
}

/* Avatar */
.profil-avatar {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18);
    border: 4px solid #d9c3ff;
}

.profil-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Bilgi Alanı */
.profil-info {
    flex: 1;
}

.profil-info h2 {
    font-size: 28px;
    font-weight: 700;
    color: #7b4bbe;
    margin-bottom: 20px;
}

/* Bilgi satırı */
.profil-info p {
    font-size: 17px;
    margin-bottom: 10px;
}

/* Düzenle Butonu */
.profil-edit-btn {
    margin-top: 18px;
    display: inline-block;
    background: #b58bff;
    color: white !important;
    padding: 10px 22px;
    font-size: 16px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.25s ease;
}

.profil-edit-btn:hover {
    background: #9b60e7;
    transform: translateY(-2px);
}
</style>

<h2 style="text-align:center; margin-top:20px; color:#7b4bbe;">
    <i class="fa-solid fa-user-circle"></i> Profilim
</h2>

<div class="profil-wrapper">

    <!-- Avatar -->
    <div>
        <div class="profil-avatar">
            <img src="<?= $avatar ? SITE_URL.'/'.$avatar : SITE_URL.'/assets/avatar-default.png' ?>" alt="">
        </div>

        <a class="profil-edit-btn" href="<?= SITE_URL ?>/pages/profil_duzenle.php">Profili Düzenle</a>
    </div>

    <!-- Bilgiler -->
    <div class="profil-info">
        <h2>Kullanıcı Bilgileri</h2>

        <p><strong>Ad Soyad:</strong> <?= e($adSoyad) ?></p>
        <p><strong>E-posta:</strong> <?= e($email) ?></p>
        <p><strong>Rol:</strong> <?= e($rol) ?></p>
    </div>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
