# 🚀 LogiFlow API — Enterprise Backend Platform

**LogiFlow** is a high-performance, enterprise-grade backend service built with **Laravel**. Originally designed around standard RESTful domain concepts, it evolved from traditional MVC paradigms into a production-ready, highly decoupled, and performant backend architecture.

---

## 🏛️ Architectural Highlights

The project follows senior software engineering principles to ensure strict data integrity, high testability, scalable database operations, and clear separation of concerns.

```
Client Request
      │
      ▼
 Form Request (Auto-Validation & Rules)
      │
      ▼
 Data Transfer Object (Immutable Data Contract)
      │
      ▼
 Action / Domain Service (Business Logic Execution within DB Transactions)
      │
      ▼
 Custom Eloquent Builder / Scopes (Encapsulated Database Queries)
      │
      ▼
 API Resource (Strict Data Masking / Schema Decoupling)
      │
      ▼
 JSON Response

```

### Key Engineering Patterns Applied:

1. **Immutable Data Transfer Objects (DTOs):**
* Eliminates side effects across services and queue background jobs.
* Utilizes PHP `readonly` classes and named parameters to ensure `Data In === Data Out`.


2. **Single-Responsibility Action Classes:**
* Business logic is decoupled from Controllers into invokable/executable Action classes (e.g., `EnrollStudentAction`).
* Handles database persistence wrapped inside atomicity-guaranteeing **Database Transactions** (`DB::transaction()`).


3. **Form Request & DTO Pipeline:**
* Requests automatically handle validation, authorization, and mapping straight into DTOs via static factory methods (`fromRequest()`).
* Keeps Controllers paper-thin and purely orchestrational.


4. **Custom Eloquent Query Builders:**
* Replaces heavy, unnecessary Repository Pattern boilerplate with extended `Illuminate\Database\Eloquent\Builder` classes.
* Provides fluent, domain-expressive query methods (e.g., `->whereActive()->forUser($id)`) without breaking ORM flexibility.


5. **API Resources (`JsonResource`) as Data Firewalls:**
* Protects internal database schemas from leaking to public clients.
* Prevents accidental exposure of sensitive keys or unnecessary payload bloating.


6. **Database Performance Guardrails:**
* Enforces global `Model::preventLazyLoading(! app()->isProduction())` in local/testing environments to throw immediate runtime exceptions when N+1 query bugs occur.
* Leverages eager loading (`with()`) and relationship aggregate queries (`withCount()`).



---

## 🛠️ Tech Stack & Requirements

* **Language:** PHP 8.2+
* **Framework:** Laravel 10.x / 11.x
* **Database:** MySQL / PostgreSQL
* **Cache & Queues:** Redis
* **Testing:** PHPUnit / Pest

---

## 🚀 Getting Started

### 1. Clone & Install Dependencies

```bash
git clone https://github.com/your-username/logiflow.git
cd logiflow

composer install

```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate

```

Configure your `.env` database and Redis connections:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=logiflow
DB_USERNAME=root
DB_PASSWORD=secret

QUEUE_CONNECTION=redis
CACHE_STORE=redis

```

### 3. Run Migrations & Seeders

```bash
php artisan migrate --seed

```

### 4. Serve the Application

```bash
php artisan serve

```

---

## 🧪 Testing & Code Quality

LogiFlow maintains rigorous testing standards across Unit, Integration, and Feature levels.

```bash
# Run test suite
php artisan test

# Run static analysis
vendor/bin/phpstan analyse

```

---

## 📁 Directory Structure Overview

```
app/
├── Actions/                  # Domain Business Logic / Use Cases
│   └── Courses/
│       └── EnrollStudentAction.php
├── Builders/                 # Custom Eloquent Query Builders
│   └── EnrollmentBuilder.php
├── DataTransferObjects/      # Immutable Request Payload Mappings
│   └── EnrollmentData.php
├── Exceptions/               # Custom Domain Exceptions
├── Http/
│   ├── Controllers/          # Orchestration layer (Thin controllers)
│   ├── Requests/             # Input validation rules
│   └── Resources/            # API Response formatting
└── Models/                   # Eloquent Domain Models

```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).