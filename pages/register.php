<?php
// pages/register.php
require __DIR__ . '/../includes/header.php';

// Eğer kullanıcı zaten giriş yaptıysa yönlendir
if (is_logged_in()) {
    redirect('/pages/index.php');
}

// Form gönderildiyse işle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adSoyad = trim($_POST['adsoyad'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $sifre   = $_POST['sifre'] ?? '';
    $sifre2  = $_POST['sifre2'] ?? '';
    $csrf    = $_POST['_csrf'] ?? '';

    // CSRF koruması
    if (!csrf_verify($csrf)) {
        flash('register', 'Geçersiz oturum güvenlik anahtarı. Lütfen formu yeniden deneyin.', 'err');
        redirect('/pages/register.php');
    }

    // Form doğrulama
    if ($adSoyad === '' || $email === '' || $sifre === '' || $sifre2 === '') {
        flash('register', 'Tüm alanlar doldurulmalıdır.', 'err');
        redirect('/pages/register.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('register', 'Geçersiz e-posta adresi.', 'err');
        redirect('/pages/register.php');
    }

    if ($sifre !== $sifre2) {
        flash('register', 'Şifreler eşleşmiyor.', 'err');
        redirect('/pages/register.php');
    }

    if (strlen($sifre) < 6) {
        flash('register', 'Şifre en az 6 karakter olmalıdır.', 'err');
        redirect('/pages/register.php');
    }

    // Şifreyi hash’le
    $sifreHash = password_hash($sifre, PASSWORD_BCRYPT);

    try {
        // Aynı e-posta var mı?
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Kullanicilar WHERE Email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            flash('register', 'Bu e-posta adresiyle zaten bir hesap mevcut.', 'err');
            redirect('/pages/register.php');
        }

        // Kullanıcıyı ekle
        $stmt = $conn->prepare("
            INSERT INTO Kullanicilar (AdSoyad, Email, Sifre, Rol)
            VALUES (?, ?, ?, 'Kullanici')
        ");
        $stmt->execute([$adSoyad, $email, $sifreHash]);

        // Otomatik giriş (oturum aç)
        $_SESSION['KullaniciID'] = $conn->lastInsertId();
        $_SESSION['AdSoyad'] = $adSoyad;
        $_SESSION['Rol'] = 'Kullanici';

        flash('auth', 'Kayıt başarılı, hoş geldin ' . $adSoyad . '!', 'ok');
        redirect('/pages/index.php');

    } catch (PDOException $e) {
        flash('register', 'Veritabanı hatası: ' . $e->getMessage(), 'err');
        redirect('/pages/register.php');
    }
}
?>

<h2>Kayıt Ol</h2>
<form method="post" action="">
    <?= csrf_input() ?>
    <label>Ad Soyad:</label><br>
    <input type="text" name="adsoyad" required><br><br>

    <label>E-posta:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Şifre:</label><br>
    <input type="password" name="sifre" required><br><br>

    <label>Şifre (Tekrar):</label><br>
    <input type="password" name="sifre2" required><br><br>

    <button type="submit">Kayıt Ol</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
