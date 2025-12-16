<?php
require __DIR__ . '/../includes/header.php';

/* Zaten giriş yapmış kullanıcıyı anasayfaya yönlendirme */
if (is_logged_in()) {
    redirect('/pages/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $adsoyad = trim($_POST['adsoyad'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $sifre   = $_POST['sifre'] ?? '';
    $sifre2  = $_POST['sifre2'] ?? '';
    $csrf    = $_POST['_csrf'] ?? '';

    /*  CSRF doğrulaması
       - Token geçersizse kayıt işlemi iptal edilir */

    if (!csrf_verify($csrf)) {
        flash('register', 'Geçersiz güvenlik anahtarı.', 'err');
        redirect('/pages/register.php');
    }

    /* Zorunlu alan kontrolü
       - Boş alan varsa kullanıcı bilgilendirilir */

    if ($adsoyad === '' || $email === '' || $sifre === '') {
        flash('register', 'Lütfen tüm alanları doldurun.', 'err');
        redirect('/pages/register.php');
    }

    /* Şifre doğrulaması
       - Şifreler eşleşmiyorsa işlem iptal edilir*/
    if ($sifre !== $sifre2) {
        flash('register', 'Şifreler uyuşmuyor!', 'err');
        redirect('/pages/register.php');
    }

    try {
        // E-posta kontrolü
        $kontrol = $conn->prepare("SELECT 1 FROM Kullanicilar WHERE Email = ?");
        $kontrol->execute([$email]);

        if ($kontrol->fetch()) {
            flash('register', 'Bu e-posta zaten kayıtlı.', 'err');
            redirect('/pages/register.php');
        }

        // Kayıt işlemi
        $stmt = $conn->prepare("
            INSERT INTO Kullanicilar (AdSoyad, Email, Sifre, Rol, Aktif)
            VALUES (?, ?, ?, 'Kullanici', 1)
        ");

        $stmt->execute([
            $adsoyad,
            $email,
            password_hash($sifre, PASSWORD_BCRYPT)
        ]);

        flash('auth', 'Kayıt başarılı! Giriş yapabilirsiniz.', 'ok');
        redirect('/pages/login.php');

    } catch (PDOException $e) {
        flash('register', 'Veritabanı hatası: ' . e($e->getMessage()), 'err');
        redirect('/pages/register.php');
    }
}
?>

<style>
/* --- FORM GENEL TASARIM --- */
.auth-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 0;
}

.auth-card {
    width: 420px;
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    text-align: center;
}

.auth-title {
    font-size: 28px;
    margin-bottom: 20px;
    color: var(--brand-dark);
    font-weight: 700;
}

.input-group {
    text-align: left;
    margin-bottom: 18px;
}

.input-group label {
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
}

.input-group input {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 15px;
}

/* --- BUTON TASARIMI --- */
.auth-btn {
    background: var(--brand-color);
    color: #fff;
    padding: 12px 32px;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: 0.25s ease;
    margin-top: 10px;
    width: 100%;
}

.auth-btn:hover {
    background: var(--brand-dark);
    transform: translateY(-2px);
}

/* --- ALT METİN --- */
.auth-bottom {
    margin-top: 20px;
    font-size: 15px;
}

.auth-bottom a {
    color: var(--brand-dark);
    font-weight: 600;
}
</style>

<!-- Kayıt formu arayüzü -->
<div class="auth-wrapper">
    <div class="auth-card">

        <h2 class="auth-title">Kayıt Ol</h2>

        <?php render_flash('register'); ?>

        <!--  Kayıt formu
             - CSRF hidden input eklenir
             - Ad Soyad, E-posta, Şifre, Şifre tekrar alanlar -->

        <form method="post" action="">
            <?= csrf_input() ?>

            <div class="input-group">
                <label for="adsoyad">Ad Soyad</label>
                <input type="text" id="adsoyad" name="adsoyad" required>
            </div>

            <div class="input-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="sifre">Şifre</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>

            <div class="input-group">
                <label for="sifre2">Şifre (Tekrar)</label>
                <input type="password" id="sifre2" name="sifre2" required>
            </div>

            <button class="auth-btn" type="submit">Kayıt Ol</button>
        </form>
        
        <!--Giriş sayfasına yönlendiren alt bağlantı-->
        <p class="auth-bottom">
            Zaten hesabın var mı?
            <a href="<?= SITE_URL ?>/pages/login.php">Giriş Yap</a>
        </p>

    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
