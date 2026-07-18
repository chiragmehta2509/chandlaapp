# PowerShell Script to Test Chandla Book API

$baseUrl = "http://localhost:8000/api/v1"
$token = ""

Write-Host "Testing Chandla Book API..." -ForegroundColor Green

# 1. Register User
Write-Host "`n1. Registering user..." -ForegroundColor Yellow
$registerBody = @{
    name = "Test User"
    email = "test$(Get-Date -Format 'yyyyMMddHHmmss')@example.com"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-RestMethod -Uri "$baseUrl/auth/register" `
        -Method Post `
        -ContentType "application/json" `
        -Body $registerBody
    
    if ($registerResponse.success) {
        $token = $registerResponse.data.token
        Write-Host "[OK] Registration successful!" -ForegroundColor Green
        Write-Host "Token: $token" -ForegroundColor Cyan
    }
} catch {
    Write-Host "[FAIL] Registration failed: $_" -ForegroundColor Red
    exit
}

# 2. Get Current User
Write-Host "`n2. Getting current user..." -ForegroundColor Yellow
try {
    $headers = @{
        "Authorization" = "Bearer $token"
        "Content-Type" = "application/json"
    }
    $userResponse = Invoke-RestMethod -Uri "$baseUrl/auth/me" `
        -Method Get `
        -Headers $headers
    
    if ($userResponse.success) {
        Write-Host "[OK] User retrieved!" -ForegroundColor Green
        Write-Host "User: $($userResponse.data.name)" -ForegroundColor Cyan
    }
} catch {
    Write-Host "[FAIL] Failed: $_" -ForegroundColor Red
}

# 3. Create Event
Write-Host "`n3. Creating event..." -ForegroundColor Yellow
$eventBody = @{
    title = "Test Wedding Event"
    description = "This is a test event"
    event_date = "2024-12-25"
    event_time = "18:00"
    venue = "Grand Hotel"
    event_type = "wedding"
} | ConvertTo-Json

try {
    $eventResponse = Invoke-RestMethod -Uri "$baseUrl/events" `
        -Method Post `
        -Headers $headers `
        -Body $eventBody
    
    if ($eventResponse.success) {
        $eventId = $eventResponse.data.id
        Write-Host "[OK] Event created! ID: $eventId" -ForegroundColor Green
    }
} catch {
    Write-Host "[FAIL] Failed: $_" -ForegroundColor Red
}

# 4. List Events
Write-Host "`n4. Listing events..." -ForegroundColor Yellow
try {
    $eventsResponse = Invoke-RestMethod -Uri "$baseUrl/events" `
        -Method Get `
        -Headers $headers
    
    if ($eventsResponse.success) {
        Write-Host "[OK] Events retrieved!" -ForegroundColor Green
        Write-Host "Total events: $($eventsResponse.data.data.Count)" -ForegroundColor Cyan
    }
} catch {
    Write-Host "[FAIL] Failed: $_" -ForegroundColor Red
}

# 5. Create Contact
Write-Host "`n5. Creating contact..." -ForegroundColor Yellow
$contactBody = @{
    name = "Jane Smith"
    phone = "9876543210"
    email = "jane@example.com"
    relationship = "Friend"
} | ConvertTo-Json

try {
    $contactResponse = Invoke-RestMethod -Uri "$baseUrl/contacts" `
        -Method Post `
        -Headers $headers `
        -Body $contactBody
    
    if ($contactResponse.success) {
        Write-Host "[OK] Contact created!" -ForegroundColor Green
    }
} catch {
    Write-Host "[FAIL] Failed: $_" -ForegroundColor Red
}

# 6. Get Dashboard
Write-Host "`n6. Getting dashboard stats..." -ForegroundColor Yellow
try {
    $dashboardResponse = Invoke-RestMethod -Uri "$baseUrl/reports/dashboard" `
        -Method Get `
        -Headers $headers
    
    if ($dashboardResponse.success) {
        Write-Host "[OK] Dashboard retrieved!" -ForegroundColor Green
        Write-Host "Total Events: $($dashboardResponse.data.events.total)" -ForegroundColor Cyan
        Write-Host "Total Contacts: $($dashboardResponse.data.contacts.total)" -ForegroundColor Cyan
    }
} catch {
    Write-Host "[FAIL] Failed: $_" -ForegroundColor Red
}

Write-Host "`n[OK] All tests completed!" -ForegroundColor Green
