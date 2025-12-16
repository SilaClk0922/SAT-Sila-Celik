<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || current_user_role() !== 'Admin') {
    redirect('/pages/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/tarif_onay.php');
    exit;
}

$id    = (int)($_POST['id'] ?? 0);
$durum = $_POST['durum'] ?? '';
$not   = trim($_POST['not'] ?? '');
$csrf  = $_POST['_csrf'] ?? '';

if (!csrf_verify($csrf)) {
    flash('tarif_onay', 'Güvenlik doğrulaması başarısız (CSRF).', 'err');
    redirect('/pages/tarif_onay.php');
    exit;
}
/* GEÇERLİ DURUMLARIN TANIMLANMASI */
$allowed = ['Onaylı', 'Reddedildi', 'Bekleyen'];

if ($id <= 0 || !in_array($durum, $allowed, true)) {
    flash('tarif_onay', 'Geçersiz işlem.', 'err');
    redirect('/pages/tarif_onay.php');
    exit;
}

/* TARİF ONAY BİLGİLERİNİN GÜNCELLENMESİ
       - Onay durumu
       - Onay tarihi
       - Admin notu */
try {
    $sql = "
        UPDATE Tarifler
        SET OnayDurumu = ?, 
            OnayTarihi  = GETDATE(),
            AdminNot    = ?
        WHERE TarifID   = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$durum, $not, $id]);

    flash('tarif_onay', 'Tarif durumu ve admin notu başarıyla kaydedildi.', 'ok');

} catch (PDOException $e) {
    flash('tarif_onay', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
}

redirect('/pages/tarif_onay.php');
exit;
