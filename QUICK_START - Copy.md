# 🚀 Quick Start Guide - Chandla Book Backend

## ⚡ 5-Minute Setup

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:
- Database credentials
- Firebase credentials path
- Social login keys (optional for now)
- Razorpay keys (optional for now)

### 3. Setup Database
```bash
# Create database manually or use:
mysql -u root -p -e "CREATE DATABASE chandla_book CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate
```

### 4. Create Storage Link
```bash
php artisan storage:link
```

### 5. Start Server
```bash
php artisan serve
```

### 6. Test API
```bash
# Test endpoint
curl http://localhost:8000/api/v1/auth/register \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

---

## 📋 What's Included

✅ **13 Database Tables** - All migrations ready  
✅ **14 Eloquent Models** - With relationships  
✅ **112+ API Endpoints** - Fully documented  
✅ **9 Controllers** - Complete CRUD operations  
✅ **2 Service Classes** - FCM & Payment  
✅ **Swagger Documentation** - OpenAPI 3.0  
✅ **Installation Guide** - Step-by-step  

---

## 🔑 Key Endpoints

### Authentication
- `POST /api/v1/auth/register` - Register
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/google/login` - Google Sign In
- `POST /api/v1/auth/phone/send-otp` - Send OTP
- `POST /api/v1/auth/phone/verify-otp` - Verify OTP
- `GET /api/v1/auth/me` - Get current user
- `POST /api/v1/auth/logout` - Logout

### Events
- `GET /api/v1/events` - List events
- `POST /api/v1/events` - Create event
- `GET /api/v1/events/{id}` - Get event
- `PUT /api/v1/events/{id}` - Update event
- `DELETE /api/v1/events/{id}` - Delete event

### Contacts
- `GET /api/v1/contacts` - List contacts
- `POST /api/v1/contacts` - Create contact
- `POST /api/v1/contacts/import` - Import from Excel
- `GET /api/v1/contacts/export` - Export to Excel

### Payments
- `POST /api/v1/payments/create-order` - Create payment order
- `POST /api/v1/payments/verify` - Verify payment

---

## 📚 Documentation

- **Full Installation:** See `INSTALLATION.md`
- **API Documentation:** See `swagger.yaml`
- **Project Structure:** See `PROJECT_STRUCTURE.md`

---

## 🎯 Next Steps

1. ✅ Complete setup (you're here!)
2. ⏳ Configure Firebase for push notifications
3. ⏳ Set up Razorpay for payments
4. ⏳ Configure social login providers
5. ⏳ Connect your Flutter/Mobile app
6. ⏳ Test all endpoints
7. ⏳ Deploy to production

---

**You're all set! Start building! 🎉**

