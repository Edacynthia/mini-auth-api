# Mini Auth API – Laravel (Advanced Edition)

A lightweight authentication micro-service built with **Laravel**, supporting:

- **JWT Authentication** (login, register, protected routes)  
- **API Key Authentication** (using custom middleware)  
- Clean route structure using `Application::configure()` (Laravel 11+ style)

---

## 🚀 Features

### ✅ JWT Authentication
- Generate JWT tokens on login  
- Validate tokens on protected routes  
- Uses a custom middleware `auth.api`  
- No Laravel Kernel required (Laravel 11+)

### ✅ API Keys
- Create and manage API keys  
- Validate requests using API keys  
- Middleware checks:
  - Key exists
  - Key active
  - Not expired  
- Tied to services (e.g., “mobile-app”, “admin-dashboard”)

### 🔒 Protected Endpoints
Endpoints under `Route::middleware('auth.api')` require a valid API key or JWT token.

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   │   └── ApiOrJwt.php
├── Models/
│   └── ApiKey.php
routes/
├── api.php
bootstrap/
├── app.php
```

---

## 🛠 Installation & Setup

### 1. Clone the project
```bash
git clone https://github.com/your-repo/mini-auth-api.git
cd mini-auth-api
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment

Copy `.env.example` → `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Configure database in `.env`:

```env
DB_DATABASE=mini_auth
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Run migrations
```bash
php artisan migrate
```

---

## 🔐 JWT Setup

Generate JWT secret:

```bash
php artisan jwt:secret
```

---

## 🔑 API Key Model

Your `ApiKey.php` should include:

```php
protected $fillable = [
    'service_name',
    'key',
    'expires_at',
```
## 🧪 Testing Authentication

### 1. Test API key protected route

Create a test API key via Tinker:

```bash
php artisan tinker
```

```php
ApiKey::create([
    'service_name' => 'test-service',
    'key' => '123456789',
    'expires_at' => now()->addDays(30)
]);
```

Call protected endpoint:

```
GET /api/protected
Headers:
  X-API-KEY: 123456789
```

### 2. Test JWT-protected route

Login:

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "password"
}
```

Copy the JWT token and access:

```
GET /api/protected
Headers:
  Authorization: Bearer <your-jwt-token>
```

---

## 🔧 Routes Example

```php
Route::middleware('auth.api')->get('/protected', function () {
    return ['message' => 'Access granted'];
});
```

Create API key route:

```php
Route::middleware('auth.api')->post('/keys/create', [ApiKeyController::class, 'create']);
```