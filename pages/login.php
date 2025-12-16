<?php
require __DIR__ . '/../includes/header.php';

if (is_logged_in()) {
    redirect('/pages/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $sifre = $_POST['sifre'] ?? '';
    $csrf  = $_POST['_csrf'] ?? '';

    if (!csrf_verify($csrf)) {
        flash('login', 'Geçersiz güvenlik anahtarı. Lütfen tekrar deneyin.', 'err');
        redirect('/pages/login.php');
    }

    if ($email === '' || $sifre === '') {
        flash('login', 'Lütfen tüm alanları doldurun.', 'err');
        redirect('/pages/login.php');
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM Kullanicilar WHERE Email = ? AND Aktif = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($sifre, $user['Sifre'])) {

            $_SESSION['user'] = [
                'KullaniciID' => $user['KullaniciID'],
                'AdSoyad'     => $user['AdSoyad'],
                'Email'       => $user['Email'],
                'Rol'         => $user['Rol'],
                'Avatar'      => $user['ProfilResmi'] ?? null
            ];

            flash('auth', 'Hoş geldin, ' . e($user['AdSoyad']) . '!', 'ok');
            redirect('/pages/index.php');
        //UYARI MESAJI
        } else {
            flash('login', 'E-posta veya şifre hatalı.', 'err');
            redirect('/pages/login.php');
        }

    } catch (PDOException $e) {
        flash('login', 'Veritabanı hatası: ' . e($e->getMessage()), 'err');
        redirect('/pages/login.php');
    }
}
?>

<style>
.auth-wrapper {
    display: flex;
    justify-content: center;
    padding: 50px 0;
}

.auth-card {
    width: 420px;
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    text-align: center;
}

.auth-title {
    font-size: 28px;
    margin-bottom: 15px;
    color: #7b4bbe;
    font-weight: 700;
}

.input-group {
    margin-bottom: 18px;
    text-align: left;
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

.auth-btn {
    width: 100%;
    padding: 12px;
    background: #b58bff;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: .25s;
    margin-top: 10px;
}

.auth-btn:hover {
    background: #9b60e7;
}

.auth-bottom {
    margin-top: 20px;
    font-size: 15px;
}

.auth-bottom a {
    color: #7b4bbe;
    font-weight: 600;
}
</style>

<!--GİRİŞ YAP-->
<div class="auth-wrapper">
    <div class="auth-card">

        <h2 class="auth-title">Giriş Yap</h2>

        <form method="post" action="">
            <?= csrf_input() ?>

            <div class="input-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="sifre">Şifre</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>

            <button class="auth-btn" type="submit">Giriş Yap</button>
        </form>

        <p class="auth-bottom">
            Hesabın yok mu?
            <a href="<?= SITE_URL ?>/pages/register.php">Kayıt Ol</a>
        </p>

    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
