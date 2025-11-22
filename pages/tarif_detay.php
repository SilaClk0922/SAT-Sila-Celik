<?php
require __DIR__ . '/../includes/header.php';

$tarifID = $_GET['id'] ?? null;

if (!$tarifID) {
    flash('index', 'Geçersiz tarif bağlantısı.', 'err');
    redirect('/pages/index.php');
}

try {
    $stmt = $conn->prepare("
        SELECT 
            t.TarifAdi,
            t.Malzemeler,
            t.Hazirlanis,
            t.Goruntu,
            t.EklemeTarihi,
            k.AdSoyad AS Ekleyen,
            c.KategoriAdi
        FROM Tarifler t
        LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
        LEFT JOIN Kullanicilar k ON t.KullaniciID = k.KullaniciID
        WHERE t.TarifID = ?
    ");
    $stmt->execute([$tarifID]);
    $tarif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tarif) {
        flash('index', 'Tarif bulunamadı.', 'err');
        redirect('/pages/index.php');
    }
} catch (PDOException $e) {
    flash('index', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
    redirect('/pages/index.php');
}
?>

<div class="tarif-detay-kapsayici">
  <div class="tarif-baslik">
    <h2><?= e($tarif['TarifAdi']) ?></h2>
  </div>

  <div class="tarif-detay-icerik">

    <!-- Görsel -->
    <div class="tarif-detay-resim">
      <?php if (!empty($tarif['Goruntu'])): ?>
        <img src="<?= SITE_URL . '/' . e($tarif['Goruntu']) ?>" alt="<?= e($tarif['TarifAdi']) ?>">
      <?php else: ?>
        <img src="<?= SITE_URL ?>/assets/no-image.png" alt="Resim Yok">
      <?php endif; ?>
    </div>

    <!-- Temel Bilgiler -->
    <div class="tarif-detay-bilgi">
      <p><strong>📂 Kategori:</strong> <?= e($tarif['KategoriAdi'] ?? 'Kategori Yok') ?></p>
      <p><strong>👨‍🍳 Ekleyen:</strong> <?= e($tarif['Ekleyen'] ?? 'Bilinmiyor') ?></p>
      <p><strong>📅 Tarih:</strong> <?= date('d.m.Y', strtotime($tarif['EklemeTarihi'])) ?></p>
    </div>

    <!-- Malzemeler -->
    <div class="tarif-bolum">
      <h3>🧂 Malzemeler</h3>
      <div class="tarif-kutu"><?= nl2br(e($tarif['Malzemeler'])) ?></div>
    </div>

    <!-- Hazırlanışı -->
    <div class="tarif-bolum">
      <h3>Hazırlanışı</h3>
      <div class="tarif-kutu"><?= nl2br(e($tarif['Hazirlanis'])) ?></div>
    </div>

  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
