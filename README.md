# L’Atelier Architectural Studio — Backend & Vendor CAD Engine

PHP 8.2+ / Laravel 12 multi-vendor workshop portal, architectural quoting engine, live CAD visualizer API, and storefront product catalog.

---

## 🚀 Deploying to Render (render.com)

Render runs PHP applications using **Docker**. This repository includes a production-ready `Dockerfile`, `docker-entrypoint.sh`, and `render.yaml` configuration.

### Option 1: Render Web Service (Recommended)
1. Log in to [dashboard.render.com](https://dashboard.render.com).
2. Click **New +** > **Web Service**.
3. Connect your repository: `https://github.com/mahadihasandev/interior-webapp-Php-backend.git`.
4. Choose **Docker** as the Runtime (Render will automatically detect the `Dockerfile`).
5. Select Region (e.g., Oregon or Frankfurt) and the **Free** instance type.
6. Under **Environment Variables**, add:
   | Key | Value / Example | Notes |
   |---|---|---|
   | `APP_NAME` | `L'Atelier Architectural Studio` | Application title |
   | `APP_ENV` | `production` | Production mode |
   | `APP_DEBUG` | `false` | Disable stack traces in prod |
   | `APP_KEY` | *(generate via `php artisan key:generate --show` or use a 32-char base64 string)* | Required by Laravel |
   | `APP_URL` | `https://<your-backend-name>.onrender.com` | Your assigned Render backend URL |
   | `FRONTEND_URL` | `https://<your-frontend-name>.onrender.com` | Your deployed Next.js storefront URL |
   | `DB_CONNECTION` | `sqlite` | SQLite file auto-initialized in `/var/www/html/database` |
   | `SESSION_DRIVER` | `database` | Or `file` |
   | `CACHE_STORE` | `database` | Or `file` |
   | `RUN_MIGRATIONS` | `true` | Runs `php artisan migrate --force` automatically on deploy |
   | `RUN_SEEDS` | `true` | Runs `php artisan db:seed --force` on first deploy to seed demo accounts |
7. Click **Create Web Service**.

### Option 2: Render Blueprint (render.yaml)
1. In the Render Dashboard, click **New +** > **Blueprint**.
2. Select this repository. Render will read `render.yaml` and configure the service automatically.

---

## 💻 Local Development Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Install NPM dependencies & build Vite assets
npm install
npm run build

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Migrate and seed database
php artisan migrate:fresh --seed

# 5. Link public storage
php artisan storage:link

# 6. Run local server
php artisan serve
```

---

## 🔑 Demo Login Credentials
- **Super Admin**: `admin@interior.com` / `password123`
- **Vendor Admin (Apex Glass)**: `admin@apexglass.com` / `password123`
- **Workshop Production**: `workshop@apexglass.com` / `password123`
- **Vendor Admin (IronCraft)**: `admin@ironcraft.com` / `password123`
