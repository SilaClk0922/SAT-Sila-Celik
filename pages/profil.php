<?php
require __DIR__ . '/../includes/header.php';

if (!is_logged_in()) {
    flash('genel', 'Bu sayfaya erişmek için giriş yapmalısın.', 'err');
    redirect('/pages/login.php');
}

$userID = current_user_id();

// Kullanıcı bilgilerini getir
$stmt = $conn->prepare("SELECT KullaniciAdi, AdSoyad, Email, ProfilResmi, KayitTarihi 
                        FROM Kullanicilar WHERE KullaniciID = ?");
$stmt->execute([$userID]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

// Profil resmi yoksa varsayılan avatar
$profilResmi = $u['ProfilResmi'] 
    ? SITE_URL . "/uploads/profil/" . $u['ProfilResmi']
    : SITE_URL . "/assets/default_avatar.png";
?>

<h2 style="text-align:center; margin-top:25px;">👤 Profilim</h2>

<div style="
    max-width:900px;
    margin:30px auto;
    display:flex;
    gap:30px;
    background:#fff;
    padding:30px;
    border-radius:18px;
    box-shadow:0 4px 20px rgba(0,0,0,0.08);
">

    <!-- SOL: PROFIL FOTO -->
    <div style="text-align:center;">
        <img src="<?= $profilResmi ?>" 
             style="width:150px; height:150px; border-radius:12px; object-fit:cover;">

        <a href="<?= SITE_URL ?>/pages/profil_duzenle.php"
           class="btn"
           style="margin-top:15px; display:block; text-align:center;">
           Profili Düzenle
        </a>

        <a href="<?= SITE_URL ?>/pages/sifre_degistir.php"
           class="btn-mini"
           style="background:#ff7675; margin-top:8px; display:block; text-align:center;">
           Şifre Değiştir
        </a>
    </div>

    <!-- SAĞ: BİLGİLER -->
    <div style="flex:1;">
        <h3 style="color:var(--brand-dark); margin-bottom:15px;">Kullanıcı Bilgileri</h3>

        <p><b>Ad Soyad:</b> <?= e($u['AdSoyad']) ?></p>
        <p><b>Kullanıcı Adı:</b> <?= e($u['KullaniciAdi']) ?></p>
        <p><b>E-posta:</b> <?= e($u['Email']) ?></p>
        <p><b>Üyelik Tarihi:</b> <?= date('d.m.Y', strtotime($u['KayitTarihi'])) ?></p>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
