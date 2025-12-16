<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/YemekTarifiSitesi');
}

$isLogged = isset($_SESSION['user']);
$user     = $isLogged ? $_SESSION['user'] : null;
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yemek Tarifi Sitesi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="<?= SITE_URL ?>/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        function toggleMenu() {
            document.getElementById("hamburgerMenu").classList.toggle("open");
        }

        document.addEventListener("click", function (e) {
            let menu = document.getElementById("hamburgerMenu");
            let btn = document.getElementById("hamburgerBtn");

            if (menu && btn && !menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.remove("open");
            }
        });
    </script>
</head>

<body>

<header class="topbar">
    <div class="wrap">

        <!-- LOGO -->
        <a class="brand" href="<?= SITE_URL ?>/pages/index.php">
            <img class="site-logo" src="<?= SITE_URL ?>/assets/logo.png" alt="Cookoria Logo">
        </a>

        <!-- NAVBAR (2 satırlı yapı) -->
        <nav class="nav">

            <!-- 1. SATIR: Herkese açık kısım -->
            <div class="nav-row nav-row-main">
                <a class="nav-link" href="<?= SITE_URL ?>/pages/index.php">Anasayfa</a>
                <a class="nav-link" href="<?= SITE_URL ?>/pages/tarifler.php">Tarifler</a>
                <a class="nav-link" href="<?= SITE_URL ?>/pages/kategori_listesi.php">Kategoriler</a>

                <?php if (!$isLogged): ?>
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/hakkimda.php">Hakkımda</a>
                <?php endif; ?>

                <?php if ($isLogged): ?>
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/tarif_ekle.php">Tarif Ekle</a>
                <?php endif; ?>

                <?php if ($isLogged && $user['Rol'] !== 'Admin'): ?>
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/kullanici_paneli.php">Benim Tariflerim</a>
                <?php endif; ?>
            </div>

            <!-- 2. SATIR: Sadece ADMIN görünür -->
            <?php if ($isLogged && $user['Rol'] === 'Admin'): ?>
                <div class="nav-row nav-row-admin">
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/admin_panel.php">Admin Paneli</a>
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/kategoriler.php">Kategori Yönetimi</a>
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/tarif_onay.php">Tarif Onay</a>
                    <a class="nav-link" href="<?= SITE_URL ?>/pages/admin_tariflerim.php">Admin Tariflerim</a>
                </div>
            <?php endif; ?>

        </nav>

        <!-- ARAMA -->
        <form action="<?= SITE_URL ?>/pages/tarif_arama.php" method="get" class="mini-search">
            <input type="search" name="ara" placeholder="Tarif ara..." required>
            <button><i class="fa fa-search"></i></button>
        </form>

        <!-- SAĞ TARAF HAMBURGER -->
        <div class="actions">

            <?php if ($isLogged): ?>
                <span class="user">Merhaba, <b><?= e($user['AdSoyad']) ?></b></span>

                <button id="hamburgerBtn" class="hamburger" onclick="toggleMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div id="hamburgerMenu" class="hamburger-menu">
                    <a href="<?= SITE_URL ?>/pages/profil.php">
                        <i class="fa-regular fa-id-card"></i> Profilim
                    </a>
                    <a href="<?= SITE_URL ?>/pages/profil_duzenle.php">
                        <i class="fa-solid fa-user-pen"></i> Profili Düzenle
                    </a>
                    <a href="<?= SITE_URL ?>/pages/logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i> Çıkış Yap
                    </a>
                </div>

            <?php else: ?>
                <a class="nav-auth-btn" href="<?= SITE_URL ?>/pages/login.php">Giriş</a>
                <a class="nav-auth-btn" href="<?= SITE_URL ?>/pages/register.php">Kayıt Ol</a>
            <?php endif; ?>

        </div>

    </div>
</header>

<main class="main-container" style="padding-top:0;">
