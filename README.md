You are completely right—my apologies for confusing LogiFlow with the EduStream e-learning domain! Let's rewrite the README so it accurately reflects **LogiFlow** as an enterprise-grade **Shipment & Logistics Backend Engine**, highlighting the core domain models (`User`, `Shipment`), the architectural patterns, and testing coverage.

---

# 📦 LogiFlow API — Enterprise Shipment & Logistics Platform

**LogiFlow** is a high-performance, enterprise-grade backend service built with **Laravel**. It is engineered to handle complex logistics workflows, real-time shipment status tracking, dispatcher assignments, and automated delivery lifecycle updates with high concurrency and strict data consistency.

---

## 🏛️ Architectural Highlights

The platform transitions traditional MVC patterns into a clean, decoupled **Domain-Driven & Action-Oriented Architecture**:

```
Client Request
      │
      ▼
 Form Request (Validation & Authorization)
      │
      ▼
 Data Transfer Object (Immutable Data Contract)
      │
      ▼
 Action / Domain Service (Business Logic & DB Transactions)
      │
      ▼
 Custom Eloquent Builder / Scopes (Query Encapsulation)
      │
      ▼
 API Resource (Strict Data Masking / Schema Decoupling)
      │
      ▼
 JSON Response

```

### Key Engineering Patterns Applied:

1. **Immutable Data Transfer Objects (DTOs):**
* Encapsulates shipment details, sender/recipient metadata, and payload metrics into strongly typed PHP `readonly` DTOs.
* Guarantees immutable state across background workers and services.


2. **Single-Responsibility Action Classes:**
* Business processes (e.g., `CreateShipmentAction`, `AssignDispatcherAction`, `UpdateShipmentStatusAction`) are isolated into dedicated invokable classes.
* Ensures status transitions occur safely within atomic **Database Transactions** (`DB::transaction()`).


3. **Custom Eloquent Query Builders (`ShipmentBuilder`):**
* Replaces bloated Repositories with custom query builder extensions.
* Provides fluent, expressive domain queries:
```php
Shipment::query()
    ->wherePending()
    ->forCustomer($userId)
    ->withLatestStatus()
    ->get();

```




4. **API Resources as Firewalls:**
* Protects internal database tables, raw tracking coordinates, and system flags from leaking to external clients.


5. **Database Performance Guardrails:**
* Global protection against N+1 query bugs via `Model::preventLazyLoading(! app()->isProduction())`.
* Optimized database queries using indexed status lookups and eager-loaded relationship aggregations (`withCount()`, `with()`).



---

## 🚚 Domain Models & Relationships

* **`User`**: Represents Customers, Dispatchers, Drivers, and System Administrators.
* **`Shipment`**: The core domain entity representing parcel details, origin/destination coordinates, weight/dimensions, current status (`pending`, `in_transit`, `delivered`, `cancelled`), tracking numbers, and assigned drivers.

---

## 🧪 Rigorous Testing Suite

LogiFlow prioritizes reliable backend behavior with comprehensive **Feature and Unit Test Suites** written using Pest / PHPUnit:

* **Domain Unit Tests:** Validate DTO conversions, status state-machine transitions, and custom query builder scopes.
* **Feature & Integration Tests:** Verify complete HTTP endpoints, form validation rules, database transaction rollbacks upon failures, and queue dispatching for tracking updates.

```bash
# Run the complete test suite
php artisan test

# Run tests in parallel for maximum speed
php artisan test --parallel

```

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

Set up your database and Redis configuration in `.env`:

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

### 3. Run Migrations & Database Seeders

```bash
php artisan migrate --seed

```

### 4. Serve the Application

```bash
php artisan serve

```

---

## 📁 Directory Structure

```
app/
├── Actions/                  # Domain Business Logic (e.g., CreateShipmentAction)
│   └── Shipments/
├── Builders/                 # Custom Eloquent Query Builders (e.g., ShipmentBuilder)
├── DataTransferObjects/      # Immutable Request & Payload Mappings (e.g., ShipmentData)
├── Http/
│   ├── Controllers/          # Paper-thin HTTP Orchestrators
│   ├── Requests/             # Input Validation Rules
│   └── Resources/            # API Response Mappers
├── Models/                   # Domain Models (User, Shipment)
tests/
├── Feature/                  # HTTP Endpoint & Pipeline Integration Tests
└── Unit/                     # DTO, Builder, and Action Unit Tests

```

---

## 📄 License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).