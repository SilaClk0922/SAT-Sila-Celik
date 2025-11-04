<?php
require __DIR__ . '/../includes/header.php';

// 🌿 Yalnızca onaylı tarifleri getir
try {
    $tarifler = $conn->query("
        SELECT 
            t.TarifID,
            t.TarifAdi,
            t.Goruntu,
            t.EklemeTarihi,
            k.AdSoyad AS Ekleyen,
            c.KategoriAdi
        FROM Tarifler t
        LEFT JOIN Kullanicilar k ON t.KullaniciID = k.KullaniciID
        LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
        WHERE t.OnayDurumu = 'Onaylı'
        ORDER BY t.EklemeTarihi DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('index', 'Tarifler yüklenirken hata oluştu: ' . $e->getMessage(), 'err');
}
?>

<h2>🍽️ En Yeni Onaylı Tarifler</h2>
<?php render_flash(); ?>

<div class="tarif-listesi">
<?php if (empty($tarifler)): ?>
    <p>Henüz onaylanmış bir tarif bulunmamaktadır.</p>
<?php else: ?>
    <?php foreach ($tarifler as $t): ?>
        <div class="tarif-kart">
            <div class="tarif-resim">
                <?php if (!empty($t['Goruntu'])): ?>
                    <img src="<?= SITE_URL . '/' . e($t['Goruntu']) ?>" 
                         alt="<?= e($t['TarifAdi']) ?>">
                <?php else: ?>
                    <img src="<?= SITE_URL ?>/assets/no-image.png" alt="Resim Yok">
                <?php endif; ?>
            </div>

            <div class="tarif-icerik">
                <h3>
                    <a href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">
                        <?= e($t['TarifAdi']) ?>
                    </a>
                </h3>

                <p class="kategori">📂 <?= e($t['KategoriAdi'] ?? 'Kategori Yok') ?></p>
                <p class="ekleyen">👨‍🍳 <?= e($t['Ekleyen'] ?? 'Bilinmiyor') ?></p>
                <p class="tarih">📅 <?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></p>
                <a class="btn-detay" href="tarif_detay.php?id=<?= e($t['TarifID']) ?>">🍽️ Detayını Gör</a>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
