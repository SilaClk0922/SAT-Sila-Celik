<?php
require __DIR__ . '/../includes/header.php';

// Kategorileri VT  çek
$kategoriler = $conn->query("
    SELECT KategoriID, KategoriAdi 
    FROM Kategoriler 
    ORDER BY KategoriAdi ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Kategori → resim eşleştirme
$kategoriResim = [
    "Ana Yemek" => "anayemek.jpg",
    "Çorba"     => "corba.jpg",
    "İçecek"    => "icecek.jpg",
    "Salata"    => "salata.jpg",
    "Tatlı"     => "tatli.jpg",
];
?>

<h2>Kategoriler</h2>
<div class="kategori-wrapper">

    <?php foreach ($kategoriler as $k): ?>

        <?php
            $resim = $kategoriResim[$k['KategoriAdi']] ?? "default.jpg";
            $resimUrl = SITE_URL . "/assets/kategori/" . $resim;
        ?>

        <a class="kategori-kart" 
           href="<?= SITE_URL ?>/pages/tarifler.php?kategori=<?= $k['KategoriID'] ?>">

            <!-- ÜSTTE RESİM -->
            <img src="<?= $resimUrl ?>" 
                 alt="<?= e($k['KategoriAdi']) ?>" 
                 style="
                    width:100%;
                    height:110px;
                    object-fit:cover;
                    border-radius:12px;
                    margin-bottom:12px;
                 ">

            <!-- Kategori Adı -->
            <div style="font-size:18px; font-weight:600;">
                <?= e($k['KategoriAdi']) ?>
            </div>

        </a>
    <?php endforeach; ?>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
