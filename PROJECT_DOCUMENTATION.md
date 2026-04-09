# MCX Trading System Documentation

## 🚀 Quick Start (5 Minutes Setup)

### Backend
```bash
cd mcx_admin
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Flutter
```bash
cd mcx_app
flutter pub get
flutter run
```

## System Flow Diagram

```text
User (Mobile App)
    ↓
Flutter App (UI + API Calls)
    ↓
Laravel API (/api/v1)
    ↓
Services Layer
    ↓
Database (Users, Orders, Posts)
    ↓
External Live Rates API
```

## 🔑 Core Concepts

- **Demo Auth System** → No real login, backend always resolves one user
- **KYC Required Flow** → User must complete KYC before placing orders
- **Live Rates System** → External API + caching fallback
- **Order Lifecycle** → pending → completed (admin side)

## 1. Project Overview

### 1.1 Project Name

MCX Trading System

### 1.2 Purpose

This project is a mobile-first bullion trading system for gold and silver rates and order placement. It consists of:

- A Laravel backend that exposes REST APIs and serves an admin panel
- A Flutter mobile app used by clients to view rates, complete KYC, and place orders
- An admin web interface used to review orders and monitor dashboard data

Important current-state note:

- The repository contains a Laravel backend and one Flutter client app
- A separate Professional App is not present in the current codebase
- There is no production-grade authentication stack such as Sanctum or JWT in the current implementation
- Client login is currently local/demo mode

### 1.3 Key Features

| Area | Implemented Features |
|---|---|
| Market Data | Live rates API, fallback cache, stale-data handling, trend rendering in Flutter |
| Orders | Real backend order creation, DB persistence, admin order listing, client order confirmation flow |
| KYC | Multi-step Flutter KYC UI, PAN/Aadhaar/selfie upload, backend verification source of truth |
| News | Published posts exposed through API and rendered in Flutter |
| Profile | Backend-driven profile/KYC status endpoint |
| Admin Panel | Dashboard, all orders, pending orders, completed orders, rate pages, settings shell |

### 1.4 High-Level Architecture

```text
+--------------------+          HTTP/JSON          +----------------------+
| Flutter Client App |  <----------------------->  | Laravel API Backend  |
| (mcx_app)          |                             | (mcx_admin)          |
+--------------------+                             +----------------------+
         |                                                      |
         | local navigation/state                               | Eloquent ORM
         |                                                      |
         v                                                      v
+--------------------+                             +----------------------+
| Local UI State     |                             | SQLite / MySQL-ready |
| OrderSyncStore     |                             | DB tables            |
+--------------------+                             +----------------------+
                                                               |
                                                               v
                                                     +----------------------+
                                                     | Admin Blade Panel    |
                                                     | /admin/*             |
                                                     +----------------------+
```

### 1.5 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12.54.1 |
| PHP | PHP 8.2.12 |
| Frontend Mobile | Flutter app, Dart SDK constraint `^3.11.1` |
| HTTP Client | `http` package in Flutter |
| Charting | `fl_chart` |
| Image Capture/Upload | `image_picker` |
| Database | SQLite in local setup, schema is portable to MySQL-compatible setups |
| Admin UI | Laravel Blade templates |
| Styling | Custom Flutter theme and Laravel Blade/CSS |

## 2. System Architecture

### 2.1 Application Boundaries

| Application | Status in Repo | Responsibility |
|---|---|---|
| Admin Panel | Present | Monitor orders, dashboard summaries, static admin screens |
| Client App | Present | Live rates, KYC, profile, news, order placement |
| Professional App | Not present | Not implemented in current repository |

### 2.2 Admin, Client, and Backend Interaction

1. The Flutter client calls Laravel REST endpoints under `/api/v1/*`
2. Laravel validates input and reads/writes the database
3. Orders created by the Flutter client are persisted in `orders`
4. The admin panel reads the same `orders` table and displays submitted orders
5. KYC status is stored on the `users` table and exposed through `/api/v1/profile`

### 2.3 API Flow Between Flutter and Laravel

```text
Flutter Screen
  -> Service class
  -> HTTP request
  -> Laravel route
  -> Controller
  -> Service / Model / DB
  -> Standard API envelope
  -> Flutter model parsing
  -> UI rendering
```

Example order flow:

```text
TradeScreen
  -> ProfileApiService.fetchProfile()
  -> if profile.isVerified == false: open KYCFlowScreen
  -> if verified: open OrderConfirmationScreen
  -> OrderApiService.createOrder()
  -> POST /api/v1/orders
  -> OrdersController@store
  -> orders row inserted
  -> response returned
  -> OrderSyncStore updated
  -> OrdersScreen shows new order immediately
```

### 2.4 Authentication Flow

Current implementation:

| Topic | Current State |
|---|---|
| Client login | Demo/local only |
| OTP verification | Not real, UI-only |
| API token auth | Not implemented |
| Sanctum/JWT | Not installed |
| Backend user resolution | `LocalAppUserResolver` resolves one local demo user from config |

Important implication:

- This is not production authentication
- Order access control is enforced by backend KYC status, not by authenticated identity
- All app actions currently operate against a single locally resolved backend user

### 2.5 Backend Folder Structure

```text
mcx_admin/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   └── Api/V1/
│   ├── Models/
│   ├── Services/
│   └── Support/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/views/admin/
├── routes/
└── tests/
```

### 2.6 Flutter Folder Structure

```text
mcx_app/
├── lib/
│   ├── core/
│   │   ├── config/
│   │   ├── constants/
│   │   ├── theme/
│   │   └── widgets/
│   └── features/
│       ├── auth/
│       ├── kyc/
│       ├── news/
│       ├── orders/
│       ├── profile/
│       ├── rates/
│       ├── shared/
│       └── trade/
├── android/
├── ios/
└── pubspec.yaml
```

## 3. Backend Documentation

### 3.1 Setup Instructions

#### 3.1.1 Prerequisites

| Requirement | Version |
|---|---|
| PHP | 8.2+ |
| Composer | 2+ |
| Laravel | 12.x |
| Database | SQLite for local development |

#### 3.1.2 Installation

```bash
cd C:\xampp\htdocs\mcx_admin
composer install
copy .env.example .env
php artisan key:generate
```

#### 3.1.3 Environment Setup

Primary local environment values in `.env.example`:

| Variable | Purpose |
|---|---|
| `APP_URL` | Base Laravel URL |
| `DB_CONNECTION` | Local default is `sqlite` |
| `LIVE_RATES_URL` | Upstream bullion live rates feed |
| `LIVE_RATES_TIMEOUT_SECONDS` | Request timeout |
| `LIVE_RATES_CACHE_TTL_SECONDS` | Short cache TTL for rates |
| `API_PROFILE_NAME` | Demo app user name |
| `API_PROFILE_EMAIL` | Demo app user email |
| `API_PROFILE_PHONE` | Demo app user phone |
| `APP_SUPPORT_PHONE` | Support content returned to app |
| `APP_SUPPORT_MESSAGE` | Support message shown in app |
| `APP_KYC_PAN_EXAMPLE` | KYC hint text |
| `APP_KYC_RULES` | Pipe-separated KYC rules |
| `APP_LIMITS_WARNING` | KYC limit warning |

#### 3.1.4 SQLite Setup

Create the SQLite file if it does not exist:

```bash
type nul > database\database.sqlite
```

Update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\xampp\htdocs\mcx_admin\database\database.sqlite
```

#### 3.1.5 Migrations and Seeding

```bash
php artisan migrate --force
php artisan db:seed
php artisan db:seed --class=PostSeeder
php artisan storage:link
```

#### 3.1.6 Run the Backend

```bash
php artisan serve
```

Default local API base:

```text
http://127.0.0.1:8000/api/v1
```

### 3.2 API Structure

### API Groups

#### 📊 Market APIs
- `GET /live-rates`

#### 👤 Profile APIs
- `GET /profile`
- `POST /profile/kyc`

#### 🛒 Order APIs
- `GET /orders`
- `POST /orders`
- `GET /trade-history`

#### 📰 Content APIs
- `GET /news`

#### ⚙️ Config APIs
- `GET /auth/settings`

#### 3.2.1 Standard Response Envelope

All implemented APIs use this wrapper:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {},
  "error": null
}
```

#### 3.2.2 `GET /api/v1/live-rates`

| Item | Value |
|---|---|
| Method | `GET` |
| Description | Returns parsed live rates from upstream source or cached fallback |
| Auth | None |
| Query Params | None |
| Headers | `Accept: application/json` recommended |

Sample response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "fetched_at": "2026-04-08T12:00:00+00:00",
    "served_at": "2026-04-08T12:00:01+00:00",
    "is_stale": false,
    "source": "suvidhigold",
    "fallback_reason": null,
    "items": [
      {
        "name": "GOLD MCX",
        "bid": "153680",
        "ask": "153705",
        "high": "153944",
        "low": "153301"
      }
    ]
  },
  "error": null
}
```

#### 3.2.8 `POST /api/v1/orders`

| Item | Value |
|---|---|
| Method | `POST` |
| Description | Creates a real order row in DB |
| Content Type | `application/json` |
| Auth | None |
| Business Rule | User must have `kyc_status = verified` |

Request body:

| Field | Type | Required |
|---|---|---|
| `customer_name` | string | No |
| `customer_phone` | string | No |
| `asset` | string | Yes |
| `side` | string enum `buy/sell` | Yes |
| `order_type` | string enum `market/pending` | Yes |
| `quantity` | numeric | Yes |
| `price` | numeric | Yes |
| `total` | numeric | Yes |
| `notes` | string | No |

Sample request:

```json
{
  "asset": "GOLD MCX",
  "side": "buy",
  "order_type": "market",
  "quantity": 10,
  "price": 153680,
  "total": 1536800
}
```

Success response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "order": {
      "id": 12,
      "user_id": 1,
      "customer_name": "MCX Demo User",
      "customer_phone": "+91 9999999999",
      "asset": "GOLD MCX",
      "side": "buy",
      "order_type": "market",
      "quantity": "10.00",
      "price": "153680.00",
      "total": "1536800.00",
      "status": "pending",
      "notes": null,
      "placed_at": "2026-04-08T12:00:00+00:00"
    },
    "message": "Order placed successfully."
  },
  "error": null
}
```

Blocked by KYC response:

```json
{
  "success": false,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "code": "kyc_required",
    "profile": {
      "is_verified": false,
      "kyc_status": "unverified"
    }
  },
  "error": "KYC verification is required before placing an order."
}
```

#### 3.2.9 `GET /api/v1/trade-history`

| Item | Value |
|---|---|
| Method | `GET` |
| Description | Returns orders where `status = completed` |
| Auth | None |

Sample response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "items": [
      {
        "id": 7,
        "asset": "GOLD MCX",
        "quantity": "10.00",
        "price": "153680.00",
        "total": "1536800.00",
        "status": "completed",
        "executed_at": "2026-04-08T12:00:00+00:00"
      }
    ],
    "message": "Trade history loaded successfully."
  },
  "error": null
}
```

### 3.3 Authentication System

#### 3.3.1 Current State

| Topic | Status |
|---|---|
| Register API | Not implemented |
| Login API | Not implemented |
| OTP API | Not implemented |
| Token issue/storage | Not implemented |
| Middleware-protected client API | Not implemented |

#### 3.3.2 Effective Identity Model

The client app backend behavior relies on:

- `App\Services\LocalAppUserResolver`
- Config values under `config/api.php`
- One demo/local user record resolved by email

This means:

- The client app is operating as one shared local backend user
- KYC and orders are tied to that resolved user
- This is acceptable only for demo/local development

### 3.4 Database Design

#### 3.4.1 Tables

##### `users`

| Field | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `name` | string | required |
| `email` | string | unique |
| `phone` | string nullable | added for app profile |
| `gst_number` | string nullable | KYC field |
| `pan_number` | string nullable | KYC field |
| `pan_image_path` | string nullable | stored file path |
| `aadhaar_number` | string nullable | KYC field |
| `aadhaar_front_image_path` | string nullable | stored file path |
| `aadhaar_back_image_path` | string nullable | stored file path |
| `selfie_image_path` | string nullable | stored file path |
| `selfie_reference` | string nullable | stored filename |
| `kyc_status` | string | default `unverified` |
| `kyc_submitted_at` | timestamp nullable | submission time |
| `kyc_verified_at` | timestamp nullable | verification time |
| `password` | string | hashed |
| `remember_token` | string nullable | Laravel default |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

##### `orders`

| Field | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `user_id` | foreignId nullable | references `users.id`, null on delete |
| `customer_name` | string nullable | snapshot |
| `customer_phone` | string nullable | snapshot |
| `asset` | string | e.g. `GOLD MCX` |
| `side` | string | `buy` or `sell` |
| `order_type` | string | `market` or `pending` |
| `quantity` | decimal(12,2) | |
| `price` | decimal(12,2) | |
| `total` | decimal(14,2) | |
| `status` | string | currently defaults to `pending` |
| `notes` | text nullable | |
| `placed_at` | timestamp | business timestamp |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

##### `posts`

| Field | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `user_id` | foreignId | author |
| `title` | string | |
| `content` | text | |
| `slug` | string unique | |
| `views` | integer | |
| `is_published` | boolean | |
| `published_at` | timestamp nullable | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

#### 3.4.2 Relationships

```text
users (1) --------- (many) orders
users (1) --------- (many) posts
```

#### 3.4.3 ER Diagram

```text
+---------+           +---------+
| users   |           | posts   |
+---------+           +---------+
| id PK   |<--------->| user_id |
| ...     |           | id PK   |
+---------+           +---------+
     |
     |
     v
+---------+
| orders  |
+---------+
| id PK   |
| user_id |
| asset   |
| side    |
| ...     |
+---------+
```

### 3.5 Key Modules

#### 3.5.1 User Management

Current scope:

- Standard Laravel `users` table exists
- No completed admin CRUD workflow for users
- Admin user pages under `/admin/users*` are currently static Blade views
- Client app user context is derived by `LocalAppUserResolver`

#### 3.5.2 KYC System

Flow:

1. Flutter fetches `/api/v1/profile`
2. If `is_verified = false`, order placement is blocked in UI
3. User completes multi-step KYC flow in Flutter
4. Flutter uploads text fields and 4 images to `/api/v1/profile/kyc`
5. Laravel stores files in `storage/app/public/kyc/*`
6. Laravel updates user KYC fields and marks status as `verified`
7. Future order attempts are allowed

Current limitation:

- Verification is auto-approved on submission
- There is no admin review queue, rejection flow, or partial KYC state

#### 3.5.3 Orders

Order creation logic in `OrdersController`:

1. Resolve local app user
2. Check `kyc_status`
3. Validate request payload
4. Insert order with `status = pending`
5. Return normalized JSON response

Admin consumption:

- Dashboard shows recent orders and status counts
- Admin order list pages read directly from `orders`

#### 3.5.4 News / Posts

Posts serve as a lightweight news feed:

- Stored in `posts`
- Exposed through `/api/v1/news`
- Consumed by Flutter `NewsScreen`

#### 3.5.5 Live Rates

Core classes:

| Class | Responsibility |
|---|---|
| `UpstreamLiveRatesClient` | Fetch raw upstream payload |
| `LiveRatesParser` | Parse upstream format into internal item structure |
| `LiveRatesResponseFormatter` | Shape response payload |
| `LiveRatesService` | Orchestrate fetch, cache, fallback, and logging |

Behavior:

- Tries live upstream first
- Stores fresh payload in cache
- If upstream fails, serves latest cached snapshot if available
- Returns hard failure only when both upstream and cache are unavailable

#### 3.5.6 Complaints / Features

Current repository state:

- No complaints module
- No voting module
- No dispute/resolution workflow

These should be treated as not implemented, not undocumented.

### 3.6 Important Files

#### Controllers

| File | Purpose |
|---|---|
| `app/Http/Controllers/Api/V1/LiveRatesController.php` | Live rates API entrypoint |
| `app/Http/Controllers/Api/V1/NewsController.php` | Published news feed |
| `app/Http/Controllers/Api/V1/AuthSettingsController.php` | Mobile app support/KYC text config |
| `app/Http/Controllers/Api/V1/ProfileController.php` | Returns current local profile and KYC status |
| `app/Http/Controllers/Api/V1/KycController.php` | Handles KYC multipart submission |
| `app/Http/Controllers/Api/V1/OrdersController.php` | Lists and creates orders |
| `app/Http/Controllers/Api/V1/TradeHistoryController.php` | Returns completed orders |
| `app/Http/Controllers/Admin/DashboardController.php` | Dashboard counts and latest orders |
| `app/Http/Controllers/Admin/OrderPagesController.php` | Admin order pages |

#### Models

| File | Purpose |
|---|---|
| `app/Models/User.php` | Application user + KYC state |
| `app/Models/Order.php` | Persisted trading orders |
| `app/Models/Post.php` | News content source |

#### Services

| File | Purpose |
|---|---|
| `app/Services/LocalAppUserResolver.php` | Resolves or creates the demo/local app user |
| `app/Services/LiveRates/LiveRatesService.php` | Live rate orchestration |
| `app/Services/LiveRates/UpstreamLiveRatesClient.php` | Upstream rate fetcher |
| `app/Services/LiveRates/LiveRatesParser.php` | Raw payload parser |
| `app/Services/LiveRates/LiveRatesResponseFormatter.php` | API payload formatter |

#### Support and Routing

| File | Purpose |
|---|---|
| `app/Support/ApiResponse.php` | Standard API envelope |
| `routes/api.php` | All mobile API routes |
| `routes/web.php` | Admin panel routes |
| `config/api.php` | Support copy, KYC config, demo profile config |

## ⚠️ Developer Notes

- Authentication is currently mocked (NOT production ready)
- KYC is auto-approved (no manual verification)
- Orders are not linked to a real trading execution system
- Admin panel has no authentication (for demo only)

## 🐞 Common Issues

### Live rates not working
- Check `LIVE_RATES_URL` in `.env`
- Ensure the backend server has internet access
- Verify the live rate service is not blocked by a firewall

### KYC images not saving
- Run: `php artisan storage:link`
- Check `storage/app/public` write permissions
- Confirm `FILESYSTEM_DISK=local` or proper disk config in `.env`

### Flutter API not working
- Ensure correct `API_BASE_URL` in `lib/core/config/app_config.dart`
- Use IP instead of `localhost` for a real device
- Confirm the backend is running on the same network as the device/emulator

## 📌 Document Info

Version: 1.0  
Last Updated: April 2026  
Maintained By: Backend + Flutter Team

## 4. Flutter Documentation

### 4.1 Setup

#### 4.1.1 Prerequisites

| Requirement | Value |
|---|---|
| Flutter channel | Stable |
| Dart SDK constraint | `^3.11.1` |
| Package name | `mcx` |

#### 4.1.2 Install Dependencies

```bash
cd C:\flutter-project\mcx_app
flutter pub get
```

#### 4.1.3 Run the App

```bash
flutter run --dart-define=API_BASE_URL=http://127.0.0.1:8000/api/v1
```

For Android emulator, `10.0.2.2` may be required:

```bash
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

### 4.2 Project Structure

#### 4.2.1 High-Level Structure

| Path | Responsibility |
|---|---|
| `lib/core` | Config, constants, theme, shared widgets |
| `lib/features/shared` | Generic API envelope parsing and reusable service helpers |
| `lib/features/rates` | Live rates models, controller, screens |
| `lib/features/trade` | Trade entry, confirm, success, history |
| `lib/features/orders` | Orders list, order API service, local sync store |
| `lib/features/profile` | Profile model/service/screen |
| `lib/features/kyc` | KYC upload service and multi-step UI |
| `lib/features/auth` | Splash/login/signup |

#### 4.2.2 Separation of Concerns

| Layer | Examples |
|---|---|
| Presentation | `trade_screen.dart`, `orders_screen.dart`, `kyc_flow_screen.dart` |
| Services | `order_api_service.dart`, `profile_api_service.dart`, `kyc_api_service.dart` |
| Models | `live_rate.dart`, `app_profile.dart`, `local_order.dart` |
| UI State | `LiveRatesController`, `OrderSyncStore` |

### 4.3 API Integration

#### 4.3.1 HTTP Stack

The app uses the Dart `http` package, not Dio, GetConnect, or Retrofit.

#### 4.3.2 Base URL Handling

Base URL is defined in:

- `lib/core/config/app_config.dart`

Current behavior:

```dart
static const String apiBaseUrl = String.fromEnvironment(
  'API_BASE_URL',
  defaultValue: 'http://127.0.0.1:8000/api/v1',
);
```

#### 4.3.3 Token Storage

Current state:

- No secure token storage
- No SharedPreferences token persistence
- No auth token is currently required by the backend

#### 4.3.4 Shared API Envelope Parsing

Core parsing classes:

| File | Purpose |
|---|---|
| `lib/features/shared/data/models/api_response.dart` | Standard envelope parser |
| `lib/features/shared/data/services/simple_api_service.dart` | Generic GET helpers for envelope-based endpoints |

### 4.4 State Management

Current approach:

| Area | Approach |
|---|---|
| Live rates | `ChangeNotifier` via `LiveRatesController` |
| Instant order reflection | `ChangeNotifier` singleton via `OrderSyncStore` |
| Screen-local async state | `FutureBuilder`, local `setState` |

Data flow example:

```text
UI action
  -> service call
  -> parse response into model
  -> setState / notifyListeners
  -> widget rebuild
```

This is a lightweight architecture. Provider, Bloc, Riverpod, or GetX are not currently used.

### 4.5 Screens Explanation

#### 4.5.1 Splash Screen

| Item | Details |
|---|---|
| File | `lib/features/auth/presentation/screens/splash_screen.dart` |
| Purpose | 2-second branded launch screen |
| API | None |
| Navigation | Pushes to `LoginScreen` |

#### 4.5.2 Login Screen

| Item | Details |
|---|---|
| File | `lib/features/auth/presentation/screens/login_screen.dart` |
| Purpose | Demo/local phone + OTP entry |
| API | None |
| Behavior | Accepts any phone, then any OTP |
| Navigation | Continues to `/home` |

Important note:

- This is not connected to backend authentication

#### 4.5.3 Live Rates Screen

| Item | Details |
|---|---|
| File | `lib/features/rates/presentation/screens/live_rates_screen.dart` |
| Purpose | Show live price cards, product rows, trend tab |
| API | `GET /api/v1/live-rates` |
| State | `LiveRatesController` with 5-second polling |
| Error UX | Loading spinner, retry state, stale/fallback banners |

#### 4.5.4 Trade Screen

| Item | Details |
|---|---|
| File | `lib/features/trade/presentation/screens/trade_screen.dart` |
| Purpose | Main trading UI for asset selection, chart, quantity, mode switching |
| APIs | `GET /api/v1/live-rates`, `GET /api/v1/profile` |
| Key Logic | Blocks order placement unless profile is verified |
| Navigation | Unverified users are routed to KYC, verified users to confirmation |

#### 4.5.5 KYC Flow Screen

| Item | Details |
|---|---|
| File | `lib/features/kyc/presentation/screens/kyc_flow_screen.dart` |
| Purpose | Multi-step KYC workflow |
| API | `POST /api/v1/profile/kyc` |
| Steps | Intro, details, PAN, Aadhaar, selfie, review |
| Media | Gallery for PAN/Aadhaar, camera for selfie |

#### 4.5.6 Order Confirmation Screen

| Item | Details |
|---|---|
| File | `lib/features/trade/presentation/screens/order_confirmation_screen.dart` |
| Purpose | Final confirmation before real order submission |
| API | `POST /api/v1/orders` |
| UX | Submission loader, retry error state, success navigation |
| State Sync | Stores successful backend response in `OrderSyncStore` |

#### 4.5.7 Order Success Screen

| Item | Details |
|---|---|
| File | `lib/features/trade/presentation/screens/order_success_screen.dart` |
| Purpose | Displays successful order placement summary |
| API | None |
| Input | Receives order metadata from confirmation screen |

#### 4.5.8 Orders Screen

| Item | Details |
|---|---|
| File | `lib/features/orders/presentation/screens/orders_screen.dart` |
| Purpose | Show immediate local recent orders and backend order list |
| API | `GET /api/v1/orders` |
| Sync Strategy | Hybrid: `OrderSyncStore` + backend fetch |

#### 4.5.9 Trade History Screen

| Item | Details |
|---|---|
| File | `lib/features/trade/presentation/screens/trade_history_screen.dart` |
| Purpose | Show completed trades |
| API | `GET /api/v1/trade-history` |

#### 4.5.10 Profile Screen

| Item | Details |
|---|---|
| File | `lib/features/profile/presentation/screens/profile_screen.dart` |
| Purpose | Show profile shell and API-backed user basics |
| API | `GET /api/v1/profile` |
| Limitations | Wallet, banking, password actions are still placeholders |

#### 4.5.11 News Screen

| Item | Details |
|---|---|
| File | `lib/features/news/presentation/screens/news_screen.dart` |
| Purpose | Render published backend posts |
| API | `GET /api/v1/news` |

#### 4.5.12 Messages Screen

| Item | Details |
|---|---|
| File | `lib/features/messages/presentation/screens/messages_screen.dart` |
| Purpose | Static information page |
| API | None |
| Status | Still static content |

#### 4.5.13 Menu Screen

| Item | Details |
|---|---|
| File | `lib/features/menu/presentation/screens/menu_screen.dart` |
| Purpose | Slide-out navigation hub |
| API | None directly |
| Navigation | Routes to orders, trade history, profile, news, contact, bank details |

### 4.6 Error Handling

#### 4.6.1 API Failure Handling

Implemented patterns:

| Pattern | Where Used |
|---|---|
| `ApiLoadingView` | Orders, profile, trade history, news, order confirm |
| `ApiErrorView` | Orders, profile, trade history, news, order confirm |
| `ApiEmptyView` | Orders, trade history, news |
| Thrown service exceptions | Live rates, order submit, KYC submit |

#### 4.6.2 User Feedback

| Scenario | UX |
|---|---|
| Unverified user tries to place order | Snack bar + redirect into KYC flow |
| Order API fails | Inline error block with retry |
| Live rates fetch fails but cache exists | Warning banner with stale/cached messaging |
| No data available | Empty-state widget |

## 5. Admin Panel Documentation

### 5.1 Current Feature Set

| Section | Route | Status |
|---|---|---|
| Dashboard | `/admin/dashboard` | Dynamic |
| All Orders | `/admin/orders` | Dynamic |
| Pending Orders | `/admin/orders/pending` | Dynamic |
| Completed Orders | `/admin/orders/completed` | Dynamic |
| Order Detail | `/admin/orders/{id}` | Dynamic |
| User Pages | `/admin/users*` | Static placeholder views |
| Gold Rate | `/admin/rates/gold` | Static page |
| Silver Rate | `/admin/rates/silver` | Static page |
| Reports History | `/admin/reports/history` | Static page |
| Settings | `/admin/settings` | Static page |
| Login | `/admin/login` | Static page |

### 5.2 Role Permissions

Current implementation:

- No backend admin authentication enforcement
- No role-based access control
- No permission tables, policies, or middleware

Therefore, the admin panel is currently a UI shell with dynamic order reading, not a secured production admin system.

### 5.3 Dashboard Logic

`DashboardController` provides:

- `recentOrders`: latest 5 orders ordered by `placed_at` then `id`
- `pendingOrdersCount`: count where `status = pending`
- `completedOrdersCount`: count where `status = completed`

### 5.4 Important Admin Workflows

#### Order Monitoring

1. Client submits order via app
2. Order stored in `orders`
3. Admin opens `/admin/orders`
4. Order appears in list immediately on refresh/page load

#### Dashboard Monitoring

1. Admin opens `/admin/dashboard`
2. Latest orders and status counts are read from DB

Current limitation:

- No order-status update UI is present in the repository
- Completed orders must currently be marked outside the existing UI or by custom DB changes/admin extension

## 6. Complete Flow Examples

### 6.1 Client Login -> KYC -> Order

1. User opens app
2. Splash screen routes to login
3. User enters any phone number
4. User taps `Send OTP`
5. User enters any OTP
6. App navigates to home
7. User opens trade screen
8. App loads live rates and profile from backend
9. If `profile.is_verified == false`, order submission is blocked
10. User is pushed into KYC multi-step flow
11. User enters details and uploads PAN, Aadhaar front/back, and selfie
12. Flutter submits multipart request to `/api/v1/profile/kyc`
13. Backend stores files, updates user KYC status to `verified`
14. User returns to trade flow
15. User confirms order
16. Flutter posts JSON to `/api/v1/orders`
17. Backend inserts row in `orders`
18. App shows success screen
19. Orders screen shows immediate local reflection and backend truth on reload
20. Admin panel can view the new order under `/admin/orders`

### 6.2 News Publishing -> App Consumption

1. Seed or insert published rows in `posts`
2. Client app requests `/api/v1/news`
3. API returns published posts ordered by `published_at DESC, id DESC`
4. Flutter renders cards in `NewsScreen`

### 6.3 Trade History Flow

1. Orders exist in DB
2. Order status is set to `completed`
3. Client app requests `/api/v1/trade-history`
4. API filters completed orders
5. Flutter renders history cards

### 6.4 Complaint Creation -> Voting -> Resolution

Current repository state:

- This flow does not exist
- No complaint, voting, or resolution models/routes/screens are implemented

## 7. Security Practices

### 7.1 Implemented

| Area | Current Practice |
|---|---|
| Password hashing | Laravel hashed cast on `User::password` |
| Validation | Request validation in `OrdersController` and `KycController` |
| File validation | KYC images validated as `image` with max 5 MB |
| SQL safety | Eloquent ORM and query builder |
| Response consistency | Standard API response envelope for main API endpoints |
| Rate limiting | Live rates endpoint uses `throttle:live-rates` |

### 7.2 Not Yet Implemented

| Gap | Impact |
|---|---|
| Real client authentication | Any app user can enter app locally |
| Auth middleware for API | No identity isolation between users |
| Role-based admin access | Admin panel is not protected by real authorization |
| CSRF/auth hardening for app APIs | Not relevant in same way for public JSON APIs, but auth is still missing |
| KYC admin review | KYC is auto-verified on submit |
| Antivirus/file scanning | Uploaded documents are not scanned |
| Audit logs | No full compliance-grade audit trail |

### 7.3 Security Recommendation

Before production, implement:

1. Sanctum or JWT-based mobile auth
2. Real OTP provider or password login
3. Admin authentication and RBAC
4. KYC review states such as `submitted`, `pending_review`, `verified`, `rejected`
5. Signed upload policies or private document storage

## 8. Deployment Guide

### 8.1 Backend Deployment

#### Server Requirements

- PHP 8.2+
- Composer
- Web server such as Apache or Nginx
- Writable `storage` and `bootstrap/cache`
- Database server or SQLite file path

#### Deployment Steps

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Production considerations:

- Change `APP_ENV=production`
- Set `APP_DEBUG=false`
- Replace SQLite with MySQL/PostgreSQL for multi-user reliability
- Configure queue worker if async workflows are introduced later

### 8.2 Flutter Build

#### Android APK

```bash
flutter build apk --release --dart-define=API_BASE_URL=https://your-domain.com/api/v1
```

#### Android App Bundle

```bash
flutter build appbundle --release --dart-define=API_BASE_URL=https://your-domain.com/api/v1
```

#### iOS

```bash
flutter build ipa --release --dart-define=API_BASE_URL=https://your-domain.com/api/v1
```

### 8.3 Environment Configuration

Backend and app must agree on:

| Concern | Backend | Flutter |
|---|---|---|
| API base URL | `APP_URL` / route host | `API_BASE_URL` dart-define |
| File URLs | `public/storage` available | app consumes returned URLs |
| KYC support text | `.env` config values | pulled from `/auth/settings` |

## 9. Future Improvements

### 9.1 Scalability

1. Replace local demo identity with real auth and user-specific orders
2. Move order polling and updates to websocket or push-based sync
3. Use queue jobs for heavy KYC processing and notifications
4. Separate admin and mobile API concerns into versioned modules

### 9.2 Performance

1. Introduce pagination for orders, trade history, and news
2. Cache admin dashboard aggregates
3. Add thumbnail generation and compressed storage for KYC images
4. Add repository/service boundaries around orders and KYC for easier testing

### 9.3 Product Maturity

1. Implement admin review workflow for KYC
2. Add order status transition management in admin UI
3. Add wallet/funds sync
4. Replace static message/bank/contact pages with backend-managed content
5. Introduce a real Professional App if that role is required

### 9.4 Suggested Verification-State Design

Recommended future `kyc_status` values:

- `unverified`
- `submitted`
- `pending_review`
- `verified`
- `rejected`

Recommended companion fields:

- `kyc_rejected_reason`
- `kyc_reviewed_by`
- `kyc_reviewed_at`
- `kyc_submission_version`

## 10. Developer Notes and Known Gaps

### 10.1 What Is Real Today

- Orders are persisted in DB
- Admin order pages read real orders
- Flutter KYC uploads real files to backend
- Backend KYC status gates order placement
- Live rates, news, orders, profile, and trade history APIs are functional

### 10.2 What Is Still Demo or Placeholder

- Login/OTP
- Admin authentication
- User management admin pages
- Wallet/bank sync
- Some informational screens in Flutter such as Messages
- Professional App
- Complaints/voting module

### 10.3 Recommended First Steps for a New Developer

1. Run backend migrations and seeders
2. Start Laravel locally on port 8000
3. Run Flutter with `API_BASE_URL` pointing to the backend
4. Verify `/api/v1/live-rates` and `/api/v1/profile`
5. Complete KYC in app
6. Place a test order
7. Open `/admin/orders` and confirm the order row exists

Error case:

```json
{
  "success": false,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "items": [],
    "source": "suvidhigold"
  },
  "error": "Live rates unavailable and no cached data exists yet."
}
```

#### 3.2.3 `GET /api/v1/news`

| Item | Value |
|---|---|
| Method | `GET` |
| Description | Returns published posts from `posts` table |
| Auth | None |
| Query Params | None |

Sample response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "items": [
      {
        "id": 1,
        "title": "Why I Spent 6 Hours Debugging a Missing Semicolon",
        "content": "It was a dark and stormy night...",
        "slug": "why-i-spent-6-hours-debugging-a-missing-semicolon",
        "views": 1240,
        "published_at": "2026-04-01T10:00:00+00:00"
      }
    ]
  },
  "error": null
}
```

#### 3.2.4 `GET /api/v1/auth/settings`

| Item | Value |
|---|---|
| Method | `GET` |
| Description | Returns app mode, support messaging, and KYC copy/config |
| Auth | None |

Sample response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "otp_enabled": false,
    "message": "Local app mode is enabled. Enter any details to continue.",
    "support": {
      "phone": "+91 8154995995",
      "message": "Support is available during market hours.",
      "announcement": ""
    },
    "kyc": {
      "pan_example": "ABCDE1234F",
      "rules": [
        "PAN must belong to the account holder",
        "Name must match PAN records"
      ],
      "limits_warning": "Trading limits may change based on KYC verification status."
    }
  },
  "error": null
}
```

#### 3.2.5 `GET /api/v1/profile`

| Item | Value |
|---|---|
| Method | `GET` |
| Description | Returns demo/local user profile and backend KYC status |
| Auth | None |

Sample response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "profile": {
      "name": "MCX Demo User",
      "email": "demo@example.com",
      "phone": "+91 9999999999",
      "gst_number": null,
      "pan_number": null,
      "aadhaar_number": null,
      "kyc_status": "unverified",
      "is_verified": false,
      "kyc_verified_at": null
    },
    "auth": {
      "type": "local",
      "guard": "none"
    }
  },
  "error": null
}
```

#### 3.2.6 `POST /api/v1/profile/kyc`

| Item | Value |
|---|---|
| Method | `POST` |
| Description | Submits full KYC and marks the local user as verified |
| Content Type | `multipart/form-data` |
| Auth | None |

Request fields:

| Field | Type | Required |
|---|---|---|
| `name` | string | Yes |
| `email` | string | Yes |
| `phone` | string | Yes |
| `gst_number` | string | No |
| `pan_number` | string | Yes |
| `aadhaar_number` | string | Yes |
| `pan_image` | image | Yes |
| `aadhaar_front_image` | image | Yes |
| `aadhaar_back_image` | image | Yes |
| `selfie_image` | image | Yes |

Example `curl`:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/profile/kyc ^
  -H "Accept: application/json" ^
  -F "name=MCX Demo User" ^
  -F "email=demo@example.com" ^
  -F "phone=+919999999999" ^
  -F "pan_number=ABCDE1234F" ^
  -F "aadhaar_number=123412341234" ^
  -F "pan_image=@pan.jpg" ^
  -F "aadhaar_front_image=@aadhaar_front.jpg" ^
  -F "aadhaar_back_image=@aadhaar_back.jpg" ^
  -F "selfie_image=@selfie.jpg"
```

Success response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "profile": {
      "name": "MCX Demo User",
      "email": "demo@example.com",
      "phone": "+919999999999",
      "is_verified": true,
      "kyc_status": "verified",
      "pan_number": "ABCDE1234F",
      "pan_image_url": "/storage/kyc/pan/pan.jpg",
      "aadhaar_number": "123412341234",
      "aadhaar_front_image_url": "/storage/kyc/aadhaar-front/front.jpg",
      "aadhaar_back_image_url": "/storage/kyc/aadhaar-back/back.jpg",
      "selfie_image_url": "/storage/kyc/selfie/selfie.jpg",
      "gst_number": null,
      "kyc_verified_at": "2026-04-08T12:00:00+00:00"
    },
    "message": "KYC submitted and verified successfully."
  },
  "error": null
}
```

Validation error response example:

```json
{
  "message": "The pan image field is required.",
  "errors": {
    "pan_image": [
      "The pan image field is required."
    ]
  }
}
```

Note:

- Laravel validation errors do not currently use the custom `ApiResponse` envelope

#### 3.2.7 `GET /api/v1/orders`

| Item | Value |
|---|---|
| Method | `GET` |
| Description | Returns all orders from the `orders` table |
| Auth | None |

Sample response:

```json
{
  "success": true,
  "version": "v1",
  "timestamp": "2026-04-08T12:00:00+00:00",
  "data": {
    "items": [
      {
        "id": 1,
        "user_id": 1,
        "customer_name": "MCX Demo User",
        "customer_phone": "+91 9999999999",
        "asset": "GOLD MCX",
        "side": "buy",
        "order_type": "market",
        "quantity": "10.00",
        "price": "153680.00",
        "total": "1536800.00",
        "status": "pending",
        "notes": null,
        "placed_at": "2026-04-08T12:00:00+00:00"
      }
    ],
    "message": "Orders loaded successfully."
  },
  "error": null
}
```
