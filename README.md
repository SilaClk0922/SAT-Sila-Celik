🍽️ YEMEK TARİF SİTESİ
<img width="1896" height="886" alt="Ekran görüntüsü 2026-01-02 212745" src="https://github.com/user-attachments/assets/a5864f66-32fa-4a75-9450-6307022387f3" />
Bu proje, kullanıcıların yemek tariflerini görüntüleyebildiği, tarif ekleyebildiği ve yönetebildiği web tabanlı bir Yemek Tarifi Platformudur.

Proje, PHP, MySQL ve Bootstrap kullanılarak geliştirilmiştir ve katmanlı dosya yapısı, güvenli oturum yönetimi ve rol bazlı erişim kontrolü mantığına sahiptir.


🚀 PROJE ÖZELLİKLERİ

👤 KULLANICI HİZMETLERİ


Kullanıcı kayıt olma ve giriş yapma

Oturum (Session) tabanlı kullanıcı yönetimi

Kullanıcı profil bilgilerini görüntüleme

Yetkilendirme (Rol bazlı erişim)


📖 TARİF İŞLEMLERİ


Yemek tariflerini listeleme

Tarif detaylarını görüntüleme

Yeni tarif ekleme

Tarif onay / reddetme sistemi / kullanıcıya tarifi hakkında kısa not ekleme(Admin)

Tarif durumları:

Bekleyen

Onaylı

Reddedildi


🛡️ GÜVENLİK


CSRF Token kontrolü

Parametreli SQL sorguları (SQL Injection koruması)

Yetkisiz sayfa erişim engelleme

Oturum kontrolü (Session validation)


🧰 KULLANILAN TEKNOLOJİLER


Teknoloji	Açıklama

PHP	Sunucu tarafı programlama

MySQL	Veritabanı yönetimi

HTML5	Sayfa yapısı

CSS3	Stil ve tasarım

Bootstrap 5	Responsive arayüz

JavaScript	Dinamik kullanıcı etkileşimi

Font Awesome	İkonlar


🗂️ PROJE KLASÖR YAPISI


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


🗄️ VERİTABANI YAPISI (Özet)


Temel Tablolar:

Kullanicilar

Tarifler

TarifOnay

GirisDenemeleri

Öne Çıkan Özellikler:

Primary Key & Foreign Key ilişkileri

Durum (status) alanları

Tarihsel kayıt (created_at)


🎯 PROJENİN AMACI


PHP tabanlı web uygulaması geliştirme pratiği kazanmak

Veritabanı ilişkilerini gerçek projede uygulamak

Güvenli oturum ve rol yönetimini öğrenmek

MVC benzeri dosya yapısı kullanmak

CV ve staj başvuruları için referans proje oluşturmak


📌 GELİŞTİRİLEBİLECEK ÖZELLİKLER


Tarif puanlama ve yorum sistemi

Kategori & etiket yapısı

Arama ve filtreleme

Görsel yükleme

API desteği (REST)


👨‍💻 GELİŞTİRİCİ NOTU


Bu proje, eğitim ve kişisel gelişim amacıyla geliştirilmiştir.

Kod yapısı okunabilirlik, güvenlik ve sürdürülebilirlik esas alınarak hazırlanmıştır.

