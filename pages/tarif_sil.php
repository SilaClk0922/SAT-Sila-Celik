<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

// ID kontrolü
$tarifID = $_GET['id'] ?? null;
$csrf    = $_GET['_csrf'] ?? '';

if (!$tarifID || !is_numeric($tarifID)) {
    flash('tarif_onay', 'Geçersiz tarif ID.', 'err');
    redirect('/pages/tarif_onay.php');
}

if (!csrf_verify($csrf)) {
    flash('tarif_onay', 'Güvenlik doğrulaması başarısız.', 'err');
    redirect('/pages/tarif_onay.php');
}

try {
    // Görseli çek
    $stmt = $conn->prepare("SELECT Goruntu FROM Tarifler WHERE TarifID = ?");
    $stmt->execute([$tarifID]);
    $tarif = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tarif) {
        flash('tarif_onay', 'Tarif bulunamadı.', 'err');
        redirect('/pages/tarif_onay.php');
    }

    // Veritabanından sil
    $delete = $conn->prepare("DELETE FROM Tarifler WHERE TarifID = ?");
    $delete->execute([$tarifID]);

    // Görseli sil
    if (!empty($tarif['Goruntu'])) {
        $imagePath = __DIR__ . '/../' . $tarif['Goruntu'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    flash('tarif_onay', 'Tarif başarıyla silindi 🗑️', 'ok');

} catch (PDOException $e) {
    flash('tarif_onay', 'Silme hatası: ' . $e->getMessage(), 'err');
}

redirect('/pages/tarif_onay.php');
