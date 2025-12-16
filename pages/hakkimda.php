<?php
require __DIR__ . '/../includes/header.php';

$isLogged = is_logged_in();

// ❗ Giriş yapanlar bu sayfayı göremesin
if ($isLogged) {
    redirect('/pages/index.php');
    exit;
}
?>

<style>
/* ==== Arka Plan ==== */
.hakkimda-bg {
    background: url('<?= SITE_URL ?>/assets/hakkimda-bg.jpg') center/cover no-repeat;
    padding: 80px 0;
    filter: blur(3px);
    opacity: 0.35;
    position: absolute;
    inset: 0;
    z-index: -1;
}

/* ==== Ana Kart ==== */
.hakkimda-container {
    max-width: 900px;
    margin: 40px auto;
    background: rgba(255, 255, 255, 0.78);
    padding: 50px;
    border-radius: 30px;
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 25px rgba(135, 69, 234, 0.25);
    animation: fadeIn 1.2s ease;
}

/* Fade-in animasyonu */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(25px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ==== Başlık ==== */
.hakkimda-title {
    text-align: center;
    font-size: 40px;
    color: #6c3edb;
    font-weight: 800;
    margin-bottom: 25px;
    display: flex;
    gap: 12px;
    justify-content: center;
    align-items: center;
}

/* Titreyen el ikonu */
.wave-anim {
    animation: wave 1.8s infinite;
    transform-origin: 70% 70%;
}

@keyframes wave {
    0% { transform: rotate(0deg); }
    15% { transform: rotate(18deg); }
    30% { transform: rotate(-8deg); }
    45% { transform: rotate(14deg); }
    60% { transform: rotate(-4deg); }
    75% { transform: rotate(10deg); }
    100% { transform: rotate(0deg); }
}

/* ==== Yazı Alanı ==== */
.hakkimda-text {
    font-size: 20px;
    color: #4a4a4a;
    line-height: 1.9;
    margin-top: 20px;
    text-align: center;
    padding: 0 10px;
}

/* Vurgu renk */
.hakkimda-text b {
    color: #6c3edb;
}

</style>


<div class="hakkimda-bg"></div>

<div class="hakkimda-container">
    
    <h2 class="hakkimda-title">
        👤 Hakkımda <span class="wave-anim">👋</span>
    </h2>

    <p class="hakkimda-text">
        Yemek Tarifi Sitemize hoş geldin! 🧁  
        Burada sana ilham olacak yüzlerce tarif keşfedebilir, mutfağında yeni tatlar deneyebilirsin.  
        <br><br>
        Üyelik oluşturarak kendi tariflerini paylaşabilir, diğer kullanıcılarla fikir alışverişinde bulunabilir ve
        yemek dünyasında kendi izini oluşturabilirsin.  
        <br><br>
        Aramıza katılmak için <b>Giriş</b> yapabilir veya hemen <b>Kayıt Ol</b>arak topluluğumuza dahil olabilirsin.  
        <br><br>
        Seni mutfakta görmek için sabırsızlanıyoruz! 🍽✨
    </p>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
