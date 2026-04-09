# MCX Trading System - Phase 2: KYC & Order Management

## Overview

This phase adds complete KYC verification workflow and trading permission system. The system does NOT handle payments or wallets—all deposits are managed offline by admin.

---

## 🎯 New Features Implemented

### 1. User Trading Fields
- `is_verified` (boolean, default: false) — User is KYC approved
- `can_trade` (boolean, default: false) — User is allowed to trade (after offline deposit)

### 2. KYC Request System
- Users submit KYC with: Name, PAN, Aadhaar
- Stored in `kyc_requests` table with `pending/approved/rejected` status
- Admin approves/rejects, automatically updates `users.is_verified`

### 3. Order Types
- **Market Order**: Immediately moves to `pending` status
- **Limit Order**: Starts as `waiting`, moves to `pending` when price condition matches

### 4. Order States
- `waiting` — Limit order waiting for price trigger
- `pending` — Awaiting admin approval
- `approved` — Admin approved
- `rejected` — Admin rejected

### 5. Price Check Engine
- Runs as scheduled command every minute
- Checks all `waiting` limit orders
- Compares against live market prices
- Moves to `pending` when condition matches

### 6. Admin Panel
- KYC requests approval/rejection
- Enable trading permission for verified users
- Order approval/rejection
- Order history with status filtering

---

## 📦 Database Migrations

Run migrations:

```bash
php artisan migrate
```

### New Tables/Fields

#### Users Table Updates
```sql
ALTER TABLE users ADD COLUMN is_verified BOOLEAN DEFAULT 0;
ALTER TABLE users ADD COLUMN can_trade BOOLEAN DEFAULT 0;
```

#### KYC Requests Table
```sql
CREATE TABLE kyc_requests (
  id BIGINT PRIMARY KEY,
  user_id BIGINT NOT NULL FOREIGN KEY,
  name VARCHAR(255),
  pan VARCHAR(20),
  aadhaar VARCHAR(20),
  status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  approved_by BIGINT NULLABLE FOREIGN KEY,
  approved_at TIMESTAMP NULLABLE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

#### Orders Table Updates
```sql
ALTER TABLE orders ADD COLUMN type ENUM('market', 'limit') DEFAULT 'market';
ALTER TABLE orders ADD COLUMN target_price DECIMAL(12, 2) NULLABLE;
ALTER TABLE orders MODIFY COLUMN status ENUM('waiting', 'pending', 'approved', 'rejected');
ALTER TABLE orders ADD COLUMN approved_by BIGINT NULLABLE FOREIGN KEY;
ALTER TABLE orders ADD COLUMN approved_at TIMESTAMP NULLABLE;
```

---

## 🔌 API Endpoints

### Submit KYC Request
**POST** `/api/v1/kyc`

Request Body:
```json
{
  "name": "John Doe",
  "pan": "ABCDE1234F",
  "aadhaar": "123456789012"
}
```

Response:
```json
{
  "success": true,
  "data": {
    "kyc_request": {
      "id": 1,
      "name": "John Doe",
      "pan": "ABCDE1234F",
      "aadhaar": "123456789012",
      "status": "pending",
      "created_at": "2026-04-08T12:00:00Z"
    },
    "message": "KYC request submitted successfully."
  }
}
```

### Get Profile & KYC Status
**GET** `/api/v1/profile`

Response:
```json
{
  "success": true,
  "data": {
    "profile": {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+919999999999",
      "is_verified": false,
      "can_trade": false,
      "kyc_request": {
        "id": 1,
        "name": "John Doe",
        "status": "pending",
        "created_at": "2026-04-08T12:00:00Z"
      }
    }
  }
}
```

### Place Order
**POST** `/api/v1/orders`

Request Body (Market Order):
```json
{
  "asset_name": "GOLD",
  "type": "market",
  "price": 60000
}
```

Request Body (Limit Order):
```json
{
  "asset_name": "GOLD",
  "type": "limit",
  "price": 60000,
  "target_price": 59500
}
```

Response:
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 1,
      "user_id": 1,
      "asset_name": "GOLD",
      "type": "limit",
      "price": "60000.00",
      "target_price": "59500.00",
      "status": "waiting",
      "placed_at": "2026-04-08T12:00:00Z"
    },
    "message": "Order placed successfully."
  }
}
```

Error if KYC not verified:
```json
{
  "success": false,
  "error": "KYC verification is required before placing an order.",
  "data": {
    "code": "kyc_required",
    "profile": {
      "is_verified": false
    }
  }
}
```

Error if trading not enabled:
```json
{
  "success": false,
  "error": "Trading is not enabled for your account.",
  "data": {
    "code": "trading_disabled"
  }
}
```

---

## 🛠️ Admin Panel Routes

### KYC Management
- **GET** `/admin/users/requests` — List pending KYC requests
- **POST** `/admin/kyc/{id}/approve` — Approve KYC request
- **POST** `/admin/kyc/{id}/reject` — Reject KYC request

### User Trading Control
- **POST** `/admin/users/{id}/enable-trading` — Enable trading for verified user

### Orders
- **GET** `/admin/orders` — All orders
- **GET** `/admin/orders/pending` — Pending approval orders
- **GET** `/admin/orders/completed` — Completed orders
- **POST** `/admin/orders/{id}/approve` — Approve order
- **POST** `/admin/orders/{id}/reject` — Reject order

---

## ⏱️ Scheduled Task: Price Check Engine

The system checks waiting limit orders every minute.

### Run Command Manually
```bash
php artisan app:check-waiting-orders
```

### Setup Cron Job (Linux/macOS)
Add to crontab:
```
* * * * * cd /path/to/mcx_admin && php artisan schedule:run >> /dev/null 2>&1
```

### Setup Scheduler (Windows Task Scheduler)
Create a task that runs:
```
php C:\xampp\htdocs\mcx_admin\artisan schedule:run
```
Every minute.

### How It Works
1. Fetches live rates from upstream API
2. Queries all orders with `status = waiting`
3. For each order: checks if `market_price <= target_price`
4. Updates status to `pending` if condition matches
5. Logs all changes

---

## 📋 Workflow Examples

### Complete KYC Flow
```
1. User submits KYC (name, pan, aadhaar) via POST /api/v1/kyc
2. KYC request created with status = "pending"
3. Admin reviews at GET /admin/users/requests
4. Admin clicks "Approve"
5. KycRequest.status = "approved" + User.is_verified = true
6. User can now place orders
```

### Offline Payment & Trading Flow
```
1. User deposits INR 100,000 offline (bank transfer, etc.)
2. Admin verifies deposit in bank
3. Admin enables trading: POST /admin/users/{id}/enable-trading
4. User.can_trade = true
5. User can now place market AND limit orders
```

### Limit Order Execution Flow
```
1. User places limit order: price=60000, target_price=59500, type=limit
2. Order status = "waiting"
3. Scheduler runs every minute, checks live prices
4. When market price <= 59500, order moves to status = "pending"
5. Admin sees order in /admin/orders/pending
6. Admin approves/rejects order
7. Order finalized
```

---

## 🔒 Validation & Business Rules

### User Prerequisites
- ✅ `is_verified = true` (KYC approved)
- ✅ `can_trade = true` (offline deposit received)
- ❌ Will reject order if either is false

### Order Validation
- ✅ Market orders: `type` and `price` required
- ✅ Limit orders: `type`, `price`, and `target_price` required
- ❌ Returns 400 if target_price missing for limit orders

### KYC Validation
- ✅ Duplicate pending/approved requests blocked
- ❌ User can only have one active KYC at a time

---

## 📚 File Structure

### Models
- `app/Models/KycRequest.php` — KYC request model
- `app/Models/User.php` — (updated) added relationships
- `app/Models/Order.php` — (updated) added new fields

### Controllers
- `app/Http/Controllers/Admin/KycRequestsController.php` — Admin KYC actions
- `app/Http/Controllers/Admin/UsersController.php` — Enable trading
- `app/Http/Controllers/Admin/OrderPagesController.php` — (updated) order approval
- `app/Http/Controllers/Api/V1/KycController.php` — (updated) new KYC flow
- `app/Http/Controllers/Api/V1/OrdersController.php` — (updated) new order types

### Services
- `app/Services/PriceCheckService.php` — Price checking logic
- `app/Console/Commands/CheckWaitingOrders.php` — Scheduler command

### Views
- `resources/views/admin/kyc/requests.blade.php` — KYC management panel
- `resources/views/admin/orders/index.blade.php` — (updated)
- `resources/views/admin/orders/pending.blade.php` — (updated)

### Routes
- `routes/web.php` — (updated) new admin routes
- `routes/api.php` — (unchanged) existing API routes still work

---

## 🧪 Testing

### Test KYC Flow
```bash
# 1. Submit KYC
curl -X POST http://localhost:8000/api/v1/kyc \
  -H "Content-Type: application/json" \
  -d '{"name":"John","pan":"ABCDE1234F","aadhaar":"123456789012"}'

# 2. Check profile
curl http://localhost:8000/api/v1/profile
```

### Test Order Placement
```bash
# 1. Place market order (will fail if not verified)
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{"asset_name":"GOLD","type":"market","price":60000}'

# 2. Place limit order
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{"asset_name":"GOLD","type":"limit","price":60000,"target_price":59500}'
```

### Test Price Check
```bash
# Run manually
php artisan app:check-waiting-orders

# View logs
tail -f storage/logs/laravel.log
```

---

## ⚠️ Important Notes

### No Real Payments
- This system does NOT connect to payment gateways
- Deposits are verified offline by admin
- No wallet or balance tracking

### KYC Auto-Verification
- For MVP, KYC is instantly approved by admin
- Production would need manual review with documents

### Scheduled Task
- Ensure cron/scheduler is running for price checks
- Without it, limit orders will never auto-trigger
- Test with `php artisan app:check-waiting-orders`

### Admin Access
- Currently no authentication for admin panel
- Add auth middleware for production:
  ```php
  Route::middleware('auth')->prefix('admin')->group(...)
  ```

---

## 🚀 Deployment Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Set up scheduler cron job
- [ ] Test KYC flow via API
- [ ] Test order placement
- [ ] Test admin panel KYC approvals
- [ ] Test order approvals
- [ ] Test limit order auto-trigger
- [ ] Set up logging for price checks
- [ ] Add authentication middleware for admin panel
- [ ] Configure error notifications

---

## 📞 Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Test scheduler: `php artisan app:check-waiting-orders`
3. Verify database migrations
4. Check API responses with curl/Postman
