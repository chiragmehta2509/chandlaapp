# Run Project Script

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Starting Chandla Book Backend" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Check if database exists
Write-Host "Checking database..." -ForegroundColor Yellow
$dbCheck = php artisan migrate:status 2>&1

if ($LASTEXITCODE -ne 0) {
    Write-Host "Database 'chandla_book' does not exist!" -ForegroundColor Red
    Write-Host "`nPlease create the database first:" -ForegroundColor Yellow
    Write-Host "1. Open MySQL command line or phpMyAdmin" -ForegroundColor White
    Write-Host "2. Run: CREATE DATABASE chandla_book CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Cyan
    Write-Host "3. Update .env with your MySQL password (DB_PASSWORD)" -ForegroundColor White
    Write-Host "4. Run this script again`n" -ForegroundColor White
    
    $createNow = Read-Host "Do you want to create database now? (y/n)"
    if ($createNow -eq 'y' -or $createNow -eq 'Y') {
        Write-Host "`nPlease run this SQL command in MySQL:" -ForegroundColor Yellow
        Write-Host "CREATE DATABASE chandla_book CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Cyan
        Write-Host "`nThen update .env with DB_PASSWORD and run this script again." -ForegroundColor Yellow
        exit
    }
    exit
}

# Run migrations if needed
Write-Host "Checking migrations..." -ForegroundColor Yellow
$migrations = php artisan migrate:status 2>&1
if ($LASTEXITCODE -eq 0) {
    $pending = $migrations | Select-String -Pattern "Pending"
    if ($pending) {
        Write-Host "Running migrations..." -ForegroundColor Yellow
        php artisan migrate --force
        if ($LASTEXITCODE -eq 0) {
            Write-Host "Migrations completed!" -ForegroundColor Green
        }
    } else {
        Write-Host "All migrations are up to date" -ForegroundColor Green
    }
}

# Start server
Write-Host "`nStarting Laravel development server..." -ForegroundColor Yellow
Write-Host "Server will be available at: http://localhost:8000" -ForegroundColor Cyan
Write-Host "API Base URL: http://localhost:8000/api/v1" -ForegroundColor Cyan
Write-Host "`nPress Ctrl+C to stop the server`n" -ForegroundColor Yellow

php artisan serve

