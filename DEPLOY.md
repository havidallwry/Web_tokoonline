# Deploy ke Dokploy

Panduan deploy **Toko Online Tri Bhakti At Taqwa** (Laravel 12) ke Dokploy
menggunakan **Dockerfile** + **database MySQL terkelola Dokploy**.

Arsitektur: 1 container aplikasi (`serversideup/php:8.2-fpm-nginx`, listen port **8080**)
+ 1 service MySQL dari Dokploy.

---

## 1. Prasyarat

- Repo sudah di GitHub/GitLab: `https://github.com/Ifanagungs/web_tokoonline`
- `.env`, `vendor/`, `node_modules/` **tidak** ikut ke-commit (sudah ada di `.gitignore`)
- Server dengan Dokploy terpasang

---

## 2. Push perubahan ke repo

File `Dockerfile`, `.dockerignore`, `.env.production.example`, dan `DEPLOY.md` ini
harus ada di root repo. Commit lalu push:

```bash
git add Dockerfile .dockerignore .env.production.example DEPLOY.md
git commit -m "Add Dokploy deployment setup"
git push origin main
```

---

## 3. Buat database di Dokploy

1. Buka Dokploy → **Create Database** → pilih **MySQL**.
2. Isi nama database, mis. `db_tokoonline`, dan simpan.
3. Catat kredensial dari tab database:
   - **Host internal**: biasanya nama service (mis. `<project>-db` / `<app>_<db>`)
   - Port: `3306`
   - Database, user, password

> Gunakan **host internal** (bukan `127.0.0.1`) agar container app bisa terhubung.

---

## 4. Buat Application

1. Dokploy → **Create Project** → **Create Application**.
2. Source: **Git** → pilih repo `Ifanagungs/web_tokoonline`, branch `main`.
3. Tab **Build**:
   - Build Type: **Dockerfile**
   - Docker File: `./Dockerfile`
4. Tab **General / Networking**:
   - **Port**: `8080`
5. Tab **Environment**: isi variabel di bawah.

### Environment Variables

Wajib diisi:

| Variabel | Nilai |
|---|---|
| `APP_NAME` | `Tri Bhakti At Taqwa` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | hasil `php artisan key:generate --show` |
| `APP_URL` | `https://domain-kamu.com` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | host internal DB dari langkah 3 |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `db_tokoonline` |
| `DB_USERNAME` | user DB dari langkah 3 |
| `DB_PASSWORD` | password DB dari langkah 3 |

Disarankan juga:

| Variabel | Nilai |
|---|---|
| `LOG_LEVEL` | `error` |
| `SESSION_SECURE_COOKIE` | `true` (hanya jika sudah HTTPS) |
| `CACHE_STORE` | `database` |
| `SESSION_DRIVER` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `MAIL_MAILER` | `log` (atau SMTP saat email sudah siap) |

Generate `APP_KEY` (lokal, butuh PHP):

```bash
php artisan key:generate --show
```

Atau pakai key yang sudah ada di `.env` lama bila ingin mempertahankannya.

---

## 5. Domain & SSL

1. Tab **Domains** → tambahkan domain, mis. `tokoonline.domain.com`.
2. Set path ke `/`, container port `8080`.
3. Aktifkan **HTTPS / Let's Encrypt**.
4. Setelah HTTPS aktif, pastikan `APP_URL` = `https://...` dan
   `SESSION_SECURE_COOKIE=true`.

---

## 6. Deploy

Klik **Deploy**. Saat container start, image `serversideup/php` otomatis menjalankan:

- `php artisan migrate --force` (AUTORUN_LARAVEL_MIGRATION)
- `php artisan storage:link` (AUTORUN_LARAVEL_STORAGE_LINK)

Pantau log build & runtime di tab **Logs**.

---

## 7. Buat akun admin (sekali saja)

Migrasi membuat tabel, tetapi seeder tidak jalan otomatis. Buka
**Terminal** / **Commands** pada container app, lalu:

```bash
php artisan db:seed --force
```

Ini menjalankan `AdminSeeder` + `CategorySeeder`:

- **Email**: `admin@tribhakti.com`
- **Password**: `password`

> Segera ganti password admin setelah login pertama.

---

## 8. (Opsional) Import data lama

Jika ingin memakai data dari dump `db_tokoonline.sql` (produk, order, dll),
jalankan dari sisi server/DB (bukan di container app):

```bash
mysql -h <host-db> -P 3306 -u <user> -p <nama-db> < db_tokoonline.sql
```

---

## 9. (Opsional, disarankan) Volume untuk upload

Agar gambar produk & bukti transfer tidak hilang saat redeploy, tambahkan
volume di Dokploy yang memetakan ke:

```
/var/www/html/storage/app/public
```

Lalu tetap jalankan `php artisan storage:link` (sudah otomatis via autorun).

---

## 10. Optimasi produksi

Image `serversideup/php` **sudah** menjalankan `php artisan optimize`
(config/route/view/event cache) otomatis saat container start, karena
`AUTORUN_LARAVEL_OPTIMIZE` default `true`. OPcache juga sudah diaktifkan
lewat `PHP_OPCACHE_ENABLE=1` di Dockerfile. Tidak ada yang perlu diubah.

Untuk mematikan salah satu cache, set variabelnya `false`, mis.
`AUTORUN_LARAVEL_ROUTE_CACHE=false`.

> Karena `config` di-cache saat start, perubahan Environment Variables
> di dashboard baru berlaku setelah container **redeploy/restart**.

---

## Catatan

- Port aplikasi = **8080** (default image `serversideup/php:8.2-fpm-nginx`).
- Jangan commit `.env`; semua rahasia lewat Environment Variables Dokploy.
- `vendor/` dan `node_modules/` dibangun ulang saat build image.
