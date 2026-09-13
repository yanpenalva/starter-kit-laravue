# 🚀 LaraVue Starter Kit

The **LaraVue Starter Kit** is a full-stack boilerplate combining **Laravel 13** and **Vue 3 + Quasar Framework**, built for scalable, testable, and maintainable web applications.
It provides a pre-configured environment for authentication, user and permission management, dashboards, REST APIs, automated testing, and development observability.

---

## 📑 Table of Contents

1. [Architecture Overview](#️-architecture-overview)
2. [Installation](#️-installation)
3. [Husky (Git Hooks)](#️-husky-git-hooks)
4. [Database Configuration](#️-database-configuration)
5. [Fix Access Permissions (Spatie Permission Cache)](#️-fix-access-permissions-spatie-permission-cache)
6. [Key Features](#️-key-features)
7. [Technology Stack](#️-technology-stack)
8. [Queues and Horizon Monitoring](#️-queues-and-horizon-monitoring)
9. [Project Architecture](#️-project-architecture)
10. [Docker Services](#️-docker-services)
11. [Testing](#️-testing)
13. [Best Practices](#️-best-practices)
13. [Commit Conventions](#️-commit-conventions)
14. [Code Standards](#️-code-standards)

---

## ⚙️ Architecture Overview

This starter kit runs entirely in **Docker (Alpine)** containers for consistent local and CI environments.
It includes containers for the app, Nginx, PostgreSQL, Redis, and Mailpit, ensuring isolated and reproducible development.

---

## 📥 Installation

### 1️⃣ Clone the repository

```bash
git clone https://github.com/yanpenalva/starter-kit-laravue.git
cd starter-kit-laravue
```

### 2️⃣ Environment setup

```bash
cp .env.example .env
```

### 3️⃣ Build and start containers

```bash
docker compose up -d --build --force-recreate --remove-orphans
docker exec starterkit-app php artisan key:generate
```

Verify your database settings:

```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=starterkit
DB_USERNAME=postgres
DB_PASSWORD=admin
```

### 4️⃣ Install dependencies

```bash
docker exec -it starterkit-app bash
composer install
npm install
php artisan migrate --seed
```

### 5️⃣ Fix permissions (Linux/Mac)

```bash
chmod +x permissions.sh
./permissions.sh
```

### 6️⃣ Run the app

```bash
docker exec -it starterkit-app npm run dev
```

Access: **[http://localhost:8001](http://localhost:8001)**

---

## 🪝 Husky (Git Hooks)

The project uses **Husky** to enforce pre-commit and pre-push checks (lint, tests, commit validation).

### Setup

After dependencies installation:

```bash
npx husky init
```

> Do **not** overwrite existing hooks — this repository already includes custom `pre-commit` and `pre-push` scripts.

If Husky overwrites them, restore:

```bash
git restore .husky/pre-commit .husky/pre-push
```

---

## 🛠️ Database Configuration

> PostgreSQL 16 is the default database.
> Created automatically on first container startup.

To access manually inside the container:

```bash
docker exec -it starterkit-db psql -U postgres -d starterkit
```

---

## 🧩 Fix Access Permissions (Spatie Permission Cache)

If a user has correct permissions in the database but still receives:

> “You do not have permission to perform this action.”

Run inside the container:

```bash
docker compose exec starterkit-app php artisan permission:cache-reset
```

### Versions <5.x:

```bash
docker compose exec starterkit-app php artisan cache:forget spatie.permission.cache
```

---

## 📌 Key Features

- **Laravel 13** — Modular, RESTful backend
- **Vue 3 + Quasar** — Modern reactive UI
- **Spatie Permission** — Role & permission system
- **Spatie Activity Log** — Transparent audit trail
- **PestPHP** — Expressive test framework
- **Larastan + PHP Insights** — Static analysis & code quality
- **Scramble** — Automatic API documentation
- **Pulse + Telescope + Debugbar + Horizon** — Monitoring and debugging

---

## ⚙️ Technology Stack

- **Backend:** Laravel 13 (PHP 8.5 on Alpine)
- **Frontend:** Vue 3.5 + Quasar Framework + Vite
- **Database:** PostgreSQL 16
- **Cache/Queue:** Redis 7
- **Mail:** Mailpit (SMTP emulator)
- **Containerization:** Docker Compose
- **Testing:** PestPHP
- **Static Analysis:** Larastan
- **Monitoring:** Pulse, Telescope, Debugbar, Horizon

## 🚢 Preparing for production

This repository is configured primarily for local development. Before using it
in production, apply the following changes:

1. Use a production Compose/deployment configuration without bind mounts from
   the host and build the frontend assets during the image build.
2. Set `APP_ENV=production`, `APP_DEBUG=false`, a unique `APP_KEY`, and
   production-specific values for the database, Redis, mail and application URL.
3. Replace the development PHP configuration with
   `docker/PHP/php-prd.ini`, disable Xdebug and run the application with a
   non-development process configuration.
4. Store passwords, encryption keys and tokens in the deployment secret
   manager; never copy `.env.example` values directly to production.
5. Restrict CORS and `SANCTUM_STATEFUL_DOMAINS` to the real frontend domains,
   enable HTTPS and configure trusted proxies correctly.
6. Do not expose Telescope, Horizon, Pulse or Scramble publicly without
   authentication and an explicit production access policy.
7. Configure persistent logs, queue workers, scheduler execution, database
   backups and health checks before accepting traffic.

---

## ⚙️ Queues and Horizon Monitoring

The Starter Kit integrates **Redis queues** and **Laravel Horizon** for distributed asynchronous job execution and real-time monitoring.

### 🧠 How It Works

- **Jobs** are dispatched using Redis queues via `queue:work`.
- **Supervisor** automatically manages the worker lifecycle (restart, retry, timeout).
- **Horizon** provides a dashboard for job tracking, performance metrics, and failure insights.

### Example Job Dispatch

```php
dispatch(new ImportProcessJob())->onQueue('imports');
dispatch(new SendEmailJob())->onQueue('emails');
```

Each queue runs independently, enabling **parallel processing** and **load distribution**.

### Supervisor Configuration Example

```ini
[program:laravel-queue]
command=/usr/local/bin/php /var/www/html/artisan queue:work redis --queue=default --daemon --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
```

- `--tries`: number of attempts before failure logging.
- `--max-time`: ensures workers restart periodically to prevent memory leaks.
- `--daemon`: keeps workers running persistently.

### Access Horizon Dashboard

```
http://localhost:8001/horizon
```

> Access is restricted to **admin users only** in all environments (local, staging, production).

Authorization defined in:

```php
Gate::define('viewHorizon', fn($user) => $user && $user->isAdmin());
```

### Horizon Features

- Real-time job and queue stats
- Retry failed jobs
- Queue balancing and prioritization
- Job execution metrics and runtime distribution

### Recommended Practices

- Name queues descriptively (`imports`, `emails`, `notifications`)
- Split heavy operations into chunks (`chunk(100)`)
- Avoid complex listeners — isolate logic in jobs
- Use `Bus::batch()` for large batch operations

---

## 🚀 Project Architecture

Applies **Action Pattern** for isolated business logic and **Event-Driven Design (EDD)** for asynchronous processes.

---

## 🧱 Docker Services

All services are defined in `docker-compose.yaml`.

---

## 🧪 Testing

### 1️⃣ Test Database

If `starterkit_test` is not created automatically, run:

```bash
chmod +x docker-entrypoint-initdb.sh
./docker-entrypoint-initdb.sh
```

### 2️⃣ Run Tests

```bash
docker compose exec app composer test
```

With coverage:

```bash
docker compose exec app composer test:coverage
```

Parallel mode:

```bash
docker compose exec app env APP_ENV=testing php artisan test --parallel
```

---

## 🧠 Best Practices

**Security**

- `APP_DEBUG=false` in production
- Generate unique `APP_KEY`
- Protect routes with `auth:sanctum`
- Mask sensitive logs

**Performance**

- Cache queries (tagged TTLs)
- Optimize autoloaders/config caches
- Use `DB::transaction()` for atomicity

**Code Quality**

- Maintain ≥80% coverage
- Run Larastan + PHP Insights regularly
- Keep controllers thin; logic in Actions

---

## 🔄 Commit Conventions

Follow **Conventional Commits**.

```
<type>: <Jira task ID> - <description>
```

**Types:** feat, fix, docs, refactor, test, perf, build, ci, ops, chore, revert

---

## 📝 Code Standards

- `declare(strict_types=1);` required
- Method names ≤ 5 words
- Use imperative verbs
- Variables in `camelCase`
- Routes follow `{resource}.{action}`
- API versioning `/api/v1/...`
- Coverage ≥ 80 lines

### Static Analysis

```bash
docker exec -it starterkit-app composer phpstan
```

### Code Quality

PHP Insights is not currently installed in this starter kit.

### API Documentation

```bash
docker exec -it starterkit-app php artisan scramble:export
```

### Tests

```bash
docker exec -it starterkit-app composer test
docker exec -it starterkit-app composer test:coverage
```

---

## ⚡ Makefile Shortcuts

```bash
make help
```

| Category      | Command                                          | Description                    |
| ------------- | ------------------------------------------------ | ------------------------------ |
| 📦 Containers | `make up` / `make down` / `make restart`         | Manage containers              |
| 💻 Dev        | `make shell` / `make front`                      | Enter container / run frontend |
| 🗄️ DB         | `make migrate` / `make rollback`                 | Manage migrations              |
| 🧪 Tests      | `make test` / `make test-all`                    | Run test suites                |
| ✨ Quality    | `make pint` / `make check`                       | Format and lint                |
| 🛠️ Tools      | `make artisan` / `make queue` / `make telescope` | Laravel utilities              |
