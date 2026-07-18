# Script to enable PHP extensions in XAMPP

$phpIniPath = "D:\xampp\php\php.ini"

if (-not (Test-Path $phpIniPath)) {
    Write-Host "PHP.ini not found at: $phpIniPath" -ForegroundColor Red
    Write-Host "Please update the path in this script" -ForegroundColor Yellow
    exit
}

Write-Host "Enabling PHP extensions..." -ForegroundColor Green

# Read php.ini
$phpIni = Get-Content $phpIniPath -Raw

# Extensions to enable
$extensions = @(
    "extension=sodium",
    "extension=openssl",
    "extension=pdo_mysql",
    "extension=mbstring",
    "extension=fileinfo",
    "extension=curl"
)

foreach ($ext in $extensions) {
    $extName = $ext -replace "extension=", ""
    
    # Check if extension line exists
    if ($phpIni -match ";?\s*$ext") {
        # Uncomment if commented
        $phpIni = $phpIni -replace ";?\s*$ext", $ext
        Write-Host "✓ Enabled: $extName" -ForegroundColor Green
    } else {
        # Add if not present
        $phpIni += "`n$ext"
        Write-Host "✓ Added: $extName" -ForegroundColor Green
    }
}

# Write back to file
Set-Content -Path $phpIniPath -Value $phpIni -NoNewline

Write-Host "`nExtensions enabled! Please restart XAMPP/Apache for changes to take effect." -ForegroundColor Yellow

