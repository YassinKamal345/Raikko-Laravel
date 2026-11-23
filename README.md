<p align="center">
  <img src="public/img/raikko_logo.jpg" alt="Raikko logo" width="160">
</p>

<h1 align="center">Raikko — E-commerce with Laravel</h1>

<p align="center">
  Online clothing store built with <b>Laravel 12 + Breeze</b> as a project for my ASIX studies at La Salle Gràcia.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white">
  <img src="https://img.shields.io/badge/Tailwind-CSS-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white">
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=flat-square&logo=alpinedotjs&logoColor=white">
  <img src="https://img.shields.io/badge/Docker-ready-2496ED?style=flat-square&logo=docker&logoColor=white">
</p>

---

## ✨ Features

**Store**
- Home page and product catalog (`/shop`)
- Product page with image gallery and size selection (sold-out sizes are disabled)
- Shopping cart stored in the browser (`localStorage`)
- Step-by-step checkout with shipping options (store pickup or home delivery +5 €) and 21% VAT
- Orders and order lines saved to the database; the cart is emptied after a successful order

**Users**
- Registration, login and password reset (Laravel Breeze)
- User profile editing
- Roles: the first registered user becomes `admin`, the rest are `user`

**Admin panel** (`/admin`)
- Create, edit and delete products
- Upload several images per product, delete them and change their order
- Manage stock per size (XS–XXL); stock is discounted when an order is placed

## 🧰 Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2, Laravel 12, Laravel Breeze |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Database | SQLite (local) · PostgreSQL driver included in the Docker image |
| Deploy | Docker (`php artisan serve` on port 10000, migrations run on start) |

## 🗂️ Data model

```
User ──< Order ──< OrderItem >── Product ──< ProductImage
                                    └──────< ProductSize
```

## 🚀 Run locally

Requirements: PHP 8.2+, Composer, Node.js 18+.

```bash
git clone https://github.com/YassinKamal345/Raikko-Laravel.git
cd Raikko-Laravel

composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate

npm run dev          # in one terminal
php artisan serve    # in another → http://localhost:8000
```

Register a user first: it will be the **admin** and can open `/admin`.

## 🐳 Docker

```bash
docker build -t raikko .
docker run -p 10000:10000 --env-file .env raikko
```

The container runs the migrations on start and serves the app on `http://localhost:10000`.

## 📁 Structure

```
app/Http/Controllers/   Home, Shop, Product, Checkout and Admin controllers
app/Models/             Product, ProductImage, ProductSize, Order, OrderItem, User
resources/views/        Blade views (home, shop, product, cart, checkout, admin)
database/migrations/    Database schema
public/img/             Logo and product images
public-branch/          Earlier static HTML/CSS version of the store
```

## 👤 Author

**Yassin Kamal** — ASIX student · [Portfolio](https://portfolio-sand-eight-20.vercel.app) · [LinkedIn](https://www.linkedin.com/in/yassin-kamal-simo/)
