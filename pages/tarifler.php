<?php
require __DIR__ . '/../includes/header.php';

// Kategori seçilmiş mi?
$kategoriID = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;

// Arama parametresi (opsiyonel)
$ara = trim($_GET['ara'] ?? '');

// Kategori adını çekelim (başlıkta göstermek için)
$kategoriAdi = "Tüm Tarifler";

if ($kategoriID > 0) {
    $stmtKategori = $conn->prepare("SELECT KategoriAdi FROM Kategoriler WHERE KategoriID = ?");
    $stmtKategori->execute([$kategoriID]);
    $kategoriRow = $stmtKategori->fetch(PDO::FETCH_ASSOC);

    if ($kategoriRow) {
        $kategoriAdi = $kategoriRow['KategoriAdi'];
    }
}

//TARİFLERİ ÇEK 
try {

    $sql = "
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
    ";

    $params = [];

    // Kategori filtresi
    if ($kategoriID > 0) {
        $sql .= " AND t.KategoriID = ? ";
        $params[] = $kategoriID;
    }

    // Arama filtresi
    if ($ara !== '') {
        $sql .= " AND t.TarifAdi LIKE ? ";
        $params[] = "%$ara%";
    }

    $sql .= " ORDER BY t.EklemeTarihi DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $tarifler = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    flash('tarifler', 'Tarifler yüklenirken hata oluştu: ' . $e->getMessage(), 'err');
    $tarifler = [];
}

?>

<!--SAYFA BAŞLIĞI -->
<h2 class="page-title">Tüm Tarifler</h2>

<main class="container">

    <div class="tarif-listesi">

        <?php if (empty($tarifler)): ?>

            <p style="text-align:center; width:100%; margin-top:20px;">
                Bu kategoriye ait tarif bulunamadı.
            </p>

        <?php else: ?>

            <?php foreach ($tarifler as $t): ?>
                <div class="tarif-kart">

                    <!-- RESİM -->
                    <div class="tarif-resim">
                        <img src="<?= !empty($t['Goruntu']) ? SITE_URL.'/'.$t['Goruntu'] : SITE_URL.'/assets/no-image.png' ?>" 
                             alt="<?= e($t['TarifAdi']) ?>">
                    </div>

                    <!-- İÇERİK -->
                    <div class="tarif-icerik">

                        <h3>
                            <a href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">
                                <?= e($t['TarifAdi']) ?>
                            </a>
                        </h3>

                        <div class="tarif-bilgi-satir">
                            <i class="fa-solid fa-folder"></i>
                            <?= e($t['KategoriAdi']) ?>
                        </div>

                        <div class="tarif-bilgi-satir">
                            <i class="fa-solid fa-user"></i>
                            <?= e($t['Ekleyen']) ?>
                        </div>

                        <div class="tarif-bilgi-satir">
                            <i class="fa-solid fa-calendar"></i>
                            <?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?>
                        </div>

                        <a class="tarif-detay-btn" 
                           href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= e($t['TarifID']) ?>">
                            Detayını Gör
                        </a>

                    </div>

                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
