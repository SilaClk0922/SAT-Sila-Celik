<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

// Tarif istatistikleri
try {
    $istatistik = $conn->query("
        SELECT 
            SUM(CASE WHEN OnayDurumu IS NULL OR LTRIM(RTRIM(OnayDurumu)) = '' OR LTRIM(RTRIM(OnayDurumu)) = 'Beklemede' THEN 1 ELSE 0 END) AS Bekleyen,
            SUM(CASE WHEN LTRIM(RTRIM(OnayDurumu)) = 'Onaylı' THEN 1 ELSE 0 END) AS Onayli,
            SUM(CASE WHEN LTRIM(RTRIM(OnayDurumu)) = 'Reddedildi' THEN 1 ELSE 0 END) AS Reddedilen
        FROM Tarifler
    ")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('admin', 'İstatistikler alınamadı: ' . $e->getMessage(), 'err');
}

// Son eklenen tarifler
try {
    $tarifler = $conn->query("
        SELECT TOP 10 
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

<h2> Admin Paneli</h2>
<?php render_flash('admin'); ?>

<div style="background:#fff3cd; padding:10px; border:1px solid #ffcc00; color:#000; font-weight:bold; margin:20px 0;">
<h4>HAM DURUM TESTİ:</h4>
<?php
if (!empty($tarifler)) {
    foreach ($tarifler as $t) {
        echo "ID {$t['TarifID']} → [" . htmlspecialchars($t['OnayDurumu'] ?? 'NULL') . "]<br>";
    }
} else {
    echo "Veri yok.";
}
?>
</div>

<?php
//  TEST: OnayDurumu ham değerlerini yukarıda göster
echo "<div style='background:#fff3cd; padding:10px; border:1px solid #ffcc00; color:#000; font-weight:bold; margin:20px 0;'>";
echo "<h4>HAM DURUM TESTİ:</h4>";
if (!empty($tarifler)) {
    foreach ($tarifler as $t) {
        echo "ID {$t['TarifID']} → [" . htmlspecialchars($t['OnayDurumu'] ?? 'NULL') . "]<br>";
    }
} else {
    echo "Veri yok.";
}
echo "</div>";
?>

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
          <?php
            // Durumu temizle (boşluk ve karakter farklarını sil)
            $hamDurum = trim((string)($t['OnayDurumu'] ?? ''));
            $durum = mb_strtolower(str_replace(['İ', 'ı'], ['i', 'i'], $hamDurum), 'UTF-8');
            $durum = preg_replace('/\s+/', '', $durum); // gizli boşlukları sil

            if ($durum === '' || $durum === null) $durum = 'beklemede';

            $renk = match($durum) {
                'onayli' => 'green',
                'reddedildi' => 'red',
                default => 'gray'
            };

            $durumLabel = match($durum) {
                'onayli' => 'Onaylı',
                'reddedildi' => 'Reddedildi',
                default => 'Beklemede'
            };
          ?>
          <tr>
            <td><?= e($t['TarifID']) ?></td>
            <td><?= e($t['TarifAdi']) ?></td>
            <td><?= e($t['Ekleyen'] ?? '-') ?></td>
            <td><?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></td>
            <td><span class="durum <?= $renk ?>"><?= e($durumLabel) ?></span></td>
            <td>
              <a class="btn-mini" href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">Görüntüle</a>

              <?php if (strpos($durum, 'beklemede') !== false): ?>
                <a href="<?= SITE_URL ?>/pages/tarif_onayla.php?id=<?= e($t['TarifID']) ?>"
                   class="btn-mini green"
                   onclick="return confirm('Bu tarifi onaylamak istediğinize emin misiniz?')">
                   Onayla
                </a>

                <a href="<?= SITE_URL ?>/pages/tarif_sil.php?id=<?= e($t['TarifID']) ?>"
                   class="btn-mini red"
                   onclick="return confirm('Bu tarifi reddetmek istediğinize emin misiniz?')">
                   Reddet
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
