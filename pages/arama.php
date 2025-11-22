<?php
require __DIR__ . '/../includes/header.php';

$q = trim($_GET['q'] ?? '');

$stmt = $conn->prepare("
    SELECT t.*, k.KategoriAdi, u.AdSoyad 
    FROM Tarifler t
    LEFT JOIN Kategoriler k ON k.KategoriID = t.KategoriID
    LEFT JOIN Kullanicilar u ON u.KullaniciID = t.KullaniciID
    WHERE t.TarifAdi LIKE ?
    AND t.OnayDurumu = 'Onaylı'
");
$stmt->execute(["%$q%"]);
$sonuclar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2> Arama Sonuçları: <b><?= e($q) ?></b></h2>

<div class="tarif-listesi">
<?php if (!$sonuclar): ?>
    <p>Sonuç bulunamadı.</p>
<?php else: ?>
    <?php foreach ($sonuclar as $t): ?>
        <div class="tarif-kart">
            <div class="tarif-resim">
                <img src="<?= SITE_URL ?>/uploads/<?= e($t['Foto']) ?>" alt="">
            </div>
            <div class="tarif-icerik">
                <h3><?= e($t['TarifAdi']) ?></h3>
                <p class="kategori">📁 <?= e($t['KategoriAdi']) ?></p>
                <p>👤 <?= e($t['AdSoyad']) ?></p>
                <p class="tarih">📅 <?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></p>
                <a class="btn-detay" href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">Detayını Gör</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
