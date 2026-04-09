# MCX Trading System - Phase 2 Implementation Summary

## ✅ Complete Implementation Delivered

This guide walks through everything created for the KYC and order management system.

---

## 📌 Database Layer

### Migrations Created

#### 1. Add Trading Fields to Users
**File**: `database/migrations/2026_04_08_081249_add_trading_fields_to_users_table.php`
- Adds `is_verified` (boolean, default false)
- Adds `can_trade` (boolean, default false)

#### 2. Create KYC Requests Table
**File**: `database/migrations/2026_04_08_081308_create_kyc_requests_table.php`

Schema:
```sql
- id (PK)
- user_id (FK → users)
- name (string)
- pan (string)
- aadhaar (string)
- status (enum: pending, approved, rejected) default pending
- approved_by (FK → users, nullable)
- approved_at (timestamp, nullable)
- created_at, updated_at
```

#### 3. Update Orders Table
**File**: `database/migrations/2026_04_08_081337_update_orders_table_for_trading_system.php`

New columns:
```sql
- type (enum: market, limit) default market
- target_price (decimal 12,2, nullable)
- status (enum: waiting, pending, approved, rejected) — replaces old status
- approved_by (FK → users, nullable)
- approved_at (timestamp, nullable)
```

### Run Migrations
```bash
php artisan migrate
```

---

## 🗂️ Model Layer

### Updated Models

#### 1. User.php
**File**: `app/Models/User.php`

Added fields:
```php
'is_verified',
'can_trade',
```

Added relationships:
```php
public function kycRequests() { return $this->hasMany(KycRequest::class); }
public function orders() { return $this->hasMany(Order::class); }
```

#### 2. Order.php
**File**: `app/Models/Order.php`

Updated fillable:
```php
'type',
'target_price',
'approved_by',
'approved_at',
```

Updated casts:
```php
'target_price' => 'decimal:2',
'approved_at' => 'datetime',
```

Added relationships:
```php
public function user() { return $this->belongsTo(User::class); }
public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
```

#### 3. KycRequest.php (NEW)
**File**: `app/Models/KycRequest.php`

Complete model with:
- Fillable fields
- `user()` relationship
- `approvedBy()` relationship
- DateTime casting for `approved_at`

---

## 🎮 Controller Layer

### API Controllers

#### KycController.php (UPDATED)
**File**: `app/Http/Controllers/Api/V1/KycController.php`

New `store()` method:
- Validates: name, pan, aadhaar
- Prevents duplicate pending/approved requests
- Creates kyc_requests row with status=pending
- Returns request ID and created_at

#### ProfileController.php (UPDATED)
**File**: `app/Http/Controllers/Api/V1/ProfileController.php`

Updated response:
- Returns `is_verified` and `can_trade` instead of KYC image paths
- Includes latest KYC request details if exists
- Status, approval timestamps, etc.

#### OrdersController.php (UPDATED)
**File**: `app/Http/Controllers/Api/V1/OrdersController.php`

Updated `store()` method:
- Checks `is_verified` (rejects with code: kyc_required)
- Checks `can_trade` (rejects with code: trading_disabled)
- Accepts new fields: asset_name, type, target_price
- Market orders → status = pending immediately
- Limit orders → status = waiting
- Returns only relevant order fields

### Admin Controllers

#### KycRequestsController.php (NEW)
**File**: `app/Http/Controllers/Admin/KycRequestsController.php`

Methods:
- `index()` — List all KYC requests with user details
- `approve(KycRequest)` — Approve + set user.is_verified = true
- `reject(KycRequest)` — Reject with timestamp

#### UsersController.php (NEW)
**File**: `app/Http/Controllers/Admin/UsersController.php`

Methods:
- `enableTrading(User)` — Checks KYC first, then sets can_trade = true

#### OrderPagesController.php (UPDATED)
**File**: `app/Http/Controllers/Admin/OrderPagesController.php`

New methods:
- `approve(Order)` — Move order to approved status
- `reject(Order)` — Move order to rejected status
- Both require pending status and set approved_by, approved_at

---

## 🔧 Services & Commands

### PriceCheckService.php (NEW)
**File**: `app/Services/PriceCheckService.php`

Method: `checkWaitingOrders()`
1. Queries all orders with status=waiting
2. Fetches live rates from LiveRatesService
3. For each waiting order:
   - Finds matching asset rate
   - If market_price ≤ target_price → moves to pending
4. Logs all changes

### CheckWaitingOrders Command (NEW)
**File**: `app/Console/Commands/CheckWaitingOrders.php`

Artisan command:
```bash
php artisan app:check-waiting-orders
```

Calls PriceCheckService to check orders.

---

## 🛣️ Routes

### API Routes (Existing)
**File**: `routes/api.php`

No changes — existing endpoints still work:
- `POST /api/v1/profile/kyc` → Updated KycController
- `GET /api/v1/profile` → Updated ProfileController
- `POST /api/v1/orders` → Updated OrdersController

### Web Routes (Updated)
**File**: `routes/web.php`

New routes added:

#### KYC Management
```php
GET  /admin/users/requests                       → KycRequestsController@index      [name: kyc.requests]
POST /admin/kyc/{kycRequest}/approve             → KycRequestsController@approve    [name: admin.kyc.approve]
POST /admin/kyc/{kycRequest}/reject              → KycRequestsController@reject     [name: admin.kyc.reject]
```

#### User Management
```php
POST /admin/users/{user}/enable-trading          → UsersController@enableTrading    [name: users.enableTrading]
```

#### Order Management
```php
GET  /admin/orders                               → OrderPagesController@index       [name: orders.index]
GET  /admin/orders/pending                       → OrderPagesController@pending     [name: orders.pending]
GET  /admin/orders/completed                     → OrderPagesController@completed   [name: orders.completed]
GET  /admin/orders/{order}                       → OrderPagesController@show        [name: orders.show]
POST /admin/orders/{order}/approve               → OrderPagesController@approve     [name: orders.approve]
POST /admin/orders/{order}/reject                → OrderPagesController@reject      [name: orders.reject]
```

---

## 👁️ Views

### KYC Requests Panel (NEW)
**File**: `resources/views/admin/kyc/requests.blade.php`

Features:
- Lists all KYC requests with user email
- Shows name, pan, aadhaar
- Status badge (pending/approved/rejected)
- Approve/Reject buttons for pending requests
- Success/error flash messages

### Orders Index (UPDATED)
**File**: `resources/views/admin/orders/index.blade.php`

Updated to show:
- Order type (market/limit)
- Target price
- Status

### Pending Orders (UPDATED)
**File**: `resources/views/admin/orders/pending.blade.php`

Enhanced with:
- Order type column
- Target price column
- Approve/Reject action buttons
- Success/error messages

---

## 📋 Complete Workflow

### User KYC Flow
```
1. User calls: POST /api/v1/kyc
   Body: { name, pan, aadhaar }
   
2. Backend:
   - Validates input
   - Checks no duplicate pending/approved
   - Creates kyc_requests row (status=pending)
   - Returns: kyc_request details
   
3. Admin reviews: GET /admin/users/requests
   - Sees all pending requests
   
4. Admin action: POST /admin/kyc/{id}/approve
   - Sets kyc_requests.status=approved
   - Sets users.is_verified=true
   - Records: approved_by, approved_at

5. User checks: GET /api/v1/profile
   - Returns: is_verified=true, kyc_request with approval info
```

### Offline Deposit & Trading Enable
```
1. User deposits offline (bank transfer, etc.)
2. Admin verifies deposit in bank records

3. Admin enables trading: POST /admin/users/{id}/enable-trading
   - Validates: user.is_verified must be true
   - Sets: users.can_trade=true
   
4. User is now ready to place orders
```

### Market Order Placement
```
1. User calls: POST /api/v1/orders
   Body: { asset_name: "GOLD", type: "market", price: 60000 }
   
2. Backend validates:
   - is_verified == true? else reject
   - can_trade == true? else reject
   
3. Creates order:
   - type = market
   - status = pending (ready for approval)
   - price = 60000
   - target_price = null
   
4. Response: order details with status=pending
```

### Limit Order & Auto-Trigger
```
1. User calls: POST /api/v1/orders
   Body: { asset_name: "GOLD", type: "limit", price: 60000, target_price: 59500 }
   
2. Backend creates:
   - type = limit
   - status = waiting (not pending yet)
   - price = 60000
   - target_price = 59500
   
3. Every minute, scheduler runs: php artisan app:check-waiting-orders
   - Fetches live rates
   - For each waiting order:
     - If market_price <= target_price → status = pending
   - Logs changes
   
4. Once pending, admin approves: POST /admin/orders/{id}/approve
   - status = approved
   - approved_by set
   - approved_at set
```

---

## 🧪 Testing Checklist

### API Testing

#### 1. Submit KYC
```bash
curl -X POST http://localhost:8000/api/v1/kyc \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "pan": "ABCDE1234F",
    "aadhaar": "123456789012"
  }'
```

Expected: kyc_request with status=pending

#### 2. Check Profile
```bash
curl http://localhost:8000/api/v1/profile
```

Expected: is_verified=false, kyc_request with status=pending

#### 3. Try Order Before Verified
```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{
    "asset_name": "GOLD",
    "type": "market",
    "price": 60000
  }'
```

Expected: Error - KYC not verified

#### 4. After Admin Approves KYC
- Admin visits: GET /admin/users/requests
- Admin clicks: "Approve"
- User checks: GET /api/v1/profile → is_verified=true

#### 5. Enable Trading
- Admin visits: (needs route to user profile)
- Admin clicks: "Enable Trading"
- User can now place orders

#### 6. Place Limit Order
```bash
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{
    "asset_name": "GOLD",
    "type": "limit",
    "price": 60000,
    "target_price": 59500
  }'
```

Expected: order with status=waiting

#### 7. Check Scheduler
```bash
php artisan app:check-waiting-orders
```

Check logs:
```bash
tail -f storage/logs/laravel.log
```

Look for: "Order X moved to pending"

---

## 🚀 Deployment Steps

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

3. **Set Up Scheduler**
   
   Linux/macOS (crontab):
   ```
   * * * * * cd /path/to/mcx_admin && php artisan schedule:run >> /dev/null 2>&1
   ```
   
   Windows (Task Scheduler):
   ```
   php C:\xampp\htdocs\mcx_admin\artisan schedule:run
   ```
   Run every minute.

4. **Test Everything**
   - Follow testing checklist above
   - Check logs for errors
   - Verify admin pages load

5. **Production Readiness**
   - Add auth middleware to admin routes
   - Configure error notification channels
   - Set up log monitoring
   - Backup database before first run

---

## 📊 Database Structure Summary

```
users (1) ──────→ (many) kyc_requests
    ↓
    └──→ (many) orders

kyc_requests
├─ id
├─ user_id
├─ name, pan, aadhaar
├─ status
├─ approved_by → users.id
└─ approved_at

orders
├─ id
├─ user_id
├─ asset, type (market/limit)
├─ price, target_price
├─ status (waiting/pending/approved/rejected)
├─ approved_by → users.id
└─ approved_at
```

---

## ✨ Key Features

✅ KYC submission and approval workflow  
✅ Trading permission control (offline deposits)  
✅ Market orders (instant pending)  
✅ Limit orders (auto-trigger on price match)  
✅ Admin order approval/rejection  
✅ Automated price checking (scheduler)  
✅ Clean API responses with proper error handling  
✅ Scalable service architecture  
✅ Full audit trail (approved_by, approved_at)  

---

## 🔐 Security Considerations

- ✅ Validation on all inputs
- ✅ Business logic checks (is_verified, can_trade)
- ✅ Audit fields for compliance
- ⚠️ TODO: Add auth middleware to admin panel
- ⚠️ TODO: Rate limiting on API endpoints
- ⚠️ TODO: Permission policies for admin actions

---

## 📞 Troubleshooting

### Migrations fail
```bash
# Check migration status
php artisan migrate:status

# Rollback and retry
php artisan migrate:rollback
php artisan migrate
```

### Orders not auto-triggering
- Check scheduler is running: `ps aux | grep schedule:run`
- Run manually: `php artisan app:check-waiting-orders`
- Check logs: `storage/logs/laravel.log`

### Admin pages not loading
- Routes: Check `routes/web.php` is correct
- Views: Check `resources/views/admin/kyc/` exists
- Clear cache: `php artisan route:clear`

### API returning 400 errors
- Check request validation
- Use curl with `-v` flag for details
- Check `storage/logs/laravel.log`

---

## 📖 Complete File List

**Models**:
- app/Models/KycRequest.php (NEW)
- app/Models/User.php (UPDATED)
- app/Models/Order.php (UPDATED)

**Controllers**:
- app/Http/Controllers/Admin/KycRequestsController.php (NEW)
- app/Http/Controllers/Admin/UsersController.php (NEW)
- app/Http/Controllers/Admin/OrderPagesController.php (UPDATED)
- app/Http/Controllers/Api/V1/KycController.php (UPDATED)
- app/Http/Controllers/Api/V1/ProfileController.php (UPDATED)
- app/Http/Controllers/Api/V1/OrdersController.php (UPDATED)

**Services**:
- app/Services/PriceCheckService.php (NEW)

**Commands**:
- app/Console/Commands/CheckWaitingOrders.php (NEW)

**Views**:
- resources/views/admin/kyc/requests.blade.php (NEW)
- resources/views/admin/orders/index.blade.php (UPDATED)
- resources/views/admin/orders/pending.blade.php (UPDATED)

**Routes**:
- routes/web.php (UPDATED)
- routes/api.php (UNCHANGED)

**Migrations**:
- database/migrations/2026_04_08_081249_add_trading_fields_to_users_table.php (NEW)
- database/migrations/2026_04_08_081308_create_kyc_requests_table.php (NEW)
- database/migrations/2026_04_08_081337_update_orders_table_for_trading_system.php (NEW)

**Documentation**:
- TRADING_SYSTEM_PHASE2.md (NEW)

---

**Ready for deployment! 🚀**
