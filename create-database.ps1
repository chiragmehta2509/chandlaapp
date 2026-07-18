# Create Database Script

Write-Host "Creating database..." -ForegroundColor Yellow

$dbName = "chandla_book"
$dbUser = "root"
$dbPassword = Read-Host "Enter MySQL root password (press Enter if no password)"

if ([string]::IsNullOrWhiteSpace($dbPassword)) {
    $mysqlCmd = "mysql -u $dbUser"
} else {
    $mysqlCmd = "mysql -u $dbUser -p$dbPassword"
}

$sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

try {
    $sql | & $mysqlCmd 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Database '$dbName' created successfully!" -ForegroundColor Green
    } else {
        Write-Host "✗ Failed to create database. Please create manually:" -ForegroundColor Red
        Write-Host "  CREATE DATABASE $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Yellow
    }
} catch {
    Write-Host "✗ Error: $_" -ForegroundColor Red
    Write-Host "`nPlease create database manually:" -ForegroundColor Yellow
    Write-Host "1. Open MySQL command line or phpMyAdmin" -ForegroundColor White
    Write-Host "2. Run: CREATE DATABASE $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor Cyan
}

