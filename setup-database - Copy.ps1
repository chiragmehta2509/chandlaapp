# Database Setup Script

Write-Host "Setting up database configuration..." -ForegroundColor Green

# Read current .env or create from template
$envContent = @"
APP_NAME="Chandla Book"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chandla_book
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="`${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="`${APP_NAME}"

# Firebase Configuration
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
FIREBASE_PROJECT_ID=

# Social Login
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
APPLE_CLIENT_ID=
APPLE_TEAM_ID=
APPLE_KEY_ID=
APPLE_PRIVATE_KEY=

# Razorpay Payment
RAZORPAY_KEY_ID=
RAZORPAY_KEY_SECRET=

# SMS Gateway (MSG91)
MSG91_AUTH_KEY=
MSG91_SENDER_ID=

# JWT Configuration
JWT_SECRET=
JWT_TTL=60
JWT_REFRESH_TTL=20160

# API Configuration
API_RATE_LIMIT=60
API_RATE_LIMIT_PER_MINUTE=60
"@

# Write .env file
$envContent | Out-File -FilePath .env -Encoding utf8 -NoNewline

Write-Host "✓ .env file created" -ForegroundColor Green

# Generate app key
Write-Host "Generating application key..." -ForegroundColor Yellow
php artisan key:generate

Write-Host "`n✓ Database configuration ready!" -ForegroundColor Green
Write-Host "`nPlease update DB_PASSWORD in .env file with your MySQL password" -ForegroundColor Yellow

