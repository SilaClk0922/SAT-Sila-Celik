CREATE DATABASE YemekTarifiSitesi;
GO
USE YemekTarifiSitesi;
GO
CREATE TABLE Kullanicilar (
    KullaniciID int identity(1,1) primary key,
    AdSoyad nvarchar (100) not null,
    Email nvarchar(100) UNIQUE not null,
    Sifre nvarchar(255) not null,
    Rol nvarchar(20) CHECK (Rol IN ('Kullanici', 'Admin')) DEFAULT 'Kullanici',
    KayitTarihi datetime default getdate(),
    Aktif bit default 1 check (Aktif in (0,1))
);
GO
CREATE TABLE Kategoriler (
    KategoriID int identity(1,1) primary key,
    KategoriAdi nvarchar(100) not null
);
GO
CREATE TABLE Tarifler (
    TarifID int identity(1,1) primary key,
    KullaniciID int not null,
    KategoriID int not null,
    TarifAdi nvarchar(150) not null,
    Malzemeler nvarchar(max) not null,
    Hazirlanis nvarchar(max) not null,
    PismeSuresi nvarchar(50),
    Zorluk nvarchar(50),
    Goruntu nvarchar(255), -- resim yolu
    EklemeTarihi datetime default getdate(),
    Onay bit default 1 check (Onay in (0,1))
);
GO
CREATE TABLE Yorumlar (
    YorumID int identity(1,1) primary key,
    TarifID int ,
    KullaniciID int ,
    YorumMetni nvarchar(500) not null,
    YorumTarihi datetime default getdate()
);
GO
CREATE TABLE Puanlar (
    PuanID int identity(1,1) primary key,
    TarifID int ,
    KullaniciID int ,
    Puan tinyint check (Puan between 1 and 5),
    PuanTarihi datetime default getdate()
);
GO
CREATE TABLE Favoriler (
    FavoriID int identity(1,1) primary key,
    KullaniciID int ,
    TarifID int ,
    EklenmeTarihi datetime default getdate()
);