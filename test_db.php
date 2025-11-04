<?php
require __DIR__ . "/config/db.php";

try {
    $stmt = $conn->query("SELECT DB_NAME() AS DbAdi, CONVERT(varchar(19), GETDATE(), 120) AS SunucuZamani");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "✅ MSSQL bağlantısı başarılı!<br>";
    echo "Veritabanı: " . htmlspecialchars($row['DbAdi']) . "<br>";
    echo "Sunucu zamanı: " . htmlspecialchars($row['SunucuZamani']);
} catch (PDOException $e) {
    echo "❌ Sorgu hatası: " . htmlspecialchars($e->getMessage());
}
?>
