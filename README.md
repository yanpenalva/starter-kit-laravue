# LaraVue Starter Kit

The **LaraVue Starter Kit** is an advanced project template that combines **Laravel 12** on the backend and **Vue 3** with **Quasar Framework** on the frontend, providing a solid foundation for scalable and modern web applications.
It includes pre-configured authentication, user and permission management, an initial dashboard, and a responsive side menu.
The kit supports automated testing and offers tools for monitoring and debugging.

---

## 📑 Table of Contents

1. [Key Features](#-key-features)
2. [Technology Stack](#-technology-stack)
3. [Installation and Configuration](#-installation-and-configuration)
4. [Database Configuration](#-database-configuration)
5. [Project Architecture](#-project-architecture)
6. [Database Schema & ER Diagram](#-database-schema--er-diagram)
7. [Commit Conventions](#-commit-conventions)
8. [Code Standards](#-code-standards)
<<<<<<< HEAD
9. [TODO List](#-todo-list)
=======
>>>>>>> dev

---

## 📌 Key Features

- **Laravel 12** – Robust backend with RESTful API support.
- **⚡ Vue 3 + Quasar** – Reactive UI for dynamic interfaces.
- **🔑 Authentication** – Complete login system.
- **👤 User Management** – Roles and permissions using Spatie Permission.
- **📝 Logs and Debugging** – Laravel Debugbar + Spatie Activity Log.
- **✅ Automated Testing** – PestPHP.
- **🔍 Larastan** – Advanced static analysis.
- **🗂️ Automatic Documentation** – Scramble for API documentation.

---

## ⚙️ Technology Stack

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Vue 3.5, Quasar Framework, Pinia (State Management)
- **Permissions:** Spatie Permission
- **Testing:** PestPHP
- **Static Analysis:** Larastan (PHPStan for Laravel)
- **Documentation:** Scramble
- **Debug & Monitoring:** Laravel Debugbar, Laravel Horizon, Laravel Telescope, Spatie Activity Log
- **Environment:** Docker

---

## 📥 Installation and Configuration

### 1️⃣ Clone the repository

```bash
git clone git@github.com:yanbrasiliano/starter-kit-laravue.git
```

### 2️⃣ Access the project directory

```bash
cd starter-kit-laravue
```

### 3️⃣ Start Docker containers

```bash
docker compose up -d --build --force-recreate --remove-orphans
```

---

## 🛠️ Database Configuration

> **Note:** PostgreSQL is the default database.

1. Copy `.env.example` to `.env` and update the database connection variables.
2. Create a PostgreSQL database using your preferred client or CLI.

---

## 📦 Install Dependencies

```bash
docker exec -it starterkit-app bash
composer install
npm install
php artisan migrate --seed
```

---

## 🛑 Permissions (Linux/Mac only)

If you face permission issues:

```bash
chmod +x permissions.sh
./permissions.sh
```

---

## 🌐 Access the System

```bash
docker exec -it starterkit-app npm run dev
```

Access: **http://localhost:8001**

---

## 🚀 Project Architecture

We use **Laravel's native Action Pattern** to encapsulate each functionality in a dedicated Action, promoting separation of concerns and maintainability.
We also adopt **Event-Driven Development (EDD)** to decouple processes, enabling asynchronous or synchronous event handling for features like notifications, audit logging, and integrations.

📌 **Architecture Diagram:** [Application Architecture](./architecture.svg)

---

## 📊 Database Schema & ER Diagram

### Export schema:

```bash
source .env && pg_dump --schema-only --file=schema.sql "postgres://${DB_USERNAME}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_DATABASE}"
```

### Generate ER diagram:

```bash
npx @liam-hq/cli erd build --input $(pwd)/schema.sql --format=postgres --output-dir $(pwd)/public
```

Diagram will be available at `/der?key=access_key` (set `APP_DER_KEY` in `.env`).

---

## 🔄 Commit Conventions

We follow **Conventional Commits** for clarity and semantic versioning.

Format:

```
<type>: <Jira task ID> - <description>
```

**Types:**

- `feat` – New feature
- `fix` – Bug fix
- `docs` – Documentation changes
- `refactor` – Code restructuring without changing behavior
- `test` – Adding/fixing tests
- `perf` – Performance improvements
- `build` – Build or tooling changes
- `ci` – CI/CD changes
- `ops` – Infrastructure or operations changes
- `chore` – Misc changes (e.g., deps update)
- `revert` – Reverting commits

---

## 📝 Code Standards

- **declare(strict_types=1);** in all PHP files.
- Naming: methods ≤ 5 words, avoid abbreviations, use imperative verbs.
- Variables in `camelCase` – avoid abbreviations.
- Routes follow `{resource}.{action}` (e.g., `roles.index`).
- Methods: descriptive, max 5 words.
- REST API versioned as `/api/v1` and breaking changes versioned.
- Minimum coverage: **lines ≥ 80%**
- Run static analysis:

```bash
docker exec -it starterkit-app composer run:phpstan
```

- Run PHP Insights:

```bash
docker exec -it starterkit-app composer run:phpinsights
```

- Generate API documentation:

```bash
docker exec -it starterkit-app php artisan scramble:export
```

- Run tests:

```bash
docker exec -it starterkit-app composer test
```

or with coverage:

```bash
docker exec -it starterkit-app composer test:coverage
```

To run manually with a specific environment:

```bash
docker exec -it starterkit-app php artisan optimize:clear && env APP_ENV=testing php artisan test --env=testing --parallel
```
