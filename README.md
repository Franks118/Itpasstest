# ItPassTest Setup Guide

This project has two apps:

- `backend` = Laravel 13 API
- `frontend` = Angular 21 app

## 1. Prerequisites

Install these first:

- **PHP 8.3+**
- **Composer 2+**
- **Node.js 20+** (Node 22 LTS recommended)
- **npm 11+**
- **SQLite** (default) or **MySQL/MariaDB** (optional alternative)

## 2. Backend setup (Laravel API)

Open PowerShell in the project root, then run:

```powershell
cd backend
composer install
Copy-Item .env.example .env -Force
php artisan key:generate
```

## 3. Database setup

### Option A (default): SQLite

1. Create the SQLite file:

```powershell
New-Item -Path database\database.sqlite -ItemType File -Force
```

2. In `backend\.env`, use:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

3. Run migrations and seed initial data:

```powershell
php artisan migrate --seed
```

### Option B: MySQL / MariaDB

1. Create a database (example: `itpasstest`).
2. In `backend\.env`, update:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=itpasstest
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

3. Run migrations and seed:

```powershell
php artisan migrate --seed
```

## 4. Run backend

From `backend`:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

API base URL: `http://127.0.0.1:8000/api`

## 5. Frontend setup (Angular)

Open a second terminal and run:

```powershell
cd frontend
npm install
npm start
```

Frontend URL: `http://localhost:4225`

## 6. API URL note

Frontend currently calls:

`http://127.0.0.1:8000/api`  
in `frontend\src\app\core\api.service.ts`.

If you run backend on a different host/port, update that file.

## 7. Useful commands

Backend tests:

```powershell
cd backend
php artisan test
```

Frontend tests:

```powershell
cd frontend
npm test
```

Reset DB and reseed:

```powershell
cd backend
php artisan migrate:fresh --seed
```

