# 📚 Library Management System

Sistem za upravljanje bibliotekom sa administracijom, zalihama, pretplatama i pozajmicama.

## 🎯 Karakteristike

- ✅ Administracija knjiga (cijene, zalihe)
- ✅ Praćenje pozajmica i prodaje
- ✅ Sistem upozorenja za niske zalihe
- ✅ Korisnički računi i pretplate
- ✅ Uloge: admin, librarian, postman, user
- ✅ Dashboard sa statistikom

## 🚀 Stack

| Komponent    | Verzija    |
| ------------ | ---------- |
| Laravel      | 10+        |
| PHP          | 8.0+       |
| Vue 3        | 3.3+       |
| Inertia.js   | 0.13+      |
| Tailwind CSS | 3.3+       |
| MySQL        | 5.7 / 8.0+ |
| MariaDB      | 10.6+      |

## 📦 Instalacija

### Zahtjevi

- PHP 8.0+
- Composer
- Node.js & npm
- MySQL/MariaDB

### Koraci

```bash
# Kloniraj projekt
git clone https://github.com/Vladimir5863/library
cd library

# Instaliraj PHP zavisnosti
composer install

# Instaliraj JS zavisnosti
npm install

# Kopiraj .env fajl
cp .env.example .env

# Generiši App Key
php artisan key:generate

# Konfiguruj bazu u .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=library
# DB_USERNAME=root
# DB_PASSWORD=

# Pokreni migracije
php artisan migrate

# (Opcionalno) Popuni testne podatke
php artisan migrate:fresh --seed

# Pokreni Vite dev server
npm run dev

# U drugoj konzoli, pokreni Laravel server
php artisan serve
```

Aplikacija je dostupna na: `http://localhost:8000`

## 🗂️ Struktura projekta

```
library/
├── app/
│   ├── Models/          # Eloquent modeli
│   ├── Http/
│   │   ├── Controllers/ # Business logika
│   │   └── Middleware/  # Auth, uloge...
│   └── Providers/       # Service providers
├── resources/
│   ├── js/Pages/        # Vue komponente
│   └── css/             # Tailwind
├── routes/              # Web rute
├── database/
│   ├── migrations/      # DB šema
│   └── seeders/         # Test data
└── public/              # Javno dostupno
```

## 🔐 Autentifikacija & Uloge

- **Admin** – Puna kontrola (korisnici, knjige, pretplate, pozajmice)
- **Librarian** – Upravljanje pozajmicama
- **Postman** – Isporuke
- **User** – Obični korisnik

## 🌍 Baza podataka

### Tabele i veze

- **users** – Korisnici sistema
- **books** – Glavna tabela knjiga
- **prices** – Cene (povezana sa `books`)
- **loans** – Pozajmice (povezana sa `users` i `books`)
- **sells** – Prodaja (povezana sa `users` i `books`)
- **subscriptions** – Pretplate (povezana sa `users`)
- **uses** – Korišćenje (povezana sa `users` i `books`)
- **news** – Novosti (povezana sa `books`)

## 🔔 Stock Alert System

Sistem automatski beleži ako neke knjige fali u:

- `loans` – ako je `remainingForLoan` kritično nisko
- `sells` – ako je `remainingForSell` kritično nisko

Admin može videti sve kritične stavke na `/admin/bookalert` i brzo ažurirati zalihe.

## 📝 Environment Varijable

```env
APP_NAME=Library
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
```

## 📚 Dodatne resurse

- [Laravel dokumentacija](https://laravel.com/docs)
- [Vue 3 dokumentacija](https://vuejs.org/)
- [Inertia.js](https://inertiajs.com/)
- [Tailwind CSS](https://tailwindcss.com/)


**Zadnja ažuriranja**: Mart 2026
