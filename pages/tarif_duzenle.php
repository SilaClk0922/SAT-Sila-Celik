<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

// ID alma
$tarifID = $_GET['id'] ?? null;
if (!$tarifID || !is_numeric($tarifID)) {
    flash('admin', 'Geçersiz tarif ID.', 'err');
    redirect('/pages/admin_panel.php');
}

// Tarif bilgilerini getir
$stmt = $conn->prepare("
    SELECT TarifAdi, Malzemeler, Yapilis, Goruntu, KategoriID
    FROM Tarifler
    WHERE TarifID = ?
");
$stmt->execute([$tarifID]);
$tarif = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarif) {
    flash('admin', 'Tarif bulunamadı.', 'err');
    redirect('/pages/admin_panel.php');
}

// Kategorileri çek
$kategoriler = $conn->query("
    SELECT KategoriID, KategoriAdi 
    FROM Kategoriler
    ORDER BY KategoriAdi ASC
")->fetchAll(PDO::FETCH_ASSOC);


// FORM POST OLDUĞUNDA GÜNCELLE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf = $_POST['_csrf'] ?? '';
    if (!csrf_verify($csrf)) {
        flash('admin', 'Güvenlik hatası!', 'err');
        redirect("/pages/tarif_duzenle.php?id=$tarifID");
    }

    $adi       = trim($_POST['adi']);
    $malzeme   = trim($_POST['malzemeler']);
    $yapilis   = trim($_POST['yapilis']);
    $kategori  = (int)$_POST['kategori'];

    // FOTOĞRAF GÜNCELLEME
    $yeniGorselYol = $tarif['Goruntu'];

    if (!empty($_FILES['gorsel']['name'])) {

        $uzanti = strtolower(pathinfo($_FILES['gorsel']['name'], PATHINFO_EXTENSION));
        $izinli = ['jpg','jpeg','png','webp'];

        if (!in_array($uzanti, $izinli)) {
            flash('admin', 'Sadece JPG, PNG, WEBP yükleyebilirsin.', 'err');
            redirect("/pages/tarif_duzenle.php?id=$tarifID");
        }

        $yeniDosya = 'uploads/tarif_' . time() . '.' . $uzanti;
        $hedef = __DIR__ . '/../' . $yeniDosya;

        // yeni foto yükle
        move_uploaded_file($_FILES['gorsel']['tmp_name'], $hedef);

        // eskiyi sil
        if (!empty($tarif['Goruntu'])) {
            $eski = __DIR__ . '/../' . $tarif['Goruntu'];
            if (file_exists($eski)) unlink($eski);
        }

        $yeniGorselYol = $yeniDosya;
    }

    // VERİTABANI GÜNCELLEME
    $guncelle = $conn->prepare("
        UPDATE Tarifler
        SET TarifAdi = ?, Malzemeler = ?, Yapilis = ?, Goruntu = ?, KategoriID = ?
        WHERE TarifID = ?
    ");
    $guncelle->execute([$adi, $malzeme, $yapilis, $yeniGorselYol, $kategori, $tarifID]);

    flash('admin', 'Tarif başarıyla güncellendi!', 'ok');
    redirect('/pages/admin_panel.php');
}

?>

<h2>Tarifi Düzenle</h2>

<form method="post" enctype="multipart/form-data" class="form">
    <?= csrf_input() ?>

    <label>Tarif Adı:</label>
    <input type="text" name="adi" value="<?= e($tarif['TarifAdi']) ?>" required>

    <label>Kategori:</label>
    <select name="kategori" required>
        <?php foreach ($kategoriler as $kat): ?>
            <option value="<?= $kat['KategoriID'] ?>"
                <?= $kat['KategoriID'] == $tarif['KategoriID'] ? 'selected' : '' ?> >
                <?= e($kat['KategoriAdi']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Malzemeler:</label>
    <textarea name="malzemeler" rows="4" required><?= e($tarif['Malzemeler']) ?></textarea>

    <label>Yapılışı:</label>
    <textarea name="yapilis" rows="6" required><?= e($tarif['Yapilis']) ?></textarea>

    <label>Mevcut Fotoğraf:</label><br>
    <img src="<?= SITE_URL . '/' . $tarif['Goruntu'] ?>" style="width:180px; border-radius:10px;">

    <br><br>

    <label>Yeni Fotoğraf (isteğe bağlı):</label>
    <input type="file" name="gorsel">

    <button type="submit" class="btn">Kaydet</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
