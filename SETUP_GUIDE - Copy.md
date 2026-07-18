# 🔧 Complete Setup Guide - Chandla Book Backend

## Step 1: Install PHP Extensions

### Required PHP Extensions
Make sure these are enabled in your `php.ini` file:

```ini
extension=sodium
extension=openssl
extension=pdo_mysql
extension=mbstring
extension=fileinfo
extension=curl
extension=json
extension=xml
```

**For XAMPP on Windows:**
1. Open `D:\xampp\php\php.ini`
2. Find and uncomment (remove `;`) these lines:
   ```
   extension=sodium
   extension=openssl
   extension=pdo_mysql
   extension=mbstring
   extension=fileinfo
   extension=curl
   ```
3. Restart Apache/XAMPP

**Or install with ignore flag:**
```bash
composer install --ignore-platform-req=ext-sodium
```

---

## Step 2: Install Dependencies

```bash
composer install
```

If you get sodium extension error:
```bash
composer install --ignore-platform-req=ext-sodium
```

---

## Step 3: Configure Database

### Option A: Using MySQL Command Line
```bash
mysql -u root -p
```

Then run:
```sql
CREATE DATABASE chandla_book CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Option B: Using phpMyAdmin
1. Open phpMyAdmin (usually http://localhost/phpmyadmin)
2. Click "New" to create database
3. Name: `chandla_book`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Update .env File
Edit `.env` and set:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chandla_book
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

---

## Step 4: Run Migrations

```bash
php artisan migrate
```

---

## Step 5: Create Storage Link

```bash
php artisan storage:link
```

---

## Step 6: Firebase Setup

### 6.1 Get Firebase Credentials

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Click "Add Project" or select existing project
3. Project name: `chandla-book` (or your choice)
4. Click "Continue" → "Create Project"

### 6.2 Enable Cloud Messaging

1. In Firebase Console, go to **Project Settings** (gear icon)
2. Click **Cloud Messaging** tab
3. Note your **Server Key** (we'll use this later)

### 6.3 Download Service Account Key

1. Go to **Project Settings** → **Service Accounts**
2. Click **Generate New Private Key**
3. Download the JSON file
4. Save it as `firebase-credentials.json` in `storage/app/` directory

### 6.4 Update .env

```env
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
FIREBASE_PROJECT_ID=your-project-id
```

**Note:** `FIREBASE_PROJECT_ID` is found in Firebase Console → Project Settings → General

---

## Step 7: Razorpay Setup

### 7.1 Create Razorpay Account

1. Go to [Razorpay](https://razorpay.com/)
2. Sign up for an account
3. Complete KYC verification (for live mode)

### 7.2 Get API Keys

**For Testing (Test Mode):**
1. Login to Razorpay Dashboard
2. Go to **Settings** → **API Keys**
3. Click **Generate Test Key**
4. Copy **Key ID** and **Key Secret**

**For Production (Live Mode):**
1. Complete KYC verification
2. Go to **Settings** → **API Keys**
3. Click **Generate Live Key**
4. Copy **Key ID** and **Key Secret**

### 7.3 Update .env

```env
RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxxx
RAZORPAY_KEY_SECRET=your_key_secret_here
```

**Note:** 
- Test keys start with `rzp_test_`
- Live keys start with `rzp_live_`
- Use test keys for development

---

## Step 8: Social Login Setup (Optional)

### 8.1 Google Sign In

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project or select existing
3. Enable **Google+ API**
4. Go to **Credentials** → **Create Credentials** → **OAuth 2.0 Client ID**
5. Application type: **Web application**
6. Authorized redirect URIs: `http://localhost:8000/api/v1/auth/google/callback`
7. Copy **Client ID** and **Client Secret**

Update `.env`:
```env
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

### 8.2 Facebook Login

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create new app → Choose **Consumer** type
3. Add **Facebook Login** product
4. Go to **Settings** → **Basic**
5. Add **Valid OAuth Redirect URIs**: `http://localhost:8000/api/v1/auth/facebook/callback`
6. Copy **App ID** and **App Secret**

Update `.env`:
```env
FACEBOOK_APP_ID=your-facebook-app-id
FACEBOOK_APP_SECRET=your-facebook-app-secret
```

### 8.3 Apple Sign In (Optional)

1. Go to [Apple Developer Portal](https://developer.apple.com/)
2. Create App ID with "Sign in with Apple" capability
3. Create Service ID
4. Generate private key
5. Download `.p8` key file

Update `.env`:
```env
APPLE_CLIENT_ID=your-apple-client-id
APPLE_TEAM_ID=your-team-id
APPLE_KEY_ID=your-key-id
APPLE_PRIVATE_KEY=path/to/your/key.p8
```

---

## Step 9: SMS Gateway Setup (Optional - for OTP)

### Using MSG91

1. Sign up at [MSG91](https://msg91.com/)
2. Get your **Auth Key** from dashboard
3. Get your **Sender ID**

Update `.env`:
```env
MSG91_AUTH_KEY=your-msg91-auth-key
MSG91_SENDER_ID=CHANDLA
```

---

## Step 10: Generate Application Key

```bash
php artisan key:generate
```

This will automatically update your `.env` file with `APP_KEY`.

---

## Step 11: Start Development Server

```bash
php artisan serve
```

Server will start at: `http://localhost:8000`

---

## Step 12: Verify Setup

### Test Database Connection
```bash
php artisan migrate:status
```

### Test API Endpoint
```bash
curl http://localhost:8000/api/v1/auth/register \
  -X POST \
  -H "Content-Type: application/json" \
  -d "{\"name\":\"Test User\",\"email\":\"test@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}"
```

---

## ✅ Configuration Checklist

- [ ] PHP extensions enabled (sodium, openssl, pdo_mysql, etc.)
- [ ] Composer dependencies installed
- [ ] Database created and configured
- [ ] Migrations run successfully
- [ ] Storage link created
- [ ] Firebase credentials downloaded and placed
- [ ] Razorpay API keys configured
- [ ] Social login keys configured (optional)
- [ ] Application key generated
- [ ] Server running on localhost:8000

---

## 🐛 Troubleshooting

### Issue: "Class not found" errors
**Solution:** Run `composer dump-autoload`

### Issue: Database connection failed
**Solution:** 
- Check MySQL is running
- Verify credentials in `.env`
- Check database exists

### Issue: Storage files not accessible
**Solution:** Run `php artisan storage:link`

### Issue: Firebase not working
**Solution:**
- Verify credentials file path
- Check file permissions
- Ensure Firebase project is active

### Issue: Razorpay payment fails
**Solution:**
- Verify API keys are correct
- Check if using test keys in test mode
- Ensure signature verification is working

---

## 🚀 Next Steps

1. Test all API endpoints (see `API_TESTS.md`)
2. Set up Flutter app (see `FLUTTER_CLIENT.md`)
3. Configure production environment
4. Deploy to server

---

**Setup Complete! 🎉**

