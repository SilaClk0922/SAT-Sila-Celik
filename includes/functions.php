<?php
//  OTURUM VE SİSTEM TANIMLARI

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proje kök URL'si — klasör adın değişirse burayı güncelle
if (!defined('SITE_URL')) {
    define('SITE_URL', '/YemekTarifiSitesi');
}

// GENEL YARDIMCI FONKSİYONLAR

//Güvenli çıktı (XSS koruması) 
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Sayfa yönlendirme 
function redirect(string $path = '/'): void {
    header('Location: ' . SITE_URL . $path);
    exit;
}


// FLASH MESAJ SİSTEMİ
 
/*
   Flash mesaj oluştur veya al.
   flash('key', 'Mesaj', 'ok'|'err') → mesajı kaydeder
   flash('key') → mesajı döndürür & siler
 */
function flash(string $key, ?string $message = null, string $type = 'ok'): ?array {
    if ($message !== null) {
        $_SESSION['flash'][$key] = ['msg' => $message, 'type' => $type];
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $data = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $data;
    }
    return null;
}


 //Tüm flash mesajlarını ekrana bas renkli uyarı kutuları.
 
function render_flash(): void {
    if (empty($_SESSION['flash'])) return;

    foreach ($_SESSION['flash'] as $k => $f) {
        $cls = ($f['type'] ?? 'ok') === 'ok' ? 'alert-ok' : 'alert-err';
        echo '<div class="alert ' . $cls . '">' . e($f['msg'] ?? '') . '</div>';
        unset($_SESSION['flash'][$k]);
    }
}


// CSRF GÜVENLİK FONKSİYONLARI
  
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_input(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(?string $token): bool {
    return $token && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}


  //  KULLANICI / AUTH YARDIMCILARI
  

function is_logged_in(): bool {
    return !empty($_SESSION['KullaniciID']);
}

function current_user_id(): ?int {
    return is_logged_in() ? (int)$_SESSION['KullaniciID'] : null;
}

function current_user_name(): ?string {
    return is_logged_in() ? ($_SESSION['AdSoyad'] ?? null) : null;
}

function current_user_role(): ?string {
    return is_logged_in() ? ($_SESSION['Rol'] ?? null) : null;
}


 // Giriş zorunlu sayfalar için kontrol
 
function require_login(): void {
    if (!is_logged_in()) {
        flash('auth', 'Bu sayfaya erişmek için giriş yapmalısın.', 'err');
        redirect('/pages/login.php');
    }
}


 //Belirli rol zorunlu 
 
function require_role(string $role): void {
    if (!is_logged_in() || strcasecmp(current_user_role() ?? '', $role) !== 0) {
        flash('auth', 'Bu sayfaya erişim yetkin yok.', 'err');
        redirect('/pages/index.php');
    }
}
