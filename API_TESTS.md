# 🧪 API Testing Guide - Chandla Book Backend

## Prerequisites

1. Server running: `php artisan serve`
2. Database configured and migrated
3. Postman or curl installed

---

## Test Scripts

### 1. Register New User

```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

**Save the token** for subsequent requests!

---

### 2. Login

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

---

### 3. Get Current User (Protected)

```bash
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

### 4. Create Event

```bash
curl -X POST http://localhost:8000/api/v1/events \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Wedding Ceremony",
    "description": "Main wedding event",
    "event_date": "2024-12-25",
    "event_time": "18:00:00",
    "venue": "Grand Hotel",
    "event_type": "wedding"
  }'
```

**Save the event ID** for next requests!

---

### 5. List Events

```bash
curl -X GET http://localhost:8000/api/v1/events \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

### 6. Get Event Details

```bash
curl -X GET http://localhost:8000/api/v1/events/1 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

### 7. Create Contact

```bash
curl -X POST http://localhost:8000/api/v1/contacts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Smith",
    "phone": "9876543210",
    "email": "jane@example.com",
    "address": "123 Main St",
    "relationship": "Friend"
  }'
```

---

### 8. Create Entry (Guest)

```bash
curl -X POST http://localhost:8000/api/v1/entries \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "event_id": 1,
    "contact_id": 1,
    "guest_name": "Jane Smith",
    "guest_phone": "9876543210",
    "adults_count": 2,
    "children_count": 0,
    "status": "pending"
  }'
```

---

### 9. Create Invitation

```bash
curl -X POST http://localhost:8000/api/v1/invitations \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "event_id": 1,
    "entry_id": 1,
    "type": "digital",
    "custom_message": "You are cordially invited!"
  }'
```

---

### 10. Get Dashboard Stats

```bash
curl -X GET http://localhost:8000/api/v1/reports/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

---

### 11. Send OTP (Phone Login)

```bash
curl -X POST http://localhost:8000/api/v1/auth/phone/send-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "9876543210"
  }'
```

**Note:** In development, OTP is returned in response. In production, it's sent via SMS.

---

### 12. Verify OTP

```bash
curl -X POST http://localhost:8000/api/v1/auth/phone/verify-otp \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "9876543210",
    "otp": "123456"
  }'
```

---

## Postman Collection

### Import to Postman

1. Open Postman
2. Click **Import**
3. Create new collection: **Chandla Book API**
4. Add requests manually or use the examples below

### Postman Environment Variables

Create environment with:
- `base_url`: `http://localhost:8000/api/v1`
- `token`: (will be set after login)

### Postman Pre-request Script (for token)

Add to collection:
```javascript
// Auto-set token from login response
if (pm.response.code === 200) {
    const jsonData = pm.response.json();
    if (jsonData.data && jsonData.data.token) {
        pm.environment.set("token", jsonData.data.token);
    }
}
```

---

## Automated Test Script

Create `test-api.sh`:

```bash
#!/bin/bash

BASE_URL="http://localhost:8000/api/v1"

echo "Testing Chandla Book API..."

# Register
echo "1. Registering user..."
REGISTER_RESPONSE=$(curl -s -X POST $BASE_URL/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test'$(date +%s)'@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }')

TOKEN=$(echo $REGISTER_RESPONSE | jq -r '.data.token')
echo "Token: $TOKEN"

# Create Event
echo "2. Creating event..."
EVENT_RESPONSE=$(curl -s -X POST $BASE_URL/events \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Test Event",
    "event_date": "2024-12-25",
    "event_type": "wedding"
  }')

EVENT_ID=$(echo $EVENT_RESPONSE | jq -r '.data.id')
echo "Event ID: $EVENT_ID"

# Get Events
echo "3. Getting events..."
curl -s -X GET $BASE_URL/events \
  -H "Authorization: Bearer $TOKEN" | jq

echo "Tests completed!"
```

---

## Expected Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Testing Checklist

- [ ] User Registration
- [ ] User Login
- [ ] Get Current User
- [ ] Create Event
- [ ] List Events
- [ ] Update Event
- [ ] Delete Event
- [ ] Create Contact
- [ ] List Contacts
- [ ] Create Entry
- [ ] Create Invitation
- [ ] Get Dashboard
- [ ] Phone OTP (if configured)
- [ ] Payment Order Creation (if Razorpay configured)

---

**Happy Testing! 🧪**

