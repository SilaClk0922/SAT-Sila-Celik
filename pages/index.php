<?php
require __DIR__ . '/../includes/header.php';
?>

<!-- BAŞLANGIÇ SLIDER -->
<div id="anasayfaSlider"
     class="carousel slide full-slider"
     data-bs-ride="carousel"
     data-bs-interval="4000"
     data-bs-pause="false">

    <div class="carousel-inner">

        <div class="carousel-item active">
            <div class="slider-blur"
                 style="background-image: url('<?= SITE_URL ?>/assets/slider1.jpg');"></div>
            <div class="slider-image"
                 style="background-image: url('<?= SITE_URL ?>/assets/slider1.jpg');"></div>
        </div>

        <div class="carousel-item">
            <div class="slider-blur"
                 style="background-image: url('<?= SITE_URL ?>/assets/slider2.jpg');"></div>
            <div class="slider-image"
                 style="background-image: url('<?= SITE_URL ?>/assets/slider2.jpg');"></div>
        </div>

        <div class="carousel-item">
            <div class="slider-blur"
                 style="background-image: url('<?= SITE_URL ?>/assets/slider3.jpg');"></div>
            <div class="slider-image"
                 style="background-image: url('<?= SITE_URL ?>/assets/slider3.jpg');"></div>
        </div>

    </div>

    <!-- OKLAR -->
    <button class="carousel-control-prev" type="button"
            data-bs-target="#anasayfaSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button"
            data-bs-target="#anasayfaSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

    <!-- SLİDER GEÇİŞ -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#anasayfaSlider" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#anasayfaSlider" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#anasayfaSlider" data-bs-slide-to="2"></button>
    </div>
</div>
<!-- SLIDER BİTİŞ  -->


<?php
// Kategoriler
$kategoriQuery = $conn->query("SELECT KategoriID, KategoriAdi FROM Kategoriler ORDER BY KategoriAdi ASC");
$kategoriler = $kategoriQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- KATEGORİLER  -->
<h2 class="kategori-baslik">Kategoriler</h2>

<div class="kategori-wrapper">
    <?php foreach ($kategoriler as $k): ?>
        <a class="kategori-kart"
           href="<?= SITE_URL ?>/pages/tarifler.php?kategori=<?= (int)$k['KategoriID'] ?>">
            <?= e($k['KategoriAdi']) ?>
        </a>
    <?php endforeach; ?>
</div>


<!--  SON EKLENEN TARİFLER  -->
<h2 class="kategori-baslik">Son Eklenen Tarifler</h2>
<?php
$sonTarifler = $conn->query("
    SELECT TOP 6 TarifID, TarifAdi, Goruntu
    FROM Tarifler
    WHERE OnayDurumu = 'Onaylı'
    ORDER BY TarifID DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="home-tarif-liste">

<?php foreach ($sonTarifler as $t): ?>
    <div class="home-tarif-kart">

        <img class="home-tarif-img"
             src="<?= !empty($t['Goruntu']) ? SITE_URL.'/'.$t['Goruntu'] : SITE_URL.'/assets/no-image.png' ?>"
             alt="<?= e($t['TarifAdi']) ?>">

        <div class="home-tarif-icerik">
            <h3><?= e($t['TarifAdi']) ?></h3>

            <a class="tarif-btn"
               href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">
                Tarifi Gör
            </a>
        </div>

    </div>
<?php endforeach; ?>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
