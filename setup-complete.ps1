# Complete Setup Script for Chandla Book Backend

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Chandla Book Backend - Setup Script" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Step 1: Generate App Key
Write-Host "Step 1: Generating application key..." -ForegroundColor Yellow
php artisan key:generate
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Application key generated" -ForegroundColor Green
} else {
    Write-Host "✗ Failed to generate key" -ForegroundColor Red
}

# Step 2: Create Storage Link
Write-Host "`nStep 2: Creating storage link..." -ForegroundColor Yellow
php artisan storage:link
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Storage link created" -ForegroundColor Green
} else {
    Write-Host "⚠ Storage link may already exist" -ForegroundColor Yellow
}

# Step 3: Run Migrations
Write-Host "`nStep 3: Running database migrations..." -ForegroundColor Yellow
Write-Host "Please ensure MySQL is running and database 'chandla_book' exists" -ForegroundColor Yellow
$runMigrations = Read-Host "Run migrations now? (y/n)"
if ($runMigrations -eq 'y' -or $runMigrations -eq 'Y') {
    php artisan migrate
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Migrations completed" -ForegroundColor Green
    } else {
        Write-Host "✗ Migrations failed. Please check database configuration" -ForegroundColor Red
    }
} else {
    Write-Host "Skipped migrations. Run 'php artisan migrate' manually" -ForegroundColor Yellow
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Setup Complete!" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Update .env with your database password" -ForegroundColor White
Write-Host "2. Create database: CREATE DATABASE chandla_book;" -ForegroundColor White
Write-Host "3. Run: php artisan migrate" -ForegroundColor White
Write-Host "4. Set up Firebase credentials (see SETUP_GUIDE.md)" -ForegroundColor White
Write-Host "5. Configure Razorpay keys (see SETUP_GUIDE.md)" -ForegroundColor White
Write-Host "6. Start server: php artisan serve" -ForegroundColor White
Write-Host "7. Test API: .\test-api.ps1`n" -ForegroundColor White

