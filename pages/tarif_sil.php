<?php
require __DIR__ . '/../includes/header.php';
require_login();

$tarifID = $_GET['id'] ?? null;

if (!$tarifID || !is_numeric($tarifID)) {
    flash('genel', 'Geçersiz tarif ID.', 'err');
    redirect('/pages/kullanici_paneli.php');
}

// Tarif bilgisi
$stmt = $conn->prepare("SELECT * FROM Tarifler WHERE TarifID = ?");
$stmt->execute([$tarifID]);
$tarif = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarif) {
    flash('genel', 'Tarif bulunamadı!', 'err');
    redirect('/pages/kullanici_paneli.php');
}

// Yetki kontrolü
if (current_user_role() !== 'Admin' && $tarif['KullaniciID'] != current_user_id()) {
    flash('genel', 'Bu tarifi silme yetkin yok.', 'err');
    redirect('/pages/kullanici_paneli.php');
}

// Fotoğrafı sil
if (!empty($tarif['Goruntu'])) {
    $fotoYol = __DIR__ . '/../' . $tarif['Goruntu'];
    if (file_exists($fotoYol)) {
        unlink($fotoYol);
    }
}

// Tarif kaydını sil
$sil = $conn->prepare("DELETE FROM Tarifler WHERE TarifID = ?");
$sil->execute([$tarifID]);

flash('genel', 'Tarif başarıyla silindi ✔', 'ok');

if (current_user_role() === 'Admin') {
    redirect('/pages/admin_tariflerim.php');
} else {
    redirect('/pages/kullanici_paneli.php');
}
exit;
?>
