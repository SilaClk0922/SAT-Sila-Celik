<?php

// Sunucu bilgileri
$serverName = "SILACAN\\SQLEXPRESS";  
$database   = "YemekTarifiSitesi";    
$username   = "sa";
$password   = "200922";

try {
    // PDO bağlantısı
    $conn = new PDO(
        "sqlsrv:Server={$serverName};Database={$database};TrustServerCertificate=true",
        $username,
        $password
    );

    // Hata modunu etkinleştir
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Türkçe karakter desteği
    $conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);

} catch (PDOException $e) {

    // Hata loglama (istersen bir log dosyasına yazabiliriz)
    error_log("DB Connection Error: " . $e->getMessage());

    // Son kullanıcıya ham hata göstermiyoruz
    die("❌ Veritabanı bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.");
}
