# OTP Authentication System

## Overview
The system now supports phone-based OTP authentication with KYC verification checks.

## API Endpoints

### 1. Send OTP
**POST** `/api/v1/auth/send-otp`

**Request:**
```json
{
  "phone": "+91 9999999999"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "message": "OTP sent successfully.",
    "phone": "+91 9999999999",
    "otp_for_testing": "123456"
  }
}
```

### 2. Verify OTP
**POST** `/api/v1/auth/verify-otp`

**Request:**
```json
{
  "phone": "+91 9999999999",
  "otp": "123456"
}
```

**Success Response (Verified User):**
```json
{
  "success": true,
  "data": {
    "message": "Login successful.",
    "user": {
      "id": 1,
      "name": "John Doe",
      "phone": "+91 9999999999",
      "is_verified": true,
      "can_trade": false
    },
    "session_token": "mock-session-1"
  }
}
```

**Error Response (Unverified Phone):**
```json
{
  "success": false,
  "error": "Phone number not verified. Please complete KYC verification first.",
  "data": {
    "requires_kyc": true
  }
}
```

## Testing Flow

### Step 1: Submit KYC (if not done)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/profile/kyc \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","pan":"ABCDE1234F","aadhaar":"123456789012"}'
```

### Step 2: Admin Approve KYC
Visit: `http://127.0.0.1:8000/admin/users/requests`
Click "Approve" on the request.

### Step 3: Send OTP
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/send-otp \
  -H "Content-Type: application/json" \
  -d '{"phone":"+91 9999999999"}'
```
Check Laravel logs for the OTP: `tail storage/logs/laravel.log`

### Step 4: Verify OTP
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/verify-otp \
  -H "Content-Type: application/json" \
  -d '{"phone":"+91 9999999999","otp":"123456"}'
```

### Step 5: Use Session Token for Authenticated Requests
```bash
curl -X GET http://127.0.0.1:8000/api/v1/profile \
  -H "Authorization: mock-session-1"
```

## Key Features

- **OTP Generation**: 6-digit codes, expires in 5 minutes
- **KYC Verification**: Only approved KYC phone numbers can login
- **Session Management**: Mock session tokens for authenticated requests
- **Security**: OTPs are cleared after successful verification
- **Logging**: OTPs logged for testing (remove in production)

## Phone Number Format
- Must be in format: `+91 9999999999`
- 10 digits after +91 space
- Validation enforced on both send and verify endpoints

## Error Scenarios

1. **Invalid Phone Format**: `"The phone field format is invalid."`
2. **Invalid OTP**: `"Invalid or expired OTP."`
3. **Unverified Phone**: `"Phone number not verified. Please complete KYC verification first."`
4. **Missing Fields**: Standard Laravel validation errors</content>
<parameter name="filePath">c:\xampp\htdocs\mcx_admin\OTP_SYSTEM_README.md