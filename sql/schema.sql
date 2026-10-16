-- Oto Yıkama Takip - XAMPP / MariaDB kurulum
-- Kullanım: http://localhost/oto-yikama/kurulum.php
CREATE DATABASE IF NOT EXISTS oto_yikama CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE oto_yikama;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ad VARCHAR(100) NOT NULL,
  kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
  sifre VARCHAR(255) NOT NULL,
  rol ENUM('admin','personel') NOT NULL DEFAULT 'personel',
  yetkiler TEXT NULL,
  aktif TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS musteriler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ad_soyad VARCHAR(120) NOT NULL,
  telefon VARCHAR(20) NOT NULL,
  plaka VARCHAR(20) NOT NULL,
  marka VARCHAR(50) DEFAULT '',
  model VARCHAR(50) DEFAULT '',
  renk VARCHAR(30) DEFAULT '',
  notlar TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_plaka (plaka),
  INDEX idx_tel (telefon)
);

CREATE TABLE IF NOT EXISTS hizmetler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  baslik VARCHAR(120) NOT NULL,
  fiyat DECIMAL(10,2) NOT NULL DEFAULT 0,
  aktif TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS urunler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  baslik VARCHAR(120) NOT NULL,
  fiyat DECIMAL(10,2) NOT NULL DEFAULT 0,
  stok INT NOT NULL DEFAULT 0,
  min_stok INT NOT NULL DEFAULT 5,
  aktif TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS arac_kartlari (
  id INT AUTO_INCREMENT PRIMARY KEY,
  plaka VARCHAR(20) NOT NULL,
  musteri_id INT NULL,
  musteri_ad VARCHAR(120) DEFAULT '',
  telefon VARCHAR(20) DEFAULT '',
  marka VARCHAR(50) DEFAULT '',
  model VARCHAR(50) DEFAULT '',
  renk VARCHAR(30) DEFAULT '',
  durum ENUM('kabul','islemde','bitti','cagrildi','teslim') NOT NULL DEFAULT 'kabul',
  personel VARCHAR(100) DEFAULT '',
  teslim_kodu VARCHAR(10) NOT NULL,
  toplam DECIMAL(10,2) NOT NULL DEFAULT 0,
  odenen DECIMAL(10,2) NOT NULL DEFAULT 0,
  odeme_yontemi VARCHAR(20) DEFAULT 'nakit',
  notlar TEXT NULL,
  foto VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_durum (durum),
  INDEX idx_plaka (plaka)
);

CREATE TABLE IF NOT EXISTS kart_kalemleri (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kart_id INT NOT NULL,
  tur ENUM('hizmet','urun') NOT NULL,
  ref_id INT NOT NULL DEFAULT 0,
  baslik VARCHAR(150) NOT NULL,
  adet INT NOT NULL DEFAULT 1,
  birim_fiyat DECIMAL(10,2) NOT NULL DEFAULT 0,
  indirim DECIMAL(10,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (kart_id) REFERENCES arac_kartlari(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS randevular (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ad_soyad VARCHAR(120) NOT NULL,
  telefon VARCHAR(20) NOT NULL,
  plaka VARCHAR(20) DEFAULT '',
  tarih DATE NOT NULL,
  saat TIME NOT NULL,
  durum ENUM('bekliyor','tamamlandi','iptal') DEFAULT 'bekliyor',
  notlar VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cariler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tip ENUM('musteri','tedarikci','personel') NOT NULL DEFAULT 'musteri',
  unvan VARCHAR(150) NOT NULL,
  telefon VARCHAR(20) DEFAULT '',
  bakiye DECIMAL(10,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cari_hareketler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cari_id INT NOT NULL,
  yon ENUM('borc','tahsilat') NOT NULL,
  tutar DECIMAL(10,2) NOT NULL,
  aciklama VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cari_id) REFERENCES cariler(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS kasa_hareketler (
  id INT AUTO_INCREMENT PRIMARY KEY,
  yon ENUM('gelir','gider') NOT NULL,
  baslik VARCHAR(150) NOT NULL,
  tutar DECIMAL(10,2) NOT NULL,
  yontem VARCHAR(20) DEFAULT 'nakit',
  kart_id INT NULL,
  personel VARCHAR(100) DEFAULT '',
  tarih DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notlar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  baslik VARCHAR(150) NOT NULL,
  icerik TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sms_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  telefon VARCHAR(20) NOT NULL,
  mesaj TEXT NOT NULL,
  tip VARCHAR(50) DEFAULT 'bilgi',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ayarlar (
  anahtar VARCHAR(50) PRIMARY KEY,
  deger TEXT
);

INSERT IGNORE INTO ayarlar (anahtar, deger) VALUES
('isletme_adi','Parla Oto Yıkama'),
('renk','#0ea5e9'),
('whatsapp','0551 094 75 79'),
('sms_sablonu_kabul','Sayin {ad}, {plaka} plakali araciniz teslim alindi. Teslim kodunuz: {kod}'),
('sms_sablonu_hazir','Sayin {ad}, {plaka} plakali araciniz hazir. Teslim kodu: {kod}');
