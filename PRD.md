# PRD — GrowStick

Product Requirements Document untuk GrowStick — aplikasi catatan tempel digital.

## 1. Konsep

- **Catatan bebas** — judul opsional + isi teks (line-break dipertahankan, tanpa Markdown/HTML).
- **Tiga tingkat visibilitas** per catatan:

  | Visibilitas | Siapa yang bisa membaca | Tampil di papan |
  |---|---|---|
  | `private` | Hanya pemilik | Tidak |
  | `unlisted` | Siapa pun yang punya tautan `/notes/{slug}` | Tidak |
  | `public` | Semua orang | Ya |

- **Papan publik** di beranda menampilkan daftar catatan `public` dari file manifest (tanpa DB).
- **Ketahanan baca**: catatan non-private ditulis juga sebagai file JSON immutable di `public/snapshots/` — papan dan halaman detail tetap terbaca walau database down. Service worker meng-cache halaman untuk mode offline.

## 2. Tech Stack

| Aspek | Teknologi |
|---|---|
| Framework | Laravel 13 (PHP ≥ 8.3) |
| UI | Livewire 4 (Single File Components), Alpine.js, Tailwind CSS 4 |
| Build | Vite 8 + `laravel-vite-plugin` (font self-host via Bunny) |
| Bahasa | Bilingual ID/EN (`SetLocale`: session → user → config `app.locale`) |
| Database | SQLite (default), bisa diganti lewat `DB_*` |
| Testing | PHPUnit, Laravel Pint |

## 3. Setup

```bash
composer setup     # install deps, .env, key, migrate, npm install, build
composer dev       # server + queue + logs (php artisan dev)
```

Atau manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install && npm run build
php artisan serve
```

## 4. Route

| Route | Halaman | Akses |
|---|---|---|
| `/` | Beranda + papan publik | Publik |
| `/notes` | Dinding sticky (buat/hapus) | Login |
| `/notes/{slug}` | Detail catatan + edit (pemilik) | Sesuai visibilitas |
| `/offline` | Fallback offline | Publik |
| `/login`, `/register`, `/forgot-password`, `/reset-password/{token}` | Auth | Tamu |
| `/locale/{locale}` | Ganti bahasa (ID/EN) | Publik |

## 5. Skema Database

```
users    — id, name, email, password, locale, timestamps
notes    — id, user_id FK (cascade delete), title (nullable), body (text),
           visibility (private|unlisted|public, default private),
           slug (unique, 20), color (20, default yellow), timestamps
```

Warna sticky: `yellow`, `green`, `blue`, `pink`, `purple`. Slug: token acak 12 karakter (huruf kecil + angka), unik.

## 6. Struktur Penting

```
app/Models/Note.php                       — model + enum visibilitas/warna + colorClass()
app/Services/Notes/NoteSnapshotWriter.php — tulis/baca snapshot & manifest papan
app/Http/Middleware/SetLocale.php         — resolusi locale per request
resources/views/pages/                    — halaman Livewire SFC (⚡*.blade.php)
resources/views/layouts/app.blade.php     — layout utama
public/snapshots/                         — snapshot JSON + manifest-index.json (gitignored)
public/sw.js                              — service worker (offline)
lang/{en,id}/                             — terjemahan UI
database/migrations/                      — users, cache, jobs, locale, notes
```

## 7. Snapshot & Offline

- Simpan catatan non-private → `NoteSnapshotWriter` menulis `{slug}-v{n}.json` (versi baru berdampingan; lama immutable).
- Ganti ke `private` → semua file snapshot slug dihapus; hapus catatan → file + entri manifest ikut dihapus.
- Papan membaca `manifest-index.json` (daftar `public` saja); detail memprioritaskan snapshot, fallback DB untuk `private`.
- Service worker (`public/sw.js`): cache-first untuk `/snapshots/`, `/build/`, `/storage/`; network-first untuk halaman; `/notes` (dinding, data privat) tidak pernah di-cache; fallback ke `/offline`. Bump `VERSION` di `sw.js` setiap perubahan strategi cache.

## 8. Keamanan

- Catatan `private` yang diakses selain pemilik → **404** (tidak membocorkan keberadaan).
- Validasi server: `body` wajib (maks 20.000 karakter), `title` maks 255, `color`/`visibility` enum.
- Edit/hapus selalu menyasar `slug + user_id` milik pemilik.
- Slug divalidasi regex (`^[a-z0-9]{6,20}$`) sebelum dipakai membaca file (anti path traversal).

## 9. Testing & CI

```bash
php artisan test --compact   # suite fitur (auth, catatan, offline)
vendor/bin/pint --dirty      # format kode
npm run build                # build aset
```

CI (`.github/workflows/ci.yml`) menjalankan build aset, Pint, dan test di setiap push/PR.

## 10. Deployment

- `public/snapshots/` wajib disajikan sebagai file statis; set `NOTES_SNAPSHOTS_PATH` bila dipindah (lihat `.env.example`).
- Setelah deploy: `php artisan migrate --force`, `npm run build`, `php artisan optimize`.
- Snapshot lama (`*-v{n}.json`) immutable — jangan dihapus manual.

## 11. Ide Lanjutan

- Tautan kolaborasi (tulis bersama pada catatan `unlisted`).
- Pencarian & tag untuk papan publik.
- Markdown opsional dengan sanitizer.
- Widget/penempatan bebas di papan (drag).
