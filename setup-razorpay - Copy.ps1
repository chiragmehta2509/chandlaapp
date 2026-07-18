# Razorpay Setup Helper Script

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Razorpay Setup Guide" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Follow these steps to set up Razorpay:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Go to: https://razorpay.com/" -ForegroundColor White
Write-Host "2. Sign up for an account" -ForegroundColor White
Write-Host "3. Complete email verification" -ForegroundColor White
Write-Host ""
Write-Host "4. Login to Razorpay Dashboard" -ForegroundColor White
Write-Host "5. Go to Settings → API Keys" -ForegroundColor White
Write-Host "6. Click 'Generate Test Key' (for development)" -ForegroundColor White
Write-Host "7. Copy the Key ID and Key Secret" -ForegroundColor White
Write-Host ""
Write-Host "8. Update .env file:" -ForegroundColor White
Write-Host "    RAZORPAY_KEY_ID=rzp_test_xxxxxxxxxxxxx" -ForegroundColor Cyan
Write-Host "    RAZORPAY_KEY_SECRET=your_key_secret_here" -ForegroundColor Cyan
Write-Host ""
Write-Host "Note:" -ForegroundColor Yellow
Write-Host "- Test keys start with 'rzp_test_'" -ForegroundColor White
Write-Host "- Live keys start with 'rzp_live_'" -ForegroundColor White
Write-Host "- Use test keys for development" -ForegroundColor White
Write-Host ""

$continue = Read-Host "Have you completed these steps? (y/n)"
if ($continue -eq 'y' -or $continue -eq 'Y') {
    Write-Host "`n✓ Razorpay setup instructions provided" -ForegroundColor Green
    Write-Host "Please ensure API keys are added to .env file" -ForegroundColor Yellow
} else {
    Write-Host "`nPlease complete the steps above and run this script again" -ForegroundColor Yellow
}

