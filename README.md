#Food Delivery Backend API

Laravel 8 REST API powering the [Food Flutter app](https://food-delivery-app-ce205.web.app). Handles user authentication, product catalogue, order management, and a hosted PayPal payment flow that redirects back to the Flutter frontend on completion.

**Live API:** [shoppingappbackend-production-85e6.up.railway.app](https://shoppingappbackend-production-85e6.up.railway.app)

---

## Background

This backend was built to support the Flutter food delivery app. It started as a standard Laravel API project and was extended with:

- Laravel Passport token-based authentication for mobile clients
- A hosted payment page flow compatible with Flutter web (full browser redirect, not WebView)
- Direct PayPal REST API v1 integration via Guzzle (replacing the PHP 8-incompatible PayPal SDK)
- Deployment to Railway using FrankenPHP and Railpack
- HTTPS enforcement behind Railway's TLS proxy

---

## Features

- **Authentication** — register, login, token refresh via Laravel Passport
- **Product catalogue** — products, categories, recommended and popular items
- **Order management** — place orders, track status, order history
- **Payment gateway** — hosted payment page with PayPal integration; redirects back to the Flutter app on success or failure via configurable callback URLs
- **Push notifications** — Firebase Cloud Messaging token storage per customer
- **File serving** — uploaded product images served via `/uploads/{path}` with CORS headers
- **Admin panel** — full back-office at `/admin` via Laravel Admin
- **Google Maps** — address geocoding support via the Google Maps API

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 8 (PHP 8.3) |
| Web server | FrankenPHP + Caddy |
| Authentication | Laravel Passport (OAuth2) |
| Payment | PayPal REST API v1 via Guzzle |
| Database | MySQL |
| Hosting | Railway (Railpack build) |
| Admin panel | encore/laravel-admin |

---

## Getting Started

### Prerequisites

- PHP 8.3
- Composer
- MySQL

### Installation

```bash
git clone https://github.com/J-arobo/Shoping_app_backend
cd Shoping_app_backend

composer install
cp .env.example .env
php artisan key:generate
php artisan passport:install
php artisan migrate
php artisan serve
```

---

## Environment Variables

| Variable | Description |
|---|---|
| `APP_ENV` | `local` or `production` |
| `APP_KEY` | Laravel app key |
| `APP_URL` | Full base URL of the backend |
| `DB_HOST` | MySQL host |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `PAYPAL_CLIENT_ID` | PayPal app client ID |
| `PAYPAL_SECRET` | PayPal app secret |
| `PAYPAL_MODE` | `sandbox` or `live` |
| `GOOGLE_MAPS_API_KEY` | Google Maps API key for address geocoding |

> In production, set `APP_ENV=production` so that HTTPS URLs are forced correctly behind Railway's TLS proxy.

---

## Payment Flow

The Flutter app opens a full browser redirect to the backend payment page:

```
GET /payment-mobile
  ?customer_id=<id>
  &order_id=<id>
  &callback=<url-encoded success URL>
  &cancel_url=<url-encoded failure URL>
```

The backend stores both URLs on the order, presents a payment method selector, then:

1. User selects PayPal → `POST /pay-paypal` → redirects to PayPal approval page
2. PayPal returns to `GET /paypal-status` with payer credentials
3. Backend executes the payment via PayPal REST API v1
4. On success → redirects to `callback` (Flutter's order-successful screen)
5. On failure → redirects to `cancel_url`

---

## Project Structure

```
app/
  Http/Controllers/
    Api/V1/                   # Mobile API endpoints
      Auth/                   # Login, register, token refresh
      CustomerController      # Profile, Firebase token
      OrderController         # Place and track orders
      ProductController       # Products and categories
      ConfigController        # App config (currency, zone, etc.)
    PaymentController         # Payment page, success/fail handlers
    PaypalPaymentController   # PayPal token + payment execution
  Models/                     # Eloquent models (Order, User, etc.)
routes/
  api.php                     # /api/v1/* mobile routes (Passport-protected)
  web.php                     # Payment pages and admin
database/migrations/          # Schema migrations
resources/views/
  payment-view.blade.php      # Payment method selection page
  payment-success.blade.php   # Fallback success page
  payment-fail.blade.php      # Fallback failure page
```

---

## Deployment (Railway)

The project uses [Railpack](https://railpack.io) for zero-config builds on Railway.

**Start Command** (Settings → Deploy → Start Command):
```
php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && frankenphp run --config /etc/caddy/Caddyfile
```

> Remove `php artisan migrate --force &&` after the initial migration so it does not run on every deploy.

---

## Related Repositories

- [food_delivery_app3](https://github.com/J-arobo/food_delivery_app3) — Flutter frontend, deployed on Firebase Hosting

---

## Author

**Joseph** — [github.com/J-arobo](https://github.com/J-arobo)
