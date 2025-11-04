<?php
// includes/header.php
require_once __DIR__ . '/../config/db.php';     // Veritabanı bağlantısı
require_once __DIR__ . '/functions.php';        // Yardımcı fonksiyonlar (session dahil)
?>
<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Yemek Tarifi Sitesi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= SITE_URL ?>/style.css">
</head>
<body>

<header class="topbar">
  <div class="wrap">
    <a class="brand" href="<?= SITE_URL ?>/pages/index.php">🍲 YemekTarifi</a>

    <nav class="nav">
      <a class="nav-link" href="<?= SITE_URL ?>/pages/index.php">Anasayfa</a>

      <?php if (is_logged_in()): ?>
        <a class="nav-link" href="<?= SITE_URL ?>/pages/tarif_ekle.php">Tarif Ekle</a>

        <?php if (current_user_role() === 'Admin'): ?>
          <a class="nav-link" href="<?= SITE_URL ?>/pages/admin_panel.php">Admin Paneli</a>
          <a class="nav-link" href="<?= SITE_URL ?>/pages/kategoriler.php">Kategoriler</a>
          <a class="nav-link" href="<?= SITE_URL ?>/pages/tarif_onay.php">Tarif Onay</a>
        <?php endif; ?>

      <?php endif; ?>
    </nav>

    <div class="actions">
      <?php if (is_logged_in()): ?>
        <span class="user">👋 Merhaba, <b><?= e(current_user_name() ?? 'Kullanıcı') ?></b></span>
        <a class="btn btn-outline" href="<?= SITE_URL ?>/pages/logout.php">Çıkış</a>
      <?php else: ?>
        <a class="btn btn-light" href="<?= SITE_URL ?>/pages/login.php">Giriş</a>
        <a class="btn" href="<?= SITE_URL ?>/pages/register.php">Kayıt Ol</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container">
  <?php render_flash(); ?>
