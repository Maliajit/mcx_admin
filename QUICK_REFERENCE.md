# MCX Trading System - Quick Developer Guide

## 🎯 30-Second Overview

**What was built?**
- KYC approval system (users submit → admin approves → is_verified flag)
- Trading permission system (offline deposits → admin enables → can_trade flag)
- Two order types: market (instant pending) and limit (waits for price trigger)
- Admin panel for reviewing/approving KYC and orders
- Scheduled job to auto-trigger limit orders when price matches

---

## 🚀 Quick Start (< 5 minutes)

### 1. Apply Migrations
```bash
php artisan migrate
```

### 2. Set Up Scheduler
Add to crontab (every minute):
```
* * * * * cd /path/to/mcx_admin && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Test KYC API
```bash
# Submit KYC
curl -X POST http://localhost:8000/api/v1/kyc \
  -H "Content-Type: application/json" \
  -d '{"name":"John","pan":"ABCDE1234F","aadhaar":"123456789012"}'

# Get profile
curl http://localhost:8000/api/v1/profile

# In browser: http://localhost:8000/admin/users/requests
# Click "Approve"

# Try order now
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{"asset_name":"GOLD","type":"market","price":60000}'
```

---

## 📚 API Quick Reference

### POST /api/v1/kyc
**Submit KYC**
```json
{
  "name": "John Doe",
  "pan": "ABCDE1234F",
  "aadhaar": "123456789012"
}
```

### POST /api/v1/orders
**Market Order**
```json
{
  "asset_name": "GOLD",
  "type": "market",
  "price": 60000
}
```

**Limit Order**
```json
{
  "asset_name": "GOLD",
  "type": "limit",
  "price": 60000,
  "target_price": 59500
}
```

---

## 🛠️ Admin Routes Quick Reference

| Action | Route |
|--------|-------|
| KYC Requests | `GET /admin/users/requests` |
| Approve KYC | `POST /admin/kyc/{id}/approve` |
| Reject KYC | `POST /admin/kyc/{id}/reject` |
| Enable Trading | `POST /admin/users/{id}/enable-trading` |
| All Orders | `GET /admin/orders` |
| Pending Orders | `GET /admin/orders/pending` |
| Approve Order | `POST /admin/orders/{id}/approve` |
| Reject Order | `POST /admin/orders/{id}/reject` |

---

## 🗂️ Key Files to Know

### Models
- `app/Models/KycRequest.php` — KYC requests
- `app/Models/User.php` — Added: is_verified, can_trade
- `app/Models/Order.php` — Added: type, target_price, status

### Controllers
- `app/Http/Controllers/Admin/KycRequestsController.php` — Admin KYC actions
- `app/Http/Controllers/Api/V1/KycController.php` — API KYC submission
- `app/Http/Controllers/Api/V1/OrdersController.php` — Order placement with checks
- `app/Http/Controllers/Admin/OrderPagesController.php` — Order approval

### Services
- `app/Services/PriceCheckService.php` — Checks waiting limit orders
- `app/Console/Commands/CheckWaitingOrders.php` — Cron job command

### Views
- `resources/views/admin/kyc/requests.blade.php` — KYC approval panel
- `resources/views/admin/orders/pending.blade.php` — Order approval panel

---

## 🔄 User Journey

```
1. User submits KYC
   POST /api/v1/kyc → kyc_requests.status = pending
   
2. Admin approves KYC
   POST /admin/kyc/{id}/approve → users.is_verified = true
   
3. User deposits offline
   (confirmed by admin)
   
4. Admin enables trading
   POST /admin/users/{id}/enable-trading → users.can_trade = true
   
5. User places market order
   POST /api/v1/orders → orders.status = pending
   
6. Admin approves order
   POST /admin/orders/{id}/approve → orders.status = approved
   
7. Order executed
```

---

## 🧪 Common Tests

### Check Migrations Ran
```bash
php artisan migrate:status
# Should show: users table with is_verified, can_trade
```

### Test KYC Workflow
```bash
# 1. Submit
curl -X POST http://localhost:8000/api/v1/kyc -H "Content-Type: application/json" -d '{"name":"Test","pan":"ABC12345F","aadhaar":"123456789012"}'

# 2. Check profile before approval
curl http://localhost:8000/api/v1/profile | grep is_verified

# 3. Admin approve (web UI)
# http://localhost:8000/admin/users/requests

# 4. Check profile after approval
curl http://localhost:8000/api/v1/profile | grep is_verified
# Should now be true
```

### Test Order Restrictions
```bash
# Before KYC verified - should fail
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{"asset_name":"GOLD","type":"market","price":60000}' \
# Response: "KYC verification is required"

# After KYC but before trading enabled - should fail
# Response: "Trading is not enabled"

# After both enabled - should work
```

### Test Limit Order Auto-Trigger
```bash
# Place limit order
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Content-Type: application/json" \
  -d '{"asset_name":"GOLD MCX","type":"limit","price":60000,"target_price":59500}'

# Check status - should be "waiting"
mysql -u root -e "SELECT id, status, target_price FROM orders ORDER BY id DESC LIMIT 1;"

# Run scheduler
php artisan app:check-waiting-orders

# Check again - status should change to "pending" when price condition matches
```

---

## 🐛 Debugging

### See what's in the database
```bash
# KYC Requests
mysql -u root -e "SELECT id, user_id, status, created_at FROM kyc_requests;"

# Orders
mysql -u root -e "SELECT id, user_id, asset, type, status, target_price FROM orders;"

# Users
mysql -u root -e "SELECT id, email, is_verified, can_trade FROM users;"
```

### Check Recent Logs
```bash
tail -f storage/logs/laravel.log
# Look for: "Order X moved to pending"
```

### Test Scheduler Manually
```bash
php artisan app:check-waiting-orders
# Should output: "Checking waiting orders..."
# Then: "Done."
```

---

## ⚠️ Common Issues

### "KYC not verified" when submitting order
- User must have is_verified=true
- Admin must approve KYC from /admin/users/requests

### "Trading not enabled" when submitting order
- User must have can_trade=true
- Admin must click "Enable Trading" after verifying deposit

### Limit orders not auto-triggering
- Scheduler must be running: `ps aux | grep schedule:run`
- Run manually: `php artisan app:check-waiting-orders`
- Check logs: `tail storage/logs/laravel.log`

### Admin pages showing 404
- Check routes named correctly: `php artisan route:list | grep admin`
- Clear cache: `php artisan route:clear`

---

## 💡 Code Patterns to Follow

### In Controllers
```php
// Check permissions
if (!$user->is_verified) {
    return ApiResponse::error('KYC verification is required.', 403);
}

if (!$user->can_trade) {
    return ApiResponse::error('Trading is not enabled.', 403);
}
```

### In Models
```php
public function kycRequests() {
    return $this->hasMany(KycRequest::class);
}

public function orders() {
    return $this->hasMany(Order::class);
}
```

### In Services
```php
$waitingOrders = Order::where('status', 'waiting')->get();
foreach ($waitingOrders as $order) {
    // Check price condition
    if ($currentPrice <= $order->target_price) {
        $order->update(['status' => 'pending']);
    }
}
```

---

## 📋 Status Definitions

### KYC Request Status
- `pending` — Awaiting admin review
- `approved` — Approved, user is_verified = true
- `rejected` — Rejected, can resubmit

### Order Status
- `waiting` — Limit order, waiting for price trigger
- `pending` — Awaiting admin approval
- `approved` — Admin approved
- `rejected` — Admin rejected

---

## 🔐 Validation Rules

### KYC Submission
- name: required, max 255
- pan: required, max 20
- aadhaar: required, max 20
- Cannot have duplicate pending/approved request

### Order Placement
- asset_name: required, max 255
- type: required, must be market or limit
- price: required, numeric, min 0
- target_price: required if type=limit
- User must have: is_verified=true AND can_trade=true

---

## 🚀 Production Checklist

- [ ] Run migrations
- [ ] Set up scheduler cron job
- [ ] Test all API endpoints
- [ ] Test admin panel
- [ ] Test scheduler: `php artisan app:check-waiting-orders`
- [ ] Check logs for errors
- [ ] Add auth middleware to admin routes
- [ ] Set error notification channels
- [ ] Backup database
- [ ] Deploy!

---

**Questions? Check TRADING_SYSTEM_PHASE2.md or IMPLEMENTATION_SUMMARY.md**
