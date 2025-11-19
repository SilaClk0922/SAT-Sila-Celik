<?php
require __DIR__ . '/../includes/header.php';
require_login(); // yalnızca giriş yapan kullanıcılar

//  Kategorileri veritabanından çek
$kategoriListesi = [];
try {
    $katSorgu = $conn->query("SELECT KategoriID, KategoriAdi FROM Kategoriler ORDER BY KategoriAdi ASC");
    $kategoriListesi = $katSorgu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('tarif', 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage(), 'err');
}

//  Form gönderimi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarifAdi = trim($_POST['baslik'] ?? '');
    $hazirlanis = trim($_POST['aciklama'] ?? '');
    $malzemeler = trim($_POST['malzemeler'] ?? '');
    $kategoriID = trim($_POST['kategori'] ?? '');
    $csrf = $_POST['_csrf'] ?? '';

    // Güvenlik kontrolü
    if (!csrf_verify($csrf)) {
        flash('tarif', 'Geçersiz güvenlik anahtarı. Lütfen tekrar deneyin.', 'err');
        redirect('/pages/tarif_ekle.php');
    }

    // Zorunlu alanlar
    if ($tarifAdi === '' || $malzemeler === '') {
        flash('tarif', 'Tarif adı ve malzemeler boş bırakılamaz.', 'err');
        redirect('/pages/tarif_ekle.php');
    }

    //  Resim yükleme işlemi
    $goruntu = null;
    if (!empty($_FILES['resim']['name'])) {
        $uploadsDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

        $fileName = time() . '_' . basename($_FILES['resim']['name']);
        $targetPath = $uploadsDir . $fileName;

        if (move_uploaded_file($_FILES['resim']['tmp_name'], $targetPath)) {
            $goruntu = 'uploads/' . $fileName;
        }
    }

    //  Veritabanına ekle (OnayDurumu = Beklemede)
    try {
        $sql = "
            INSERT INTO Tarifler 
            (KullaniciID, KategoriID, TarifAdi, Malzemeler, Hazirlanis, Goruntu, EklemeTarihi, OnayDurumu)
            VALUES (?, ?, ?, ?, ?, ?, GETDATE(), 'Beklemede')
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            current_user_id(),
            $kategoriID ?: null,
            $tarifAdi,
            $malzemeler,
            $hazirlanis,
            $goruntu
        ]);

        flash('auth', 'Tarif başarıyla eklendi ve admin onayına gönderildi! 🎉', 'ok');
        redirect('/pages/index.php');

    } catch (PDOException $e) {
        flash('tarif', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
        redirect('/pages/tarif_ekle.php');
    }
}
?>

<div class="form-card">
    <h2>🧑‍🍳 Yeni Tarif Ekle</h2>
    <?php render_flash(); ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_input() ?>

        <label>Tarif Adı:</label>
        <input type="text" name="baslik" required>

        <label>Açıklama (Hazırlanış):</label>
        <textarea name="aciklama" rows="4" placeholder="Hazırlanışı hakkında kısa bir açıklama..."></textarea>

        <label>Malzemeler:</label>
        <textarea name="malzemeler" rows="4" placeholder="Malzemeleri satır satır yazın..." required></textarea>

        <label>Kategori:</label>
        <select name="kategori" required>
            <option value="">Seçiniz</option>
            <?php foreach ($kategoriListesi as $kat): ?>
                <option value="<?= e($kat['KategoriID']) ?>">
                    <?= e($kat['KategoriAdi']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Resim (isteğe bağlı):</label>
        <input type="file" name="resim" accept="image/*">

        <button type="submit">Tarifi Ekle</button>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
