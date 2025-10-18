
CREATE DATABASE YemekTarifiSitesi;
GO
USE YemekTarifiSitesi;
GO


-- KULLANICILAR
CREATE TABLE dbo.Kullanicilar (
    KullaniciID INT IDENTITY(1,1) PRIMARY KEY,
    AdSoyad NVARCHAR(100) NOT NULL,
    Email NVARCHAR(100) UNIQUE NOT NULL,
    Sifre NVARCHAR(255) NOT NULL,
    Rol NVARCHAR(20) CHECK (Rol IN ('Kullanici', 'Admin')) DEFAULT 'Kullanici',
    KayitTarihi DATETIME DEFAULT GETDATE(),
    Aktif BIT DEFAULT 1 CHECK (Aktif IN (0,1))
);
GO

-- KATEGORÝLER
CREATE TABLE dbo.Kategoriler (
    KategoriID INT IDENTITY(1,1) PRIMARY KEY,
    KategoriAdi NVARCHAR(100) NOT NULL
);
GO

-- TARÝFLER
CREATE TABLE dbo.Tarifler (
    TarifID INT IDENTITY(1,1) PRIMARY KEY,
    KullaniciID INT NOT NULL,
    KategoriID INT NOT NULL,
    TarifAdi NVARCHAR(150) NOT NULL,
    Malzemeler NVARCHAR(MAX) NOT NULL,
    Hazirlanis NVARCHAR(MAX) NOT NULL,
    PismeSuresi NVARCHAR(50),
    Zorluk NVARCHAR(50),
    Goruntu NVARCHAR(255), -- resim yolu
    EklemeTarihi DATETIME DEFAULT GETDATE(),
    Onay BIT DEFAULT 1 CHECK (Onay IN (0,1))
);
GO

-- YORUMLAR
CREATE TABLE dbo.Yorumlar (
    YorumID INT IDENTITY(1,1) PRIMARY KEY,
    TarifID INT NOT NULL,
    KullaniciID INT NOT NULL,
    YorumMetni NVARCHAR(500) NOT NULL,
    YorumTarihi DATETIME DEFAULT GETDATE()
);
GO

-- PUANLAR
CREATE TABLE dbo.Puanlar (
    PuanID INT IDENTITY(1,1) PRIMARY KEY,
    TarifID INT NOT NULL,
    KullaniciID INT NOT NULL,
    Puan TINYINT CHECK (Puan BETWEEN 1 AND 5),
    PuanTarihi DATETIME DEFAULT GETDATE()
);
GO

-- FAVORÝLER
CREATE TABLE dbo.Favoriler (
    FavoriID INT IDENTITY(1,1) PRIMARY KEY,
    KullaniciID INT NOT NULL,
    TarifID INT NOT NULL,
    EklenmeTarihi DATETIME DEFAULT GETDATE()
);
GO

 
  -- FOREIGN KEY ÝLÝÞKÝLERÝ
                                                

ALTER TABLE dbo.Tarifler
ADD CONSTRAINT FK_Tarifler_Kullanicilar
    FOREIGN KEY (KullaniciID) REFERENCES dbo.Kullanicilar(KullaniciID)
    ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE dbo.Yorumlar
ADD CONSTRAINT FK_Yorumlar_Tarifler
    FOREIGN KEY (TarifID) REFERENCES dbo.Tarifler(TarifID)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE dbo.Puanlar
ADD CONSTRAINT FK_Puanlar_Tarifler
    FOREIGN KEY (TarifID) REFERENCES dbo.Tarifler(TarifID)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE dbo.Favoriler
ADD CONSTRAINT FK_Favoriler_Tarifler
    FOREIGN KEY (TarifID) REFERENCES dbo.Tarifler(TarifID)
    ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE dbo.Yorumlar
ADD CONSTRAINT FK_Yorumlar_Kullanicilar
    FOREIGN KEY (KullaniciID) REFERENCES dbo.Kullanicilar(KullaniciID)
    ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE dbo.Puanlar
ADD CONSTRAINT FK_Puanlar_Kullanicilar
    FOREIGN KEY (KullaniciID) REFERENCES dbo.Kullanicilar(KullaniciID)
    ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE dbo.Favoriler
ADD CONSTRAINT FK_Favoriler_Kullanicilar
    FOREIGN KEY (KullaniciID) REFERENCES dbo.Kullanicilar(KullaniciID)
    ON DELETE NO ACTION ON UPDATE NO ACTION;
GO


  -- Index'ler
  

CREATE INDEX IX_Tarifler_KullaniciID   ON dbo.Tarifler(KullaniciID);
CREATE INDEX IX_Tarifler_KategoriID    ON dbo.Tarifler(KategoriID);
CREATE INDEX IX_Yorumlar_TarifID       ON dbo.Yorumlar(TarifID);
CREATE INDEX IX_Yorumlar_KullaniciID   ON dbo.Yorumlar(KullaniciID);
CREATE INDEX IX_Puanlar_TarifID        ON dbo.Puanlar(TarifID);
CREATE INDEX IX_Puanlar_KullaniciID    ON dbo.Puanlar(KullaniciID);
CREATE INDEX IX_Favoriler_TarifID      ON dbo.Favoriler(TarifID);
CREATE INDEX IX_Favoriler_KullaniciID  ON dbo.Favoriler(KullaniciID);
GO

