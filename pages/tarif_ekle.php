<?php
require __DIR__ . '/../includes/header.php';
require_login();

// Kategorileri veritabanından çek
try {
    $kats = $conn->query("SELECT KategoriID, KategoriAdi FROM Kategoriler ORDER BY KategoriAdi ASC");
    $kategoriListesi = $kats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('tarif', 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage(), 'err');
}

// FORM GÖNDERİLDİ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ad          = trim($_POST['baslik'] ?? '');
    $hazirlanis  = trim($_POST['aciklama'] ?? '');
    $malzemeler  = trim($_POST['malzemeler'] ?? '');
    $kategoriID  = trim($_POST['kategori'] ?? '');

    // Yeni alanlar
    $pisirmeSuresi = trim($_POST['pisirme_suresi'] ?? '');
    $kacKisilik    = (int)($_POST['kac_kisilik'] ?? 0);

    $csrf = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('tarif', 'Geçersiz güvenlik anahtarı.', 'err');
        redirect('/pages/tarif_ekle.php');
    }

    if ($ad === '' || $malzemeler === '') {
        flash('tarif', 'Tarif adı ve malzemeler zorunludur.', 'err');
        redirect('/pages/tarif_ekle.php');
    }

    // Resim yükleme
    $goruntu = null;
    if (!empty($_FILES['resim']['name'])) {
        $uploads = __DIR__ . '/../uploads/';
        if (!is_dir($uploads)) mkdir($uploads, 0777, true);

        $fileName = time() . '_' . basename($_FILES['resim']['name']);
        $target = $uploads . $fileName;

        if (move_uploaded_file($_FILES['resim']['tmp_name'], $target)) {
            $goruntu = 'uploads/' . $fileName;
        }
    }

    // Veritabanı kaydı
    try {
        $stmt = $conn->prepare("
            INSERT INTO Tarifler 
            (KullaniciID, KategoriID, TarifAdi, Malzemeler, Hazirlanis, Goruntu, 
             PisirmeSuresi, KacKisilik, EklemeTarihi, OnayDurumu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), 'Bekleyen')
        ");

        $stmt->execute([
            current_user_id(),
            $kategoriID ?: null,
            $ad,
            $malzemeler,
            $hazirlanis,
            $goruntu,
            $pisirmeSuresi,
            $kacKisilik
        ]);

        flash('auth', 'Tarif başarıyla eklendi ve admin onayına gönderildi! 🎉', 'ok');
        redirect('/pages/index.php');

    } catch (PDOException $e) {
        flash('tarif', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
        redirect('/pages/tarif_ekle.php');
    }
}
?>

<h2 class="tarif-ekle-title">🧑‍🍳 Yeni Tarif Ekle</h2>

<!-- FORMU ORTALAYAN KART YAPISI-->
<div class="tarif-ekle-wrapper">

<form method="post" enctype="multipart/form-data">
    <?= csrf_input() ?>

    <label>Tarif Adı:</label>
    <input type="text" name="baslik" required>

    <label>Açıklama (Hazırlanış):</label>
    <textarea name="aciklama" rows="3" placeholder="Hazırlanış hakkında kısa bir açıklama..."></textarea>

    <label>Malzemeler:</label>
    <textarea name="malzemeler" rows="4" placeholder="Malzemeleri satır satır yazın..." required></textarea>

    <label>Pişirme Süresi:</label>
    <input type="text" name="pisirme_suresi" placeholder="Örn: 30 dakika" required>

    <label>Kaç Kişilik:</label>
    <input type="number" name="kac_kisilik" placeholder="Örn: 4" required>

    <label>Kategori:</label>
    <select name="kategori" required>
        <option value="">Seçiniz</option>
        <?php foreach ($kategoriListesi as $k): ?>
            <option value="<?= e($k['KategoriID']) ?>">
                <?= e($k['KategoriAdi']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Resim (isteğe bağlı):</label>
    <input type="file" name="resim" accept="image/*">

    <button type="submit" style="width:100%; margin-top:15px;">
        Tarifi Ekle
    </button>
</form>

</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
