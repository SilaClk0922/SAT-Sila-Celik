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
            $_SESSION['KullaniciID'] = $user['KullaniciID'];
            $_SESSION['AdSoyad'] = $user['AdSoyad'];
            $_SESSION['Rol'] = $user['Rol'];

            flash('auth', 'Hoş geldin, ' . e($user['AdSoyad']) . '!', 'ok');
            redirect('/pages/index.php');
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

<div class="container">
  <div class="card">
    <h2>Giriş Yap</h2>

    <!-- header.php zaten render_flash() çağırdığı için burada tekrar etmiyoruz -->

    <form method="post" action="">
      <?= csrf_input() ?>

      <label for="email">E-posta:</label>
      <input type="email" name="email" id="email" required>

      <label for="sifre">Şifre:</label>
      <input type="password" name="sifre" id="sifre" required>

      <button type="submit">Giriş Yap</button>
    </form>

    <p style="margin-top: 15px;">
      Hesabın yok mu? <a href="<?= SITE_URL ?>/pages/register.php">Kayıt Ol</a>
    </p>
  </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
