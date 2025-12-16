<?php
require __DIR__ . '/../includes/header.php';
require_role('Admin');

$yap = $_GET['yap'] ?? '';
$id  = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    flash('auth', 'Geçersiz istek.', 'err');
    redirect('/pages/admin_panel.php');
}

try {
    switch ($yap) {
        case 'adminYap':
            $stmt = $conn->prepare("UPDATE Kullanicilar SET Rol='Admin' WHERE KullaniciID=?");
            $stmt->execute([$id]);
            flash('auth', 'Kullanıcı admin yapıldı.', 'ok');
            break;

        case 'silKullanici':
            // Kullanıcının tariflerini silelim 
            $conn->prepare("DELETE FROM Tarifler WHERE KullaniciID=?")->execute([$id]);
            $conn->prepare("DELETE FROM Kullanicilar WHERE KullaniciID=?")->execute([$id]);
            flash('auth', 'Kullanıcı silindi.', 'ok');
            break;

/*  Tarif onaylama işlemi */
        case 'onayla':
            $stmt = $conn->prepare("UPDATE Tarifler SET Onay=1 WHERE TarifID=?");
            $stmt->execute([$id]);
            flash('auth', 'Tarif onaylandı 🎉', 'ok');
            break;

 /* Tarif silme işlemi */
        case 'silTarif':
            $stmt = $conn->prepare("DELETE FROM Tarifler WHERE TarifID=?");
            $stmt->execute([$id]);
            flash('auth', 'Tarif silindi.', 'ok');
            break;

        default:
            flash('auth', 'Geçersiz işlem.', 'err');
    }

} catch (PDOException $e) {
    flash('auth', 'Hata: ' . e($e->getMessage()), 'err');
}

redirect('/pages/admin_panel.php');
