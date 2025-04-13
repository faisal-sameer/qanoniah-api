# 🔥 Qanoniah API

This is the **Laravel API** backend for the Qanoniah temperature processing system. It provides authentication, job processing, real-time event broadcasting, and result storage.

---

## ⚙️ Setup Instructions

### 🧱 1. Clone the repository

```bash
git clone https://github.com/your-username/qanoniah.git
cd qanoniah/qanoniah-api
```

---

### 🛠️ 2. Install PHP dependencies

```bash
composer install
```

---

### 📄 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

#### Update `.env`:

```env
DB_DATABASE=qanoniah
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

APP_URL=http://127.0.0.1:8000
```

---

### 🧬 4. Migrate the database

```bash
php artisan migrate
```

> ✅ Make sure MySQL is running and database `qanoniah` exists

---

### 🛰️ 5. Run Laravel server

```bash
php artisan serve
```

The API will be available at: [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)

---

### ⚡ 6. Queue worker for jobs

This project uses Laravel Queues and Redis. Start the worker:

```bash
php artisan queue:work
```

---

### 📡 7. Real-time Broadcasting (via Laravel Echo Server)

```bash
npm install -g laravel-echo-server
laravel-echo-server init
laravel-echo-server start
```

Ensure `.env` has:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1
```

---

## ✅ API Features

- ✅ User Registration & Login (Sanctum)
- ✅ Job submission & queue processing
- ✅ Result aggregation & storage
- ✅ Real-time job status via WebSocket
- ✅ Job metrics (execution time, memory, rows)
- ✅ Clean RESTful structure

---

## 📂 Folder Structure Highlights

```
app/
├── Http/Controllers
├── Jobs/
├── Models/
├── Events/
├── Providers/
database/
routes/
├── api.php
```

---

## 🛠️ Example Usage

Submit a job (POST `/api/jobs`)  
Get job results (GET `/api/jobs/{id}/result`)  
Authenticate (POST `/api/login`, `/api/register`)  

---

## 🪪 License

This project is open-source under the [MIT License](LICENSE).
