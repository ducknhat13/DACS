#!/bin/bash
# set -e  # Tạm thời bỏ để script không exit khi có lỗi nhỏ

echo "=== Starting Laravel Application ==="

# Clear config cache trước (phòng khi có thay đổi env vars)
echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Generate APP_KEY nếu chưa có hoặc rỗng
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Chạy migration (sẽ bỏ qua nếu đã chạy rồi)
echo "Running migrations..."
php artisan migrate --force || echo "Migration completed or failed, continuing..."

# Cache config, routes, views
echo "Caching configuration..."
php artisan config:cache || echo "Config cache failed, continuing..."
php artisan route:cache || echo "Route cache failed, continuing..."
php artisan view:cache || echo "View cache failed, continuing..."

# Start server
echo "Starting Laravel server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}

