<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

//  Tüm tarifleri durumlarına göre çek
try {
    $tarifler = [
        'Bekleyen' => [],
        'Onaylı' => [],
        'Reddedildi' => []
    ];

    $sorgu = $conn->query("
        SELECT 
            t.TarifID, t.TarifAdi, t.Goruntu, t.EklemeTarihi,
            t.OnayDurumu, k.AdSoyad AS Ekleyen, c.KategoriAdi
        FROM Tarifler t
        LEFT JOIN Kullanicilar k ON t.KullaniciID = k.KullaniciID
        LEFT JOIN Kategoriler c ON t.KategoriID = c.KategoriID
        ORDER BY t.EklemeTarihi DESC
    ");

    while ($row = $sorgu->fetch(PDO::FETCH_ASSOC)) {
        $durum = $row['OnayDurumu'] ?? 'Bekleyen';
        $tarifler[$durum][] = $row;
    }
} catch (PDOException $e) {
    flash('tarif_onay', 'Tarifler yüklenemedi: ' . $e->getMessage(), 'err');
}

//  Onay / Red işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tarif_id'], $_POST['durum'])) {
    $tarifID = (int)$_POST['tarif_id'];
    $durum   = $_POST['durum'];
    $csrf    = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('tarif_onay', 'Güvenlik anahtarı geçersiz.', 'err');
        redirect('/pages/tarif_onay.php');
    }

    try {
        // AdminNotu artık yok
        $stmt = $conn->prepare("
            UPDATE Tarifler 
            SET OnayDurumu = ?, OnayTarihi = GETDATE()
            WHERE TarifID = ?
        ");
        $stmt->execute([$durum, $tarifID]);

        $msg = $durum === 'Onaylı' ? 'Tarif onaylandı 🎉' : 'Tarif reddedildi ❌';
        flash('tarif_onay', $msg, 'ok');
    } catch (PDOException $e) {
        flash('tarif_onay', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
    }

    redirect('/pages/tarif_onay.php');
}
?>

<h2> Tarif Yönetimi</h2>
<?php render_flash('tarif_onay'); ?>

<!-- Sekmeler -->
<div class="tab-container">
  <button class="tab-link active" data-tab="Bekleyen">🕓 Bekleyen</button>
  <button class="tab-link" data-tab="Onaylı">✅ Onaylı</button>
  <button class="tab-link" data-tab="Reddedildi">❌ Reddedilen</button>
</div>

<!-- Tab içerikleri -->
<?php foreach (['Bekleyen', 'Onaylı', 'Reddedildi'] as $durum): ?>
  <div class="tab-content <?= $durum === 'Bekleyen' ? 'active' : '' ?>" id="<?= $durum ?>">
    <div class="admin-section">
      <?php if (empty($tarifler[$durum])): ?>
        <p><?= e($durum) ?> tarif bulunmamaktadır.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Tarif Adı</th>
              <th>Kategori</th>
              <th>Ekleyen</th>
              <th>Tarih</th>
              <th>Durum</th>
              <th>İşlem</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($tarifler[$durum] as $t): ?>
            <tr>
              <td><?= e($t['TarifID']) ?></td>
              <td><?= e($t['TarifAdi']) ?></td>
              <td><?= e($t['KategoriAdi'] ?? '-') ?></td>
              <td><?= e($t['Ekleyen'] ?? '-') ?></td>
              <td><?= date('d.m.Y', strtotime($t['EklemeTarihi'])) ?></td>
              <td><span class="durum <?= strtolower($durum) ?>"><?= e($durum) ?></span></td>
              <td>
                <?php if ($durum === 'Bekleyen'): ?>
                  <button class="btn-mini green"
                          onclick="modalAc('Onaylı', <?= (int)$t['TarifID'] ?>)">Onayla</button>
                  <button class="btn-mini red"
                          onclick="modalAc('Reddedildi', <?= (int)$t['TarifID'] ?>)">Reddet</button>
                <?php endif; ?>

                <a class="btn-mini"
                   href="<?= SITE_URL ?>/pages/tarif_detay.php?id=<?= (int)$t['TarifID'] ?>">
                   Görüntüle
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

<!-- Modal – textarea YOK -->
<div id="onayModal" class="modal">
  <div class="modal-content">
    <span class="close" id="modalKapat">&times;</span>
    <h3 id="modalBaslik">Tarif İşlemi</h3>

    <form method="post">
      <?= csrf_input() ?>
      <input type="hidden" name="tarif_id" id="tarif_id">
      <input type="hidden" name="durum" id="durum">
      <button type="submit" id="modalButon" style="margin-top: 15px;">Kaydet</button>
    </form>
  </div>
</div>

<script>
// Sekmeler
const tabs = document.querySelectorAll(".tab-link");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(btn => {
  btn.addEventListener("click", () => {
    tabs.forEach(b => b.classList.remove("active"));
    contents.forEach(c => c.classList.remove("active"));
    btn.classList.add("active");
    document.getElementById(btn.dataset.tab).classList.add("active");
  });
});

// Modal
const modal   = document.getElementById("onayModal");
const span    = document.getElementById("modalKapat");
const idInput = document.getElementById("tarif_id");
const durumInput = document.getElementById("durum");
const baslik  = document.getElementById("modalBaslik");
const buton   = document.getElementById("modalButon");

function modalAc(durum, id) {
  modal.style.display = "block";
  idInput.value = id;
  durumInput.value = durum;

  baslik.innerText = (durum === 'Onaylı') ? "Tarifi Onayla" : "Tarifi Reddet";
  buton.innerText  = (durum === 'Onaylı') ? "Onayla"        : "Reddet";
  buton.style.background = (durum === 'Onaylı') ? "var(--brand)" : "#e74c3c";
}

span.onclick = () => modal.style.display = "none";
window.onclick = e => { if (e.target === modal) modal.style.display = "none"; }
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
