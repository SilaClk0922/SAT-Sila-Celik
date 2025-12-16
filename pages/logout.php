<?php
session_start();
require __DIR__ . '/../includes/functions.php';

// Tüm oturum verilerini temizle
session_unset();
session_destroy();

// Logout sonrası yeni session başlat (sadece flash mesaj için)
session_start();
flash('auth', 'Oturum başarıyla kapatıldı.', 'ok');

// Login sayfasına yönlendir
redirect('/pages/login.php');
exit;
