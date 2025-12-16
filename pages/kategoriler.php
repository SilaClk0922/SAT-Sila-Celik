<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

//  KATEGORİ EKLEME
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kategori_adi']) && !isset($_POST['duzenle_id'])) {
    $kategoriAdi = trim($_POST['kategori_adi'] ?? '');
    $csrf        = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('kategori', 'Geçersiz güvenlik anahtarı.', 'err');
        redirect('/pages/kategoriler.php');
    }

    if ($kategoriAdi === '') {
        flash('kategori', 'Kategori adı boş bırakılamaz.', 'err');
        redirect('/pages/kategoriler.php');
    }

    try {
        $stmt = $conn->prepare("INSERT INTO Kategoriler (KategoriAdi) VALUES (?)");
        $stmt->execute([$kategoriAdi]);
        flash('kategori', 'Kategori başarıyla eklendi 🎉', 'ok');
    } catch (PDOException $e) {
        flash('kategori', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
    }

    redirect('/pages/kategoriler.php');
}

//  KATEGORİ DÜZENLEME
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duzenle_id']) && $_POST['duzenle_id'] !== '') {
    $id   = (int)($_POST['duzenle_id'] ?? 0);
    $adi  = trim($_POST['kategori_adi'] ?? '');
    $csrf = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('kategori', 'Geçersiz güvenlik anahtarı.', 'err');
        redirect('/pages/kategoriler.php');
    }

    if ($adi === '') {
        flash('kategori', 'Kategori adı boş bırakılamaz.', 'err');
        redirect('/pages/kategoriler.php');
    }

    try {
        $stmt = $conn->prepare("UPDATE Kategoriler SET KategoriAdi = ? WHERE KategoriID = ?");
        $stmt->execute([$adi, $id]);
        flash('kategori', 'Kategori başarıyla güncellendi ✅', 'ok');
    } catch (PDOException $e) {
        flash('kategori', 'Düzenleme hatası: ' . $e->getMessage(), 'err');
    }

    redirect('/pages/kategoriler.php');
}

//  KATEGORİ SİLME
if (isset($_GET['sil'])) {
    $id = (int)$_GET['sil'];

    try {
        $stmt = $conn->prepare("DELETE FROM Kategoriler WHERE KategoriID = ?");
        $stmt->execute([$id]);
        flash('kategori', 'Kategori silindi 🗑️', 'ok');
    } catch (PDOException $e) {
        flash('kategori', 'Silme hatası: ' . $e->getMessage(), 'err');
    }

    redirect('/pages/kategoriler.php');
}

//  KATEGORİLERİ LİSTELE
try {
    $kategoriler = $conn
        ->query("SELECT KategoriID, KategoriAdi FROM Kategoriler ORDER BY KategoriAdi ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('kategori', 'Kategoriler yüklenemedi: ' . $e->getMessage(), 'err');
    $kategoriler = [];
}
?>

<main class="container">
    <h2> Kategori Yönetimi</h2>
    <?php render_flash('kategori'); ?>

    <div class="admin-section">

        <div style="display:flex; justify-content:flex-end; margin-bottom:15px;">
            <button type="button" id="kategoriEkleBtn" class="btn-admin btn-edit">
                + Yeni Kategori Ekle
            </button>
        </div>

        <table class="admin-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Kategori Adı</th>
                <th>İşlemler</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($kategoriler as $kat): ?>
                <tr>
                    <td><?= e($kat['KategoriID']) ?></td>
                    <td><?= e($kat['KategoriAdi']) ?></td>
                    <td>
                        <div class="admin-actions">

                            <!-- DÜZENLE -->
                            <a href="#!"
                               class="btn-admin btn-edit"
                               onclick="kategoriDuzenle(<?= (int)$kat['KategoriID'] ?>, '<?= e($kat['KategoriAdi']) ?>'); return false;">
                                Düzenle
                            </a>

                            <!-- SİL -->
                            <a href="?sil=<?= e($kat['KategoriID']) ?>"
                               class="btn-admin btn-delete"
                               onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                Sil
                            </a>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</main>

<!-- KATEGORİ MODEL -->
<div id="kategoriModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" id="modalKapat">&times;</span>
        <h3 id="modalBaslik">Yeni Kategori Ekle</h3>

        <form method="post" id="kategoriForm">
            <?= csrf_input() ?>

            <input type="hidden" name="duzenle_id" id="duzenle_id">

            <label for="kategori_adi">Kategori Adı:</label>
            <input type="text" name="kategori_adi" id="kategori_adi" required>

            <button type="submit" class="btn-admin btn-view" id="modalButon">
                Kaydet
            </button>
        </form>
    </div>
</div>

<script>
const modal      = document.getElementById("kategoriModal");
const btn        = document.getElementById("kategoriEkleBtn");
const spanClose  = document.getElementById("modalKapat");
const form       = document.getElementById("kategoriForm");
const baslik     = document.getElementById("modalBaslik");
const duzenleID  = document.getElementById("duzenle_id");
const kategoriAd = document.getElementById("kategori_adi");
const modalBtn   = document.getElementById("modalButon");

// Yeni kategori ekleme
btn.onclick = function () {
    form.reset();
    baslik.innerText    = "Yeni Kategori Ekle";
    modalBtn.innerText  = "Ekle";
    duzenleID.value     = "";
    modal.style.display = "block";
};

spanClose.onclick = function () {
    modal.style.display = "none";
};

window.onclick = function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
};

// Düzenleme
function kategoriDuzenle(id, ad) {
    form.reset();
    baslik.innerText    = "Kategoriyi Düzenle";
    modalBtn.innerText  = "Güncelle";
    duzenleID.value     = id;
    kategoriAd.value    = ad;
    modal.style.display = "block";
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
