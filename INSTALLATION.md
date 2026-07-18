# 📦 Chandla Book Backend - Installation Guide

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL >= 5.7 or MariaDB >= 10.3
- Node.js & NPM (for frontend assets, if needed)
- Firebase Account (for push notifications)
- Razorpay Account (for payments)
- Google/Facebook/Apple Developer Accounts (for social login)

---

## Step 1: Install Dependencies

```bash
composer install
```

This will install all required packages including:
- Laravel Framework
- Laravel Sanctum (JWT Authentication)
- Firebase PHP SDK
- Google API Client
- Razorpay SDK
- Maatwebsite Excel
- DomPDF

---

## Step 2: Environment Configuration

1. Copy the example environment file:
```bash
cp .env.example .env
```

2. Generate application key:
```bash
php artisan key:generate
```

3. Update `.env` file with your configuration:

```env
# Application
APP_NAME="Chandla Book"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chandla_book
DB_USERNAME=root
DB_PASSWORD=your_password

# Firebase Configuration
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
FIREBASE_PROJECT_ID=your-project-id

# Social Login
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
FACEBOOK_APP_ID=your-facebook-app-id
FACEBOOK_APP_SECRET=your-facebook-app-secret
APPLE_CLIENT_ID=your-apple-client-id
APPLE_TEAM_ID=your-apple-team-id
APPLE_KEY_ID=your-apple-key-id
APPLE_PRIVATE_KEY=your-apple-private-key

# Razorpay Payment
RAZORPAY_KEY_ID=your-razorpay-key-id
RAZORPAY_KEY_SECRET=your-razorpay-key-secret

# SMS Gateway (Optional - for OTP)
MSG91_AUTH_KEY=your-msg91-auth-key
MSG91_SENDER_ID=your-sender-id
```

---

## Step 3: Firebase Setup

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select existing one
3. Go to Project Settings > Service Accounts
4. Click "Generate New Private Key"
5. Download the JSON file
6. Save it as `firebase-credentials.json` in `storage/app/` directory
7. Update `FIREBASE_PROJECT_ID` in `.env`

---

## Step 4: Database Setup

1. Create MySQL database:
```sql
CREATE DATABASE chandla_book CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Run migrations:
```bash
php artisan migrate
```

This will create all 13 tables:
- users
- events
- contacts
- entries
- invitations
- invitation_shares
- upi_transactions
- device_tokens
- notifications
- user_settings
- activity_logs
- event_collaborators
- personal_access_tokens

---

## Step 5: Storage Setup

Create storage link for public access:
```bash
php artisan storage:link
```

This allows access to uploaded files (avatars, event covers, etc.)

---

## Step 6: Configure Social Login

### Google Sign In

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add authorized redirect URIs
6. Copy Client ID and Secret to `.env`

### Facebook Login

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app
3. Add Facebook Login product
4. Configure OAuth redirect URIs
5. Copy App ID and Secret to `.env`

### Apple Sign In

1. Go to [Apple Developer Portal](https://developer.apple.com/)
2. Create App ID with Sign in with Apple capability
3. Create Service ID
4. Generate private key
5. Copy credentials to `.env`

---

## Step 7: Configure Razorpay

1. Sign up at [Razorpay](https://razorpay.com/)
2. Go to Settings > API Keys
3. Generate Test/Live API keys
4. Copy Key ID and Secret to `.env`

---

## Step 8: Start Development Server

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

API Base URL: `http://localhost:8000/api/v1`

---

## Step 9: Test API

### Test Google Login:
```bash
curl -X POST http://localhost:8000/api/v1/auth/google/login \
  -H "Content-Type: application/json" \
  -d '{
    "id_token": "your-google-id-token",
    "email": "test@example.com",
    "name": "Test User"
  }'
```

### Test Phone OTP:
```bash
# Send OTP
curl -X POST http://localhost:8000/api/v1/auth/phone/send-otp \
  -H "Content-Type: application/json" \
  -d '{"phone": "9876543210"}'

# Verify OTP
curl -X POST http://localhost:8000/api/v1/auth/phone/verify-otp \
  -H "Content-Type: application/json" \
  -d '{"phone": "9876543210", "otp": "123456"}'
```

---

## Production Deployment

### 1. Update Environment

Set production values in `.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.chandlabook.com
```

### 2. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Set Up Queue Worker

For background jobs (notifications, emails):
```bash
php artisan queue:work
```

### 4. Set Up Cron Job

Add to crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Configure Web Server

#### Nginx Configuration Example:

```nginx
server {
    listen 80;
    server_name api.chandlabook.com;
    root /var/www/chandla-book-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Troubleshooting

### Issue: Firebase credentials not found

**Solution:**
- Ensure `firebase-credentials.json` is in `storage/app/` directory
- Check file permissions (should be readable)
- Verify path in `.env` matches actual file location

### Issue: Razorpay payment verification fails

**Solution:**
- Verify API keys are correct
- Check if using test keys in production (or vice versa)
- Ensure signature verification logic is correct

### Issue: Social login not working

**Solution:**
- Verify OAuth credentials in `.env`
- Check redirect URIs match in provider console
- Ensure required APIs are enabled

### Issue: Database connection error

**Solution:**
- Verify database credentials in `.env`
- Check MySQL service is running
- Ensure database exists
- Check user has proper permissions

### Issue: Storage files not accessible

**Solution:**
- Run `php artisan storage:link`
- Check `public/storage` symlink exists
- Verify file permissions

---

## Security Checklist

- [ ] Change default APP_KEY
- [ ] Set APP_DEBUG=false in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS in production
- [ ] Configure CORS properly
- [ ] Set up rate limiting
- [ ] Enable API authentication
- [ ] Secure Firebase credentials
- [ ] Use environment variables for secrets
- [ ] Regular security updates

---

## API Documentation

Swagger documentation is available at:
- Local: `http://localhost:8000/api/documentation` (if Swagger UI is installed)
- Or import `swagger.yaml` into Postman/Insomnia

---

## Support

For issues or questions:
- Check documentation in `README.md`
- Review API routes in `routes/api.php`
- Check Swagger documentation in `swagger.yaml`

---

## Next Steps

1. ✅ Set up frontend application (Flutter/React Native)
2. ✅ Connect frontend to API endpoints
3. ✅ Test all authentication flows
4. ✅ Test payment integration
5. ✅ Set up push notifications
6. ✅ Deploy to production

---

**Happy Coding! 🚀**

