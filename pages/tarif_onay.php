<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

/* Arama + Kategori Filtresi*/
$ara = trim($_GET['ara'] ?? '');
$kategori = $_GET['kategori'] ?? '';

/* Tarifleri Listele (Gruplu)*/
try {
    $sql = "
        SELECT 
            t.TarifID, t.TarifAdi, t.Goruntu, t.EklemeTarihi,
            t.OnayDurumu, k.AdSoyad AS Ekleyen, 
            c.KategoriAdi, t.KategoriID
        FROM Tarifler t
        LEFT JOIN Kullanicilar k ON t.KullaniciID = k.KullaniciID
        LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
        WHERE 1=1
    ";

    $params = [];

    if ($ara !== "") {
        $sql .= " AND t.TarifAdi LIKE ? ";
        $params[] = "%$ara%";
    }

    if ($kategori !== "") {
        $sql .= " AND t.KategoriID = ? ";
        $params[] = $kategori;
    }

    $sql .= " ORDER BY t.EklemeTarihi DESC";
    $s = $conn->prepare($sql);
    $s->execute($params);
    $ham = $s->fetchAll(PDO::FETCH_ASSOC);

    /* Gruplandır */
    $tarifler = [
        "Bekleyen"   => [],
        "Onaylı"     => [],
        "Reddedildi" => []
    ];

    foreach ($ham as $t) {
        $d = strtolower($t["OnayDurumu"] ?? "");

        if ($d === "" || $d === "bekleyen")        
            $grup = "Bekleyen";
        elseif ($d === "onaylı" || $d === "onayli") 
            $grup = "Onaylı";
        elseif ($d === "reddedildi")               
            $grup = "Reddedildi";
        else                                        
            $grup = "Bekleyen";

        $tarifler[$grup][] = $t;
    }

} catch (PDOException $e) {
    flash('tarif_onay', 'Tarifler yüklenemedi: ' . $e->getMessage(), 'err');
}
?>

<h2 style="text-align:center; margin-top:20px;"> Tarif Onay Paneli</h2>
<?php render_flash('tarif_onay'); ?>

<style>
    .onay-kart-liste {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .onay-kart {
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border: 1px solid #eee;
    }
    .onay-kart h4 {
        margin-bottom: 6px;
        font-size: 20px;
        color: #6a30c7;
    }
    .onay-kart .bilgi {
        font-size: 14px;
        color: #555;
        margin-bottom: 3px;
    }
    .durum-etiket {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
    }
    .durum-bekleyen { background:#fff3cd; color:#b8860b; }
    .durum-onaylı { background:#d4f5d6; color:#1d8a3a; }
    .durum-reddedildi { background:#ffd6d6; color:#b33939; }

    .onay-kart-actions {
        margin-top: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .btn-mini {
        padding: 8px 14px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        color: white;
        display: inline-block;
    }
    .btn-onayla { background:#28a745; }
    .btn-reddet { background:#dc3545; }
    .btn-bekle { background:#f39c12; }
    .btn-view { background:#7b4bbe; }
</style>

<?php
$gruplar = [
    "Bekleyen"   => "🔄 Bekleyen Tarifler",
    "Onaylı"     => "✅ Onaylı Tarifler",
    "Reddedildi" => "❌ Reddedilmiş Tarifler"
];

foreach ($gruplar as $durum => $baslik):
?>
<div style="margin-top:40px;">
    <h3><?= $baslik ?></h3>

    <?php if (empty($tarifler[$durum])): ?>
        <p style="background:white; padding:15px; border-radius:10px;">Bu kategoride tarif yok.</p>

    <?php else: ?>

    <div class="onay-kart-liste">

        <?php foreach ($tarifler[$durum] as $t): ?>
        <div class="onay-kart">

            <h4><?= e($t['TarifAdi']) ?></h4>
            <div class="bilgi"><b>Kategori:</b> <?= e($t['KategoriAdi']) ?></div>
            <div class="bilgi"><b>Ekleyen:</b> <?= e($t['Ekleyen']) ?></div>
            <div class="bilgi"><b>Tarih:</b> <?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></div>

            <span class="durum-etiket durum-<?= strtolower($durum) ?>">
                <?= $durum ?>
            </span>

            <div class="onay-kart-actions">

                <?php if ($durum === "Bekleyen"): ?>

                    <!--Admin notu ile onaylama -->
                    <a class="btn-mini btn-onayla"
                       href="<?= SITE_URL ?>/pages/tarif_not.php?id=<?= $t['TarifID'] ?>&durum=Onaylı">
                        Onayla
                    </a>

                    <!--Admin notu ile reddetme -->
                    <a class="btn-mini btn-reddet"
                       href="<?= SITE_URL ?>/pages/tarif_not.php?id=<?= $t['TarifID'] ?>&durum=Reddedildi">
                        Reddet
                    </a>

                <?php else: ?>

                    <!-- Geri beklemeye al  -->
                    <a class="btn-mini btn-bekle"
                       href="<?= SITE_URL ?>/pages/tarif_not.php?id=<?= $t['TarifID'] ?>&durum=Bekleyen">
                        Beklemeye Al
                    </a>

                <?php endif; ?>

                <a class="btn-mini btn-view"
                   href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= $t['TarifID'] ?>">
                    Görüntüle
                </a>

            </div>
        </div>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>
</div>
<?php endforeach; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
