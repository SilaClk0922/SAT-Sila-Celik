<?php
/*OTURUM & GENEL AYARLAR*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('SITE_URL')) {
    define('SITE_URL', '/YemekTarifiSitesi');
}


/* GÜVENLİ ÇIKTI (XSS KORUMA)*/
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}



/* YÖNLENDİRME*/
function redirect(string $path = '/'): void
{
    header('Location: ' . SITE_URL . $path);
    exit;
}



/*FLASH MESAJ SİSTEMİ*/

function flash(string $key, ?string $message = null, string $type = 'ok'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = [
            'msg' => $message,
            'type' => $type
        ];
        return null;
    }

    if (!empty($_SESSION['flash'][$key])) {
        $data = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $data;
    }

    return null;
}

function render_flash(): void
{
    if (empty($_SESSION['flash'])) return;

    foreach ($_SESSION['flash'] as $key => $data) {
        $cls = ($data['type'] === 'ok') ? 'alert-ok' : 'alert-err';
        echo '<div class="alert ' . $cls . '">' . e($data['msg']) . '</div>';
        unset($_SESSION['flash'][$key]);
    }
}

/* CSRF GÜVENLİĞİ*/
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token): bool
{
    return $token && isset($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}



/* KULLANICI BİLGİ FONKSİYONLARI
   - Oturumdaki kullanıcı bilgilerine erişim*/

function is_logged_in(): bool
{
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function current_user_id(): ?int
{
    return $_SESSION['user']['KullaniciID'] ?? null;
}

function current_user_name(): ?string
{
    return $_SESSION['user']['AdSoyad'] ?? null;  
}

function current_user_email(): ?string
{
    return $_SESSION['user']['Email'] ?? null;   
}

function current_user_role(): ?string
{
    return $_SESSION['user']['Rol'] ?? null;
}

function current_user_photo(): ?string
{
    return $_SESSION['user']['Avatar'] ?? null;
}

/* ERİŞİM KONTROLÜ*/

function require_login(): void
{
    if (!is_logged_in()) {
        flash('auth', 'Bu sayfaya erişmek için giriş yapmalısın.', 'err');
        redirect('/pages/login.php');
    }
}

function require_role(string $role): void
{
    if (!is_logged_in() || strcasecmp(current_user_role(), $role) !== 0) {
        flash('auth', 'Bu sayfaya erişim yetkin yok.', 'err');
        redirect('/pages/index.php');
    }
}

?>
