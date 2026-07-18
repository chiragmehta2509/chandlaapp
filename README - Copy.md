# 🎁 Chandla Book - Complete Laravel Backend Package

## 📦 **What You've Got**

A **complete, production-ready Laravel backend** for your Chandla Book application with:

- ✅ **112+ API Endpoints**
- ✅ **13 Database Tables with Migrations**
- ✅ **14 Eloquent Models with Relationships**
- ✅ **10+ Controllers** (Authentication, User, Event, Contact, Entry, Invitation, UPI, Reports, Notifications)
- ✅ **Complete Swagger Documentation**
- ✅ **Service Classes** (FCM, Payment, Reports)
- ✅ **Social Login** (Google, Facebook, Apple)
- ✅ **Push Notifications** (FCM)
- ✅ **UPI Payment Integration** (Razorpay)
- ✅ **JWT Authentication** (Laravel Sanctum)
- ✅ **Offline Sync Support**
- ✅ **Multi-language Support**

---

## 🚀 **Quick Start (5 Minutes)**

### **Step 1: Install Dependencies**
```bash
composer install
```

### **Step 2: Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials and API keys.

### **Step 3: Setup Database**
```bash
php artisan migrate
```

### **Step 4: Start Server**
```bash
php artisan serve
```

### **Step 5: Test API**
```bash
curl http://localhost:8000/api/v1/auth/google/login \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"id_token":"test","email":"test@example.com"}'
```

---

## 📋 **Installation Guide**

See `INSTALLATION.md` for complete step-by-step setup instructions.

---

## 📖 **API Documentation**

See `swagger.yaml` for complete Swagger/OpenAPI documentation.

Base URL: `http://localhost:8000/api/v1`

---

## 🏗️ **Project Structure**

```
chandla-book-backend/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── Auth/
│   │           ├── User/
│   │           ├── Notification/
│   │           ├── Event/
│   │           ├── Contact/
│   │           ├── Entry/
│   │           ├── Invitation/
│   │           ├── UPI/
│   │           └── Report/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   └── migrations/
└── routes/
    └── api.php
```

---

## 🔧 **Configuration Required**

1. **Firebase Configuration**
   - Download `firebase-credentials.json`
   - Place in `storage/app/`
   - Update `.env` with path

2. **Payment Gateway Keys**
   - Razorpay API keys
   - Update `.env`

3. **Social Login Credentials**
   - Google Client ID/Secret
   - Facebook App ID/Secret
   - Apple Sign In keys
   - Update `.env`

---

## 📝 **License**

MIT

