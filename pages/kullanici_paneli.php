<?php
require __DIR__ . '/../includes/header.php';

// Sadece giriş yapmış kullanıcı erişsin
if (!is_logged_in()) {
    flash('genel', 'Bu sayfaya erişmek için giriş yapmalısın.', 'err');
    redirect('/pages/login.php');
}

$userID   = current_user_id();
$userName = current_user_name();

/* Kullanıcı tarif istatistikleri */
$istatistik = [
    'Toplam'     => 0,
    'Bekleyen'   => 0,
    'Onaylı'     => 0,
    'Reddedildi' => 0,
];

try {
    $stmt = $conn->prepare("
        SELECT
            COUNT(*) AS Toplam,
            SUM(CASE WHEN OnayDurumu IS NULL OR OnayDurumu = 'Bekleyen' THEN 1 ELSE 0 END) AS Bekleyen,
            SUM(CASE WHEN OnayDurumu = 'Onaylı' THEN 1 ELSE 0 END) AS Onayli,
            SUM(CASE WHEN OnayDurumu = 'Reddedildi' THEN 1 ELSE 0 END) AS Reddedilen
        FROM Tarifler
        WHERE KullaniciID = ?
    ");
    $stmt->execute([$userID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $istatistik['Toplam']     = (int)$row['Toplam'];
        $istatistik['Bekleyen']   = (int)$row['Bekleyen'];
        $istatistik['Onaylı']     = (int)$row['Onayli'];
        $istatistik['Reddedildi'] = (int)$row['Reddedilen'];
    }

} catch (PDOException $e) {
    flash('kullanici_panel', 'İstatistikler alınamadı: ' . $e->getMessage(), 'err');
}

/* Son eklenen tarifler (kullanıcıya ait)*/
$tarifler = [];

try {
    $stmt = $conn->prepare("
        SELECT TOP 5
            t.TarifID,
            t.TarifAdi,
            t.EklemeTarihi,
            t.OnayDurumu,
            t.AdminNot,
            c.KategoriAdi
        FROM Tarifler t
        LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
        WHERE t.KullaniciID = ?
        ORDER BY t.EklemeTarihi DESC
    ");
    $stmt->execute([$userID]);
    $tarifler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    flash('kullanici_panel', 'Tarifler alınamadı: ' . $e->getMessage(), 'err');
}

?>

<h2 style="text-align:center; margin-top:40px;">Kullanıcı Paneli</h2>

<!-- Admin Notu Stili -->
<style>
.admin-not {
    margin-top: 8px;
    padding: 10px 14px;
    background: #fff3cd;
    border-left: 5px solid #f0ad4e;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.5;
    color: #8a6d3b;
}
</style>

<!-- İstatistik Kartları -->
<div class="istatistik-kutular" style="display:flex; gap:20px; flex-wrap:wrap; justify-content:center; margin-bottom:30px;">

  <div class="istatistik-box" style="background:#fff; padding:18px 22px; border-radius:14px; text-align:center; min-width:150px; box-shadow:0 3px 10px rgba(0,0,0,0.06);">
    <h3><?= e($istatistik['Toplam']) ?></h3>
    <p>Toplam Tarifin</p>
  </div>

  <div class="istatistik-box" style="background:#fff7d1; padding:18px 22px; border-radius:14px; text-align:center; min-width:150px; box-shadow:0 3px 10px rgba(0,0,0,0.06);">
    <h3><?= e($istatistik['Bekleyen']) ?></h3>
    <p>Bekleyen</p>
  </div>

  <div class="istatistik-box" style="background:#d9fbe5; padding:18px 22px; border-radius:14px; text-align:center; min-width:150px; box-shadow:0 3px 10px rgba(0,0,0,0.06);">
    <h3><?= e($istatistik['Onaylı']) ?></h3>
    <p>Onaylı</p>
  </div>

  <div class="istatistik-box" style="background:#ffd6d6; padding:18px 22px; border-radius:14px; text-align:center; min-width:150px; box-shadow:0 3px 10px rgba(0,0,0,0.06);">
    <h3><?= e($istatistik['Reddedildi']) ?></h3>
    <p>Reddedilen</p>
  </div>

</div>

<!-- Son Eklenen Tarifler -->
<div class="admin-section">
  <h3> Son Eklediğin Tarifler</h3>

  <?php if (empty($tarifler)): ?>
    <p>Henüz tarif eklememişsin. İlk tarifini eklemek için
      <a href="<?= SITE_URL ?>/pages/tarif_ekle.php">buraya tıkla</a>.
    </p>

  <?php else: ?>

    <table class="table-modern">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tarif Adı</th>
          <th>Kategori</th>
          <th>Tarih</th>
          <th>Durum</th>
          <th>Admin Notu</th>
          <th>İşlem</th>
        </tr>
      </thead>

      <tbody>

      <?php foreach ($tarifler as $t): ?>

        <?php
            $durum = $t['OnayDurumu'] ?? 'Bekleyen';
            $badgeClass =
                ($durum === 'Onaylı') ? 'badge-onayli'
                : (($durum === 'Reddedildi') ? 'badge-reddedildi' : 'badge-bekleyen');
        ?>

        <tr>
          <td><?= e($t['TarifID']) ?></td>
          <td><?= e($t['TarifAdi']) ?></td>
          <td><?= e($t['KategoriAdi'] ?? '-') ?></td>
          <td><?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></td>

          <td><span class="badge <?= $badgeClass ?>"><?= e($durum) ?></span></td>

          <!-- Admin Notu -->
          <td>
            <?php if (!empty($t['AdminNot'])): ?>
              <div class="admin-not">
                <?= nl2br(e($t['AdminNot'])) ?>
              </div>
            <?php else: ?>
              <span style="color:#888;">—</span>
            <?php endif; ?>
          </td>

          <td class="table-actions">

              <a href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>"
                 class="btn-action btn-view2">
                 <i class="fa-solid fa-eye"></i> Görüntüle
              </a>

              <a href="<?= SITE_URL ?>/pages/tarif_duzenle.php?id=<?= e($t['TarifID']) ?>"
                 class="btn-action btn-edit2">
                 <i class="fa-solid fa-pen-to-square"></i> Düzenle
              </a>

              <a href="<?= SITE_URL ?>/pages/tarif_sil.php?id=<?= e($t['TarifID']) ?>&_csrf=<?= csrf_token() ?>"
                 class="btn-action btn-delete2"
                 onclick="return confirm('Bu tarifi silmek istediğine emin misin?');">
                 <i class="fa-solid fa-trash"></i> Sil
              </a>

          </td>
        </tr>

      <?php endforeach; ?>

      </tbody>
    </table>

    <div style="margin-top:15px; text-align:right;">
      <a class="btn-all-recipes" href="<?= SITE_URL ?>/pages/benim_tariflerim.php">
         Tüm Tariflerimi Gör
      </a>
    </div>

  <?php endif; ?>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
