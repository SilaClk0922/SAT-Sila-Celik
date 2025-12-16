<?php
require __DIR__ . '/../includes/header.php';

$ara = trim($_GET['ara'] ?? '');

/*  Boş arama kontrolü
   - Arama kelimesi yoksa kullanıcı bilgilendirilir */
if ($ara === '') {
    flash('arama', 'Arama yapmak için bir kelime giriniz!', 'err');
}
?>

<h2> Arama Sonuçları</h2>
<?php render_flash('arama'); ?>

<?php
if ($ara !== '') {

    try {
        $stmt = $conn->prepare("
            SELECT 
                t.TarifID,
                t.TarifAdi,
                t.Goruntu,
                t.EklemeTarihi,
                c.KategoriAdi,
                k.AdSoyad AS Ekleyen
            FROM Tarifler t
            LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
            LEFT JOIN Kullanicilar k ON t.KullaniciID = k.KullaniciID
            WHERE t.OnayDurumu = 'Onaylı'
              AND t.TarifAdi LIKE ?
            ORDER BY t.EklemeTarihi DESC
        ");

        $stmt->execute(["%$ara%"]);
        $sonuclar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        flash('arama', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
    }
}
?>

<!-- Arama sonuçlarının listelendiği alan -->
<div class="tarif-listesi">

<?php if (empty($sonuclar)): ?>
    <p>🔎 “<?= e($ara) ?>” için sonuç bulunamadı.</p>
<?php else: ?>
    <?php foreach ($sonuclar as $t): ?>
        <div class="tarif-kart">

            <div class="tarif-resim">
                <?php if (!empty($t['Goruntu'])): ?>
                    <img src="<?= SITE_URL . '/' . e($t['Goruntu']) ?>" alt="<?= e($t['TarifAdi']) ?>">
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

                <p>📂 <?= e($t['KategoriAdi'] ?? '-') ?></p>
                <p>👨‍🍳 <?= e($t['Ekleyen'] ?? 'Bilinmiyor') ?></p>
                <p>📅 <?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></p>

                <a class="btn-detay" href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">
                    🍽️ Detayına Git
                </a>
            </div>

        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
