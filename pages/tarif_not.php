<?php
// Header dahil, böylece hem db hem functions hem session hazır gelir
require __DIR__ . '/../includes/header.php';
require_role('Admin'); // sadece admin erişsin

$id    = (int)($_GET['id'] ?? 0);
$durum = $_GET['durum'] ?? '';

$allowed = ['Onaylı', 'Reddedildi', 'Bekleyen'];

if ($id <= 0 || !in_array($durum, $allowed, true)) {
    flash('tarif_onay', 'Geçersiz tarif veya durum bilgisi.', 'err');
    redirect('/pages/tarif_onay.php');
    exit;
}
?>

<style>
.not-container {
    max-width: 650px;
    margin: 40px auto;
    background: #ffffff;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
.not-container h2 {
    font-size: 26px;
    color: #7b4bbe;
    margin-bottom: 10px;
    text-align: center;
}
.not-container p.aciklama {
    text-align: center;
    color: #666;
    margin-bottom: 20px;
}
.not-container label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: #444;
}
.not-container textarea {
    width: 100%;
    min-height: 140px;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #ddd;
    resize: vertical;
}
.not-container .btn-kaydet {
    margin-top: 18px;
    padding: 10px 22px;
    border-radius: 999px;
    border: none;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    background: #7b4bbe;
}
.not-container .btn-kaydet:hover {
    opacity: .9;
}
</style>

<div class="not-container">
    <h2>Tarif İçin Not Yaz</h2>
    <p class="aciklama">
        Bu tarifin durumunu <strong><?= e($durum) ?></strong> olarak güncelleyeceksin.<br>
        Kullanıcıya görünecek kısa bir açıklama yazabilirsin.
    </p>

    <form action="<?= SITE_URL ?>/pages/tarif_not_kaydet.php" method="POST">
        <?= csrf_input() ?>
        <input type="hidden" name="id"    value="<?= $id ?>">
        <input type="hidden" name="durum" value="<?= e($durum) ?>">

        <label for="not">Admin Notu</label>
        <textarea id="not" name="not"
                  placeholder="Örneğin: Tarif güzel ama malzeme listesi eksik, lütfen güncelleyip tekrar gönder..."></textarea>

        <button type="submit" class="btn-kaydet">Kaydet ve Geri Dön</button>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
