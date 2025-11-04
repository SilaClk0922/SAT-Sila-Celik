<?php
require __DIR__ . '/../includes/functions.php';

// Oturumu kapat
session_unset();
session_destroy();

// Yeni oturum başlat (flash mesaj için)
session_start();
flash('auth', 'Oturum başarıyla kapatıldı.', 'ok');

// Ana sayfaya yönlendir
redirect('/pages/index.php');
