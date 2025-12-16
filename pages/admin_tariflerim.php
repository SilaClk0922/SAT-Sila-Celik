<?php
/*  Ortak header dosyasının dahil edilmesi */

require __DIR__ . '/../includes/header.php';

/*  Admin yetki kontrolü
   Kullanıcı giriş yapmamışsa veya Admin değilse
   erişim engellenir ve ana sayfaya yönlendirilir*/

if (!is_logged_in() || current_user_role() !== 'Admin') {
    flash('genel', 'Bu sayfaya erişim yetkin yok.', 'err');
    redirect('/pages/index.php');
}

/* Giriş yapan admin kullanıcının ID bilgisinin alınması*/

$adminID = current_user_id();

/* Adminin kendi eklediği tariflerin veritabanından çekilmesi
   - Tarif bilgileri
   - Kategori adı
   - Eklenme tarihi
   - Onay durumu*/
   
$stmt = $conn->prepare("
    SELECT 
        t.TarifID,
        t.TarifAdi,
        t.EklemeTarihi,
        t.OnayDurumu,
        t.Goruntu,
        c.KategoriAdi
    FROM Tarifler t
    LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
    WHERE t.KullaniciID = ?
    ORDER BY t.EklemeTarihi DESC
");
$stmt->execute([$adminID]);
$tarifler = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!--  Sayfa başlığı-->
<h2 style="text-align:center; margin:30px 0; font-size:32px; color:#7b4bbe;">
     Admin – Kendi Tariflerin
</h2>

<!--Admin tarifler ana kısım -->
<div class="admin-section" style="max-width:1100px; margin:0 auto 40px auto;">

    <!-- Alt başlık -->
    <h3 style="margin-bottom:25px; color:#7b4bbe; font-size:24px;">
        📌 Eklediğin Tarifler
    </h3>

    <?php if (empty($tarifler)): ?>

        <!--Adminin henüz tarif eklemediği durum -->
        <p style="background:white; padding:15px; border-radius:10px;">
            Henüz tarif eklememişsin. 
            <a href="<?= SITE_URL ?>/pages/tarif_ekle.php">Buraya tıklayarak</a> ilk tarifini ekleyebilirsin.
        </p>

    <?php else: ?>

        <?php foreach ($tarifler as $t):

            /*  Tarif onay durumu belirleme Varsayılan durum: Bekleyen */
            $durum = $t['OnayDurumu'] ?: 'Bekleyen';
            $durumClass = match($durum) {
                'Onaylı'      => 'badge-admin onayli',
                'Reddedildi'  => 'badge-admin red',
                default       => 'badge-admin bekleyen'
            };

            /*  Tarif görseli belirleme Görsel yoksa varsayılan resim kullanılır*/
            $imgUrl = !empty($t['Goruntu'])
                ? SITE_URL . '/' . $t['Goruntu']
                : SITE_URL . '/assets/no-image.png';
        ?>
            <!--  Admin tarif kartı -->
            <div class="admin-tarif-kart">

                <!-- Tarif kapak görseli -->
                <img src="<?= $imgUrl ?>"
                     alt="<?= e($t['TarifAdi']) ?>">

                <!--  Tarif  bilgileri -->
                <div class="admin-tarif-bilgi">
                    <h4><?= e($t['TarifAdi']) ?></h4>

                    <div class="admin-tarif-detay">
                        <!-- Kategori bilgisi -->
                        <span>
                            <i class="fa-solid fa-list"></i>
                            <?= e($t['KategoriAdi'] ?? 'Kategori Yok') ?>
                        </span>

                        <!-- Eklenme tarihi -->
                        <span>
                            <i class="fa-solid fa-calendar-days"></i>
                            <?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?>
                        </span>

                        <!-- Onay durumu -->
                        <span>
                            <i class="fa-solid fa-circle-check"></i>
                            <span class="<?= $durumClass ?>">
                                <?= e($durum) ?>
                            </span>
                        </span>
                    </div>
                </div>

                <!--  Tarif işlem butonları (Görüntüle / Düzenle / Sil) -->
                <div class="admin-tarif-islem">
                      <!--GÖRÜNTÜLE-->
                    <a class="btn-admin view"
                       href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">
                        <i class="fa-solid fa-eye"></i> Görüntüle
                    </a>
                      <!--DÜZENLE-->
                    <a class="btn-admin edit"
                       href="<?= SITE_URL ?>/pages/tarif_duzenle.php?id=<?= e($t['TarifID']) ?>">
                        <i class="fa-solid fa-pen-to-square"></i> Düzenle
                    </a>
                    <!--SİL-->
                    <a class="btn-admin delete"
                       onclick="return confirm('Bu tarifi silmek istediğine emin misin?')"
                       href="<?= SITE_URL ?>/pages/tarif_sil.php?id=<?= e($t['TarifID']) ?>&_csrf=<?= csrf_token() ?>">
                        <i class="fa-solid fa-trash"></i> Sil
                    </a>

                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php
/* Ortak footer dosyasının dahil edilmesi*/
require __DIR__ . '/../includes/footer.php';
?>
