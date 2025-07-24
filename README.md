# LaraVue Starter Kit

The **LaraVue Starter Kit** is an advanced project template that combines **Laravel 12** on the backend and **Vue 3** with **Quasar Framework** on the frontend, providing a solid foundation for scalable and modern web applications. This kit includes pre-configured authentication, user and permission management, an initial dashboard and a responsive side menu. It also supports automated testing and tools for monitoring and debugging.

## 📌 **Key Features**

- Laravel 12 - Robust and modern backend with RESTful API support.
- **⚡ Vue 3 + Quasar** - Progressive and reactive UI for dynamic interfaces.
- **🔑 Authentication** - Complete and ready-to-use login system.
- **👤 User Management** - Full control over users, roles and permissions.
- **Logs and Debugging**- Structured logs with Laravel Debugbar and auditing with Spatie Activity Log.
- **✅ Automated Testing** - Test coverage using **PestPHP** and **PHPUnit**.
- **🔍 Larastan** - Advanced static analysis to maintain code quality.
- **🗂️ Automatic Documentation** - Automatic generation of documentation with **Scramble**.

---

## ⚙️ **Technology Stack**

- **Laravel 12** | **PHP 8.4**
- **Vue 3.5** | **Quasar Framework**
- **Pinia (State Management)**
- **Spatial Permission**
- **PestPHP**
- **Larastan (PHPStan for Laravel)**
- **Scramble (API Documentation)**
- **Laravel Debugbar**
- **Larvel Horizon**
- **Laravel Telescope**
- **Spatie Activity Log**
- **Docker**

---

## 📥 Installation and Configuration

### 🔄 Cloning the repository

```bash
git clone git@github.com:yanbrasiliano/starter-kit-laravue.git
```

### 📂 Access the project directory

```bash
cd starter-kit-laravue
```

### 🐳 Run Docker Compose to spin up containers

```bash
docker compose up -d --build --force-recreate --remove-orphans
```

## 🛠️ Database Configuration

1 - Copy the env.example and create an instance of the database in your favorite software client.

2 - In the .env, assign the connection variables related to the instance that was created.

### 📦 Install Laravel and Vue.js dependencies

```bash
docker exec -it starterkit-app bash
composer install
npm install
php artisan migrate --seed
```

### 🛑 If you have problems with permissions

1. Give execute permission to the file:
   ```bash
   chmod +x permissions.sh
   ```
2. Now run the script:
   ```bash
   ./permissions.sh
   ```

### 🌐 Access the system

Run the command:

```bash
docker exec -it starterkit-app npm run dev
```

Now, access the system through the link:
http://localhost:8001

## 🚀 **Project Architecture**

We use Laravel's native Action Pattern, which promotes the separation of responsibilities by encapsulating each functionality in a specific action. This eliminates the need for custom Service Providers and facilitates code reuse and maintenance. Actions perform a single task, making the code cleaner, more predictable and testable.We adopted the Event-Driven Development (EDD) paradigm to decouple processes and improve the extensibility of the system. This model allows events to be triggered from actions or services and processed by Listeners asynchronously or synchronously, as required. This makes it easier to implement logic such as notifications, log auditing and integration with external systems without directly coupling these responsibilities to the main flow of the application.

📌 **Architecture Diagram**
[Application Architecture](./architecture.svg)

---

## 📊 **Database Schema Dump & ER Diagram**

The schema can be exported using the command:

```bash
pg_dump --schema-only --file=schema.sql “postgres://$(grep DB_USERNAME .env | cut -d ‘=’ -f2):$(grep DB_PASSWORD . env | cut -d '=' -f2)@$(grep DB_HOST .env | cut -d '=' -f2):$(grep DB_PORT .env | cut -d '=' -f2)/$(grep DB_DATABASE .env | cut -d '=' -f2)”
```

DER generation:

```bash
npx @liam-hq/cli erd build --input $(pwd)/schema.sql --format=postgres --output-dir $(pwd)/public
```

The diagram will be accessible at `/der?key=access_key`. To do this, set `APP_DER_KEY` in `.env`.

---

## 🔄 **Commit Conventions**

We follow the **Conventional Commits** specification to maintain a clean commit history and facilitate semantic versioning. Every commit message should follow the structure:

```
<type>: <Jira task ID> - <description of what was done>
```

Where:

- **type**: Represents the type of change made. Examples include adding a feature, fixing a bug, etc.
- **Jira task ID**: The unique ID associated with the Jira task or activity related to the commit. This allows you to track which task the commit is addressing.
- **description of what was done**: A concise, clear explanation of what was changed, added, or fixed in the code.

### Commit Types:

- **feat**: Introduces a new feature to the application.
- **fix**: Fixes an issue or bug in the code.
- **docs**: Updates or adds documentation.
- **refactor**: Code changes that do not alter the functionality but improve the structure or readability.
- **test**: Adds or fixes tests to ensure the correctness of the code.
- **perf**: Performance improvements that make the application run more efficiently.
- **build**: Changes that affect the build process or tooling (e.g., npm, Docker).
- **ci**: Changes to the Continuous Integration or Continuous Deployment setup.
- **ops**: Modifications related to infrastructure, deployment, or operations.
- **chore**: Miscellaneous changes that don't fit into any of the above categories (e.g., updating dependencies).
- **revert**: Reverts a previous commit or set of changes.

### Benefits of Following This Convention:

- **Consistency**: The use of a standard format makes it easier for developers to understand commit history.
- **Traceability**: Linking each commit to a Jira task ID allows for better tracking and traceability.
- **Automation**: Enables the use of tools like semantic versioning and automatic changelog generation.

## Following this convention will help ensure that commits are clear, well-documented, and directly tied to specific tasks, making it easier to understand the context of each change.

## 📝 **Code Standards**

- Variables in `camelCase`, no short abbreviations (`$q` → `$query`).
- Routes follow `{resource}.{action}` (example: `roles.index`).
- Methods must be descriptive and contain up to 5 words (`getUserProfile()`).
- Run **PHPStan** for static validation:
  ```bash
  ./vendor/bin/phpstan analyze
  ```
- Generate API documentation:
  ```bash
  php artisan scramble:export
  ```
- Run PHP Insights

  ```bash
  ./vendor/bin/phpstan insights
  ```

  - To run the tests, first ensure that your local test database is configured correctly and that the tables have been created.

```bash
./docker-entrypoint-initdb.sh
```

This script will be create the test database.

- After, execute the tests with the command:

```bash
docker exec -it starterkit-app composer test
```
or

```bash
docker exec -it starterkit-app composer test:coverage
```

- Attention: if you want to run manually, remember to insert env APP_ENV=testing next to the command, for example

```bash
docker exec -it starterkit-app env APP_ENV=testing php artisan test --env=testing --parallel
```
If you do not enter it, TestCase will block the execution of the tests.

## 📌 **TODO List**

- [ ] Set phpstan max level(10), currently level is 8.
