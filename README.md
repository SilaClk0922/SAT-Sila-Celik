🍽️ Yemek Tarifi Sitesi
Bu proje, kullanıcıların yemek tariflerini görüntüleyebildiği, tarif ekleyebildiği ve yönetebildiği web tabanlı bir Yemek Tarifi Platformudur.
Proje, PHP, MySQL ve Bootstrap kullanılarak geliştirilmiştir ve katmanlı dosya yapısı, güvenli oturum yönetimi ve rol bazlı erişim kontrolü mantığına sahiptir.

🚀 Proje Özellikleri
👤 Kullanıcı İşlemleri
Kullanıcı kayıt olma ve giriş yapma
Oturum (Session) tabanlı kullanıcı yönetimi
Kullanıcı profil bilgilerini görüntüleme
Yetkilendirme (Rol bazlı erişim)

📖 Tarif İşlemleri
Yemek tariflerini listeleme
Tarif detaylarını görüntüleme
Yeni tarif ekleme
Tarif onay / reddetme sistemi / kullanıcıya tarifi hakkında kısa not ekleme(Admin)
Tarif durumları:
Bekleyen
Onaylı
Reddedildi

🛡️ Güvenlik
CSRF Token kontrolü
Parametreli SQL sorguları (SQL Injection koruması)
Yetkisiz sayfa erişim engelleme
Oturum kontrolü (Session validation)

🧰 Kullanılan Teknolojiler
Teknoloji	Açıklama
PHP	Sunucu tarafı programlama
MySQL	Veritabanı yönetimi
HTML5	Sayfa yapısı
CSS3	Stil ve tasarım
Bootstrap 5	Responsive arayüz
JavaScript	Dinamik kullanıcı etkileşimi
Font Awesome	İkonlar

🗂️ Proje Klasör Yapısı
YemekTarifiSitesi/
│
├── config/
│   └── db.php
│
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── functions.php
│
├── pages/
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── profil.php
│   ├── tarif_detay.php
│
├── admin/
│   ├── tarif_onay.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
└── README.md

🗄️ Veritabanı Yapısı (Özet)
Temel Tablolar:
Kullanicilar
Tarifler
TarifOnay
GirisDenemeleri
Öne Çıkan Özellikler:
Primary Key & Foreign Key ilişkileri
Durum (status) alanları
Tarihsel kayıt (created_at)

🎯 Projenin Amacı
PHP tabanlı web uygulaması geliştirme pratiği kazanmak
Veritabanı ilişkilerini gerçek projede uygulamak
Güvenli oturum ve rol yönetimini öğrenmek
MVC benzeri dosya yapısı kullanmak
CV ve staj başvuruları için referans proje oluşturmak

📌 Geliştirilebilecek Özellikler
Tarif puanlama ve yorum sistemi
Kategori & etiket yapısı
Arama ve filtreleme
Görsel yükleme
API desteği (REST)

👨‍💻 Geliştirici Notu
Bu proje, eğitim ve kişisel gelişim amacıyla geliştirilmiştir.
Kod yapısı okunabilirlik, güvenlik ve sürdürülebilirlik esas alınarak hazırlanmıştır.
