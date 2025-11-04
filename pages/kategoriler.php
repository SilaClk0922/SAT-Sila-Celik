<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

// 🔹 Kategori ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kategori_adi'])) {
    $kategoriAdi = trim($_POST['kategori_adi']);
    $csrf = $_POST['_csrf'] ?? '';

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

// 🔹 Kategori silme
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

// 🔹 Kategori düzenleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duzenle_id'])) {
    $id = (int)$_POST['duzenle_id'];
    $adi = trim($_POST['duzenle_adi']);
    $csrf = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('kategori', 'Geçersiz güvenlik anahtarı.', 'err');
        redirect('/pages/kategoriler.php');
    }

    try {
        $stmt = $conn->prepare("UPDATE Kategoriler SET KategoriAdi = ? WHERE KategoriID = ?");
        $stmt->execute([$adi, $id]);
        flash('kategori', 'Kategori başarıyla güncellendi ✏️', 'ok');
    } catch (PDOException $e) {
        flash('kategori', 'Düzenleme hatası: ' . $e->getMessage(), 'err');
    }

    redirect('/pages/kategoriler.php');
}

// 🔹 Kategorileri listele
try {
    $kategoriler = $conn->query("SELECT * FROM Kategoriler ORDER BY KategoriAdi ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    flash('kategori', 'Kategoriler yüklenemedi: ' . $e->getMessage(), 'err');
}
?>

<h2>📂 Kategori Yönetimi</h2>
<?php render_flash('kategori'); ?>

<div class="admin-section">
  <div class="kategori-header">
    <button class="btn" id="kategoriEkleBtn">+ Yeni Kategori Ekle</button>
  </div>

  <table>
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
            <button class="btn-mini" onclick="kategoriDuzenle(<?= e($kat['KategoriID']) ?>, '<?= e($kat['KategoriAdi']) ?>')">Düzenle</button>
            <a class="btn-mini red" href="?sil=<?= e($kat['KategoriID']) ?>" onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- 🟣 Modal Penceresi -->
<div id="kategoriModal" class="modal">
  <div class="modal-content">
    <span class="close" id="modalKapat">&times;</span>
    <h3 id="modalBaslik">Yeni Kategori Ekle</h3>

    <form method="post" id="kategoriForm">
      <?= csrf_input() ?>
      <input type="hidden" name="duzenle_id" id="duzenle_id">
      <label>Kategori Adı:</label>
      <input type="text" name="kategori_adi" id="kategori_adi" required>
      <button type="submit" id="modalButon">Kaydet</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById("kategoriModal");
const btn = document.getElementById("kategoriEkleBtn");
const span = document.getElementById("modalKapat");
const form = document.getElementById("kategoriForm");
const baslik = document.getElementById("modalBaslik");
const duzenleID = document.getElementById("duzenle_id");
const kategoriAdi = document.getElementById("kategori_adi");
const modalButon = document.getElementById("modalButon");

// Modal aç
btn.onclick = function() {
  form.reset();
  baslik.innerText = "Yeni Kategori Ekle";
  modalButon.innerText = "Ekle";
  duzenleID.value = "";
  modal.style.display = "block";
}

// Modal kapat
span.onclick = function() { modal.style.display = "none"; }
window.onclick = function(event) {
  if (event.target === modal) modal.style.display = "none";
}

// Düzenleme işlemi
function kategoriDuzenle(id, ad) {
  modal.style.display = "block";
  baslik.innerText = "Kategoriyi Düzenle";
  modalButon.innerText = "Güncelle";
  duzenleID.value = id;
  kategoriAdi.value = ad;
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
