<div align="center">

**English** | [Türkçe](README_tr.md)

# Car Wash Tracker

Car-wash business management system in **two implementations**:
marketing site + operations panel, vehicle cards, appointments, inventory,
customers, cash desk and reports.

</div>

## Versions

| | `v1` (root PHP files) | `v2` (`backend/` + `frontend/`) |
|---|---|---|
| Stack | PHP 8 + MySQL/MariaDB (XAMPP) | Spring Boot 3.2 + React 18 (Vite) |
| Auth | Session login, roles (admin/staff) | JWT, roles |
| Run | Copy to `htdocs`, open `kurulum.php` | `mvn spring-boot:run` + `npm run dev` |
| Best for | Single-PC deployment | API + SPA architecture |

Both cover the same domain: 5-step vehicle card
(received → in service → done → called → delivered, one click + pickup code),
plate search, services/products with auto stock deduction, staff and roles,
customer accounts + statements, cash/expense, reports + CSV, notes, SMS log,
public online appointment page.

## v1 — PHP (XAMPP)

```bash
# 1. Copy the repo to C:\xampp\htdocs\oto-yikama
# 2. Start Apache + MySQL
# 3. Open http://localhost/oto-yikama/kurulum.php   (creates schema + demo users)
# 4. Login: http://localhost/oto-yikama/login.php    (admin / admin123)
```

- `index.php` marketing site, `randevu-al.php` public appointments
- Panel: `dashboard, araclar, arac-detay, musteriler, hizmetler, randevular, cari, kasa, raporlar, notlar, sms, kullanicilar, ayarlar`
- Helpers: `inc/db.php`, plate lookup `api/plaka-sorgula.php`, schema `sql/schema.sql`

Real-integration stubs (add your keys): plate OCR in `araclar.php → plakaOku()`,
SMS in `inc/db.php → sms_gonder()`, receipt printing via `window.print()`.

## v2 — Spring Boot + React

```bash
# backend (needs MySQL; JWT secret via env, dev default in application.properties)
cd backend
APP_JWT_SECRET=<long-random-string> mvn spring-boot:run   # http://localhost:8080

# frontend
cd frontend
npm install
npm run dev   # http://localhost:5173
```

- JWT login (`POST /api/auth/login`), Spring Security, validation, OpenAPI config
- REST resources: `/api/arac-kartlari` (+`/{id}/ilerle`, +`/{id}/kalemler`),
  `/api/musteriler`, `/api/cariler` (+`/hareketler`), `/api/hizmetler`,
  `/api/urunler`, `/api/randevular`, `/api/kasa`, `/api/raporlar`,
  `/api/notlar`, `/api/sms-log`, `/api/ayarlar`, `/api/kullanicilar`,
  `/api/dashboard/ozet`
- React pages mirror the panel: Dashboard, Araclar, AracDetay, Musteriler,
  Cari, Kasa, Randevular, Raporlar, Notlar, Tanimlar, Yonetim + Auth context

## Production notes

- Replace the dev JWT default: set the `APP_JWT_SECRET` environment variable
  (64+ chars) — never ship the bundled default.
- Demo credentials (`admin/admin123`) are for local setup only.

## Structure

```
v1 (root):  index.php, login.php, dashboard.php, araclar.php, ... (panel)
            api/, inc/db.php, sql/schema.sql, assets/
backend/:   Spring Boot 3.2 (com.otoyikama.api): controller/service/entity/repository,
            security (JwtFilter), config, dto
frontend/:  React 18 + Vite: pages/, api.js, AuthContext, Layout
```
