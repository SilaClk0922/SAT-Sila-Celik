<?php
require __DIR__ . '/../includes/header.php';
require_login();

// Tarif ID
$tarifID = $_GET['id'] ?? null;

if (!$tarifID || !is_numeric($tarifID)) {
    flash('genel', 'Geçersiz tarif ID.', 'err');
    redirect('/pages/index.php');
}

// Tarif verisi
$stmt = $conn->prepare("
    SELECT t.*, k.KategoriAdi 
    FROM Tarifler t
    LEFT JOIN Kategoriler k ON t.KategoriID = k.KategoriID
    WHERE t.TarifID = ?
");
$stmt->execute([$tarifID]);
$tarif = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarif) {
    flash('genel', 'Tarif bulunamadı.', 'err');
    redirect('/pages/index.php');
}

// Yetki kontrolü
if (current_user_role() !== 'Admin' && $tarif['KullaniciID'] != current_user_id()) {
    flash('genel', 'Bu tarifi düzenleme yetkin yok.', 'err');
    redirect('/pages/index.php');
}

// Tüm kategoriler
$kategoriler = $conn->query("SELECT * FROM Kategoriler ORDER BY KategoriAdi ASC")->fetchAll(PDO::FETCH_ASSOC);

// FORM POST işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $isim       = trim($_POST['tarif_adi']);
    $kategori   = (int)$_POST['kategori_id'];
    $malzeme    = trim($_POST['malzemeler']);
    $hazirlanis = trim($_POST['hazirlanis']);
    $csrf       = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('genel', 'Güvenlik doğrulaması başarısız.', 'err');
        redirect("/pages/tarif_duzenle.php?id=$tarifID");
    }

    // Fotoğraf işlemi
    $fotoPath = $tarif['Goruntu'];

    if (!empty($_FILES['foto']['name'])) {

        $dosyaAdi = time() . '_' . basename($_FILES['foto']['name']);
        $hedef = __DIR__ . '/../uploads/' . $dosyaAdi;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $hedef)) {

            // Eski fotoğrafı sil
            if (!empty($tarif['Goruntu'])) {
                $eski = __DIR__ . '/../' . $tarif['Goruntu'];
                if (file_exists($eski)) unlink($eski);
            }

            $fotoPath = 'uploads/' . $dosyaAdi;
        }
    }

    // Veritabanını güncelle
    $update = $conn->prepare("
        UPDATE Tarifler
        SET TarifAdi = ?, KategoriID = ?, Malzemeler = ?, Hazirlanis = ?, Goruntu = ?
        WHERE TarifID = ?
    ");
    $update->execute([$isim, $kategori, $malzeme, $hazirlanis, $fotoPath, $tarifID]);

    flash('genel', 'Tarif başarıyla güncellendi 🎉', 'ok');

    // Yönlendirme
    if (current_user_role() === 'Admin') {
        redirect('/pages/admin_tariflerim.php');
    } else {
        redirect('/pages/kullanici_paneli.php');
    }
    exit;
}
?>

<style>
.form-modern {
    max-width: 700px;
    margin: 30px auto;
    padding: 25px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.form-modern input,
.form-modern textarea,
.form-modern select {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ccc;
    margin-bottom: 15px;
}

/* Güzel güncelle butonu */
.update-btn {
    background: #b58bff;
    color: white;
    padding: 12px 28px;
    border-radius: 30px;
    border: none;
    font-size: 17px;
    font-weight: 600;
    display: block;
    margin: 20px auto 0 auto;
    text-align: center;
    cursor: pointer;
    transition: 0.25s ease;
    box-shadow: 0 4px 12px rgba(181,139,255,0.4);
}
.update-btn:hover {
    background: #9b60e7;
    transform: translateY(-2px);
}

/* Fotoğraf preview */
.preview-box {
    text-align: center;
    margin: 15px 0;
}
.preview-box img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    display: block;
    margin: auto;
}
</style>

<h2 style="text-align:center; margin-top:15px; color:#7b4bbe;">
    ✏️ Tarif Düzenle
</h2>

<?php render_flash(); ?>

<form method="post" enctype="multipart/form-data" class="form-modern">

    <?= csrf_input() ?>

    <label><strong>Tarif Adı</strong></label>
    <input type="text" name="tarif_adi" value="<?= e($tarif['TarifAdi']) ?>" required>

    <label><strong>Kategori</strong></label>
    <select name="kategori_id" required>
        <?php foreach ($kategoriler as $k): ?>
            <option value="<?= $k['KategoriID'] ?>"
                <?= $k['KategoriID'] == $tarif['KategoriID'] ? 'selected' : '' ?>>
                <?= e($k['KategoriAdi']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label><strong>Malzemeler</strong></label>
    <textarea name="malzemeler" rows="5" required><?= e($tarif['Malzemeler']) ?></textarea>

    <label><strong>Yapılışı</strong></label>
    <textarea name="hazirlanis" rows="6" required><?= e($tarif['Hazirlanis']) ?></textarea>

    <label><strong>Fotoğraf</strong></label>
    <input type="file" name="foto">

    <!-- Fotoğraf varsa göster -->
    <?php if (!empty($tarif['Goruntu']) && file_exists(__DIR__ . '/../' . $tarif['Goruntu'])): ?>
        <div class="preview-box">
            <img src="<?= SITE_URL ?>/<?= $tarif['Goruntu'] ?>" alt="Tarif Fotoğrafı">
        </div>
    <?php endif; ?>

    <button type="submit" class="update-btn">Güncelle</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
