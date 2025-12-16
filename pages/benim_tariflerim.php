<?php
require __DIR__ . '/../includes/header.php';

// Sadece giriş yapmış kullanıcı erişsin
if (!is_logged_in()) {
    flash('genel', 'Bu sayfaya erişmek için giriş yapmalısın.', 'err');
    redirect('/pages/login.php');
}

$userID   = current_user_id();
$userName = current_user_name();

// Kullanıcının tüm tarifleri
$tarifler = [];
try {
    $stmt = $conn->prepare("
        SELECT 
            t.TarifID,
            t.TarifAdi,
            t.EklemeTarihi,
            t.OnayDurumu,
            c.KategoriAdi
        FROM Tarifler t
        LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
        WHERE t.KullaniciID = ?
        ORDER BY t.EklemeTarihi DESC, t.TarifID DESC
    ");
    $stmt->execute([$userID]);
    $tarifler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    flash('benim_tariflerim', 'Tarifler alınamadı: ' . $e->getMessage(), 'err');
}
?>

<h2 class="section-title" style="margin-top:40px; text-align:left;">
     Tüm Tariflerim
</h2>

<?php if (empty($tarifler)): ?>
<!-- Kullanıcının hiç tarifi yoksa gösterilecek alan-->
    <p>Henüz tarif eklememişsin. İlk tarifini eklemek için
        <a href="<?= SITE_URL ?>/pages/tarif_ekle.php">buraya tıklayabilirsin.</a>
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
            <th>İşlem</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($tarifler as $t): ?>

<!-- Tarif onay durumunun belirlenmesi
               - Onaylı
               - Reddedildi
               - Bekleyen -->
            <?php
            $durum = $t['OnayDurumu'] ?? 'Bekleyen';

            $badgeClass =
                ($durum === 'Onaylı') ? 'badge-onayli'
                    : (($durum === 'Reddedildi') ? 'badge-reddedildi' : 'badge-bekleyen');
            ?>
            <tr>
                <td><?= (int)$t['TarifID'] ?></td>
                <td><?= e($t['TarifAdi']) ?></td>
                <td><?= e($t['KategoriAdi'] ?? '-') ?></td>
                <td><?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></td>

                <td>
                    <span class="badge <?= $badgeClass ?>">
                        <?= e($durum) ?>
                    </span>
                </td>
                
                <!-- Tarif işlem butonları
                     - Görüntüle
                     - Düzenle
                     - Sil -->
                <td class="table-actions">

                    <a href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= (int)$t['TarifID'] ?>"
                       class="btn-action btn-view2">
                        <i class="fa-solid fa-eye"></i> Görüntüle
                    </a>

                    <a href="<?= SITE_URL ?>/pages/tarif_duzenle.php?id=<?= (int)$t['TarifID'] ?>"
                       class="btn-action btn-edit2">
                        <i class="fa-solid fa-pen-to-square"></i> Düzenle
                    </a>

                    <a href="<?= SITE_URL ?>/pages/tarif_sil.php?id=<?= (int)$t['TarifID'] ?>&_csrf=<?= csrf_token() ?>"
                       class="btn-action btn-delete2"
                       onclick="return confirm('Bu tarifi silmek istediğine emin misin?');">
                        <i class="fa-solid fa-trash"></i> Sil
                    </a>

                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
