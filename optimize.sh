#!/bin/bash

# Performance Optimization Script for MedixLab

echo "🚀 Starting MedixLab Performance Optimization..."

# Clear caches
echo "📦 Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Optimize Laravel
echo "⚡ Optimizing Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Composer autoloader optimization
echo "📚 Optimizing composer autoloader..."
composer dump-autoload --optimize

echo "✅ Performance optimization complete!"
echo ""
echo "Performance improvements applied:"
echo "  ✓ Database indexes for faster queries"
echo "  ✓ Query eager loading to prevent N+1 problems"
echo "  ✓ Query pagination and limits"
echo "  ✓ Laravel configuration caching"
echo "  ✓ Route caching"
echo "  ✓ View caching"
echo "  ✓ Optimized autoloader"
echo ""
echo "For development, use: php artisan serve"
echo "For production, use: php artisan optimize:fresh"
