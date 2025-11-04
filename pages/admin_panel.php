<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

// 📊 Tarif sayıları (istatistik kutucuklar)
try {
    $istatistik = $conn->query("
        SELECT 
            SUM(CASE WHEN OnayDurumu = 'Beklemede' OR OnayDurumu IS NULL THEN 1 ELSE 0 END) AS Bekleyen,
            SUM(CASE WHEN OnayDurumu = 'Onaylı' THEN 1 ELSE 0 END) AS Onayli,
            SUM(CASE WHEN OnayDurumu = 'Reddedildi' THEN 1 ELSE 0 END) AS Reddedilen
        FROM Tarifler
    ")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('admin', 'İstatistikler alınamadı: ' . $e->getMessage(), 'err');
}

// 📋 Son eklenen tarifler (ön izleme)
try {
    $tarifler = $conn->query("
        SELECT TOP 5 
            t.TarifID, t.TarifAdi, t.OnayDurumu, t.EklemeTarihi, 
            k.AdSoyad AS Ekleyen
        FROM Tarifler t
        LEFT JOIN Kullanicilar k ON t.KullaniciID = k.KullaniciID
        ORDER BY t.EklemeTarihi DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('admin', 'Tarif listesi alınamadı: ' . $e->getMessage(), 'err');
}
?>

<h2>👑 Admin Paneli</h2>
<?php render_flash('admin'); ?>

<!-- 📊 İSTATİSTİK KUTULARI -->
<div class="istatistik-kutular">
  <div class="istatistik-box bekleyen">
    <h3><?= e($istatistik['Bekleyen'] ?? 0) ?></h3>
    <p>Bekleyen Tarif</p>
  </div>

  <div class="istatistik-box onayli">
    <h3><?= e($istatistik['Onayli'] ?? 0) ?></h3>
    <p>Onaylı Tarif</p>
  </div>

  <div class="istatistik-box reddedilen">
    <h3><?= e($istatistik['Reddedilen'] ?? 0) ?></h3>
    <p>Reddedilen Tarif</p>
  </div>
</div>

<!-- 🧾 SON TARİFLER -->
<div class="admin-section">
  <h3>🧾 Son Eklenen Tarifler</h3>

  <?php if (empty($tarifler)): ?>
    <p>Henüz tarif bulunmamaktadır.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Tarif Adı</th>
          <th>Ekleyen</th>
          <th>Tarih</th>
          <th>Durum</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tarifler as $t): ?>
          <tr>
            <td><?= e($t['TarifID']) ?></td>
            <td><?= e($t['TarifAdi']) ?></td>
            <td><?= e($t['Ekleyen'] ?? '-') ?></td>
            <td><?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></td>
            <td>
              <?php
                $durum = $t['OnayDurumu'] ?? 'Beklemede';
                $renk = match($durum) {
                    'Onaylı' => 'green',
                    'Reddedildi' => 'red',
                    default => 'gray'
                };
              ?>
              <span class="durum <?= $renk ?>"><?= e($durum) ?></span>
            </td>
            <td>
              <a class="btn-mini" href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">Görüntüle</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
