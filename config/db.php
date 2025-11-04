<?php
$serverName = "SILACAN\\SQLEXPRESS";  
$database   = "YemekTarifiSitesi";    
$username   = "sa";                   
$password   = "200922";              

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Bağlantı başarılı!";
} catch (PDOException $e) {
    die("❌ Veritabanı bağlantı hatası: " . $e->getMessage());
}
