<?php
require __DIR__ . '/../includes/header.php';

// Tarif ID alma
$tarifID = $_GET['id'] ?? null;

if (!$tarifID || !is_numeric($tarifID)) {
    flash('genel', 'Geçersiz tarif ID.', 'err');
    redirect('/pages/index.php');
}

// Tarif verisi
$stmt = $conn->prepare("
    SELECT t.*, k.KategoriAdi, u.AdSoyad AS KullaniciAdi
    FROM Tarifler t
    LEFT JOIN Kategoriler k ON t.KategoriID = k.KategoriID
    LEFT JOIN Kullanicilar u ON t.KullaniciID = u.KullaniciID
    WHERE t.TarifID = ?
");
$stmt->execute([$tarifID]);
$tarif = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarif) {
    flash('genel', 'Tarif bulunamadı.', 'err');
    redirect('/pages/index.php');
}
?>

<style>
.detay-wrapper {
    max-width: 850px;
    margin: 30px auto;
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.detay-img {
    width: 100%;
    border-radius: 12px;
    margin-bottom: 20px;
}

.detay-title {
    font-size: 28px;
    font-weight: 700;
    color: #7b4bbe;
    margin-bottom: 10px;
}

.detay-info-box {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}
.detay-info-item {
    background: #f4e9ff;
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: 600;
    color: #6a36c9;
}

.detay-section-title {
    font-size: 22px;
    font-weight: bold;
    color: #7b4bbe;
    margin-top: 20px;
}
</style>

<div class="detay-wrapper">

    <?php if ($tarif['Goruntu']): ?>
        <img src="<?= SITE_URL ?>/<?= $tarif['Goruntu'] ?>" class="detay-img">
    <?php endif; ?>

    <div class="detay-title"><?= e($tarif['TarifAdi']) ?></div>

    <div class="detay-info-box">
        <div class="detay-info-item">⏳ Pişirme: <?= e($tarif['PisirmeSuresi'] ?? 'Belirtilmemiş') ?></div>
        <div class="detay-info-item">👥 Kaç Kişilik: <?= e($tarif['KacKisilik'] ?? 'Belirtilmemiş') ?></div>
        <div class="detay-info-item">🏷 Kategori: <?= e($tarif['KategoriAdi']) ?></div>
    </div>

    <div><b>Tarifi Ekleyen:</b> <?= e($tarif['KullaniciAdi']) ?></div>

    <div class="detay-section-title">📌 Malzemeler</div>
    <p><?= nl2br(e($tarif['Malzemeler'])) ?></p>

    <div class="detay-section-title">🍳 Yapılışı</div>
    <p><?= nl2br(e($tarif['Hazirlanis'])) ?></p>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
