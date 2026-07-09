# Performance Optimization Script for MedixLab (Windows)

Write-Host "🚀 Starting MedixLab Performance Optimization..." -ForegroundColor Green
Write-Host ""

# Clear caches
Write-Host "📦 Clearing application caches..." -ForegroundColor Cyan
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
Write-Host ""

# Run migrations
Write-Host "🗄️  Running database migrations..." -ForegroundColor Cyan
php artisan migrate --force
Write-Host ""

# Optimize Laravel
Write-Host "⚡ Optimizing Laravel configuration..." -ForegroundColor Cyan
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host ""

# Composer autoloader optimization
Write-Host "📚 Optimizing composer autoloader..." -ForegroundColor Cyan
composer dump-autoload --optimize
Write-Host ""

Write-Host "✅ Performance optimization complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Performance improvements applied:" -ForegroundColor Yellow
Write-Host "  ✓ Database indexes for faster queries"
Write-Host "  ✓ Query eager loading to prevent N+1 problems"
Write-Host "  ✓ Query pagination and limits"
Write-Host "  ✓ Laravel configuration caching"
Write-Host "  ✓ Route caching"
Write-Host "  ✓ View caching"
Write-Host "  ✓ Optimized autoloader"
Write-Host ""
Write-Host "For development, use: php artisan serve" -ForegroundColor Cyan
Write-Host "For production, use: php artisan optimize:fresh" -ForegroundColor Cyan
