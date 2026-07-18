# Firebase Setup Helper Script

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Firebase Setup Guide" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Follow these steps to set up Firebase:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Go to: https://console.firebase.google.com/" -ForegroundColor White
Write-Host "2. Click 'Add Project' or select existing project" -ForegroundColor White
Write-Host "3. Enter project name: chandla-book (or your choice)" -ForegroundColor White
Write-Host "4. Click 'Continue' → 'Create Project'" -ForegroundColor White
Write-Host ""
Write-Host "5. Go to Project Settings (gear icon)" -ForegroundColor White
Write-Host "6. Click 'Service Accounts' tab" -ForegroundColor White
Write-Host "7. Click 'Generate New Private Key'" -ForegroundColor White
Write-Host "8. Download the JSON file" -ForegroundColor White
Write-Host ""
Write-Host "9. Save the file as: storage/app/firebase-credentials.json" -ForegroundColor White
Write-Host ""
Write-Host "10. Get Project ID from Firebase Console → Settings → General" -ForegroundColor White
Write-Host "11. Update .env file:" -ForegroundColor White
Write-Host "    FIREBASE_PROJECT_ID=your-project-id" -ForegroundColor Cyan
Write-Host ""

$continue = Read-Host "Have you completed these steps? (y/n)"
if ($continue -eq 'y' -or $continue -eq 'Y') {
    Write-Host "`n✓ Firebase setup instructions provided" -ForegroundColor Green
    Write-Host "Please ensure firebase-credentials.json is in storage/app/" -ForegroundColor Yellow
} else {
    Write-Host "`nPlease complete the steps above and run this script again" -ForegroundColor Yellow
}

