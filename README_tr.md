<div align="center">

[English](README.md) | **Türkçe**

# Oto Yıkama Takip

Araç yıkama işletmeleri için yönetim sistemi — **iki ayrı implementasyon**:
tanıtım sitesi + operasyon paneli, araç kartları, randevu, stok,
müşteri, kasa ve raporlar.

</div>

## Versiyonlar

| | `v1` (kökteki PHP dosyaları) | `v2` (`backend/` + `frontend/`) |
|---|---|---|
| Teknoloji | PHP 8 + MySQL/MariaDB (XAMPP) | Spring Boot 3.2 + React 18 (Vite) |
| Giriş | Session login, roller (admin/personel) | JWT, roller |
| Çalıştırma | `htdocs` altına kopyala, `kurulum.php` aç | `mvn spring-boot:run` + `npm run dev` |
| Uygun olduğu yer | Tek bilgisayarlık kurulum | API + SPA mimarisi |

İkisi de aynı işi yapar: 5 adımlı araç kartı
(kabul → işlemde → bitti → çağrıldı → teslim, tek tuş + teslim kodu),
plaka arama, otomatik stok düşen hizmet/ürün, personel ve roller,
cari takip + ekstre, kasa/gider, rapor + CSV, notlar, SMS log,
halka açık online randevu sayfası.

## v1 — PHP (XAMPP)

```bash
# 1. Repoyu C:\xampp\htdocs\oto-yikama altına kopyala
# 2. Apache + MySQL başlat
# 3. http://localhost/oto-yikama/kurulum.php aç   (şema + demo kullanıcılar)
# 4. Giriş: http://localhost/oto-yikama/login.php  (admin / admin123)
```

- `index.php` tanıtım sitesi, `randevu-al.php` halka açık randevu
- Panel: `dashboard, araclar, arac-detay, musteriler, hizmetler, randevular, cari, kasa, raporlar, notlar, sms, kullanicilar, ayarlar`
- Yardımcılar: `inc/db.php`, plaka arama `api/plaka-sorgula.php`, şema `sql/schema.sql`

Gerçek entegrasyon noktaları (anahtarını ekle): `araclar.php → plakaOku()` içine
plaka OCR, `inc/db.php → sms_gonder()` içine SMS servisi, fiş yazdırma `window.print()` ile.

## v2 — Spring Boot + React

```bash
# backend (MySQL gerekir; JWT secret env'den, dev varsayılanı application.properties içinde)
cd backend
APP_JWT_SECRET=<uzun-rastgele-deger> mvn spring-boot:run   # http://localhost:8080

# frontend
cd frontend
npm install
npm run dev   # http://localhost:5173
```

- JWT giriş (`POST /api/auth/login`), Spring Security, validasyon, OpenAPI config
- REST kaynakları: `/api/arac-kartlari` (+`/{id}/ilerle`, +`/{id}/kalemler`),
  `/api/musteriler`, `/api/cariler` (+`/hareketler`), `/api/hizmetler`,
  `/api/urunler`, `/api/randevular`, `/api/kasa`, `/api/raporlar`,
  `/api/notlar`, `/api/sms-log`, `/api/ayarlar`, `/api/kullanicilar`,
  `/api/dashboard/ozet`
- React sayfaları paneli yansıtır: Dashboard, Araclar, AracDetay, Musteriler,
  Cari, Kasa, Randevular, Raporlar, Notlar, Tanimlar, Yonetim + Auth context

## Canlıya alma notları

- Geliştirme JWT varsayılanını kullanma: `APP_JWT_SECRET` ortam değişkenini
  ayarla (64+ karakter) — paketteki varsayılanla yayına çıkma.
- Demo bilgileri (`admin/admin123`) yalnızca yerel kurulum içindir.

## Yapı

```
v1 (kök):   index.php, login.php, dashboard.php, araclar.php, ... (panel)
            api/, inc/db.php, sql/schema.sql, assets/
backend/:   Spring Boot 3.2 (com.otoyikama.api): controller/service/entity/repository,
            security (JwtFilter), config, dto
frontend/:  React 18 + Vite: pages/, api.js, AuthContext, Layout
```
