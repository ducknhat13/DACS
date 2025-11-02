#!/bin/bash
# set -e  # Tạm thời bỏ để script không exit khi có lỗi nhỏ

echo "=== Starting Laravel Application ==="

# Clear config cache trước (phòng khi có thay đổi env vars)
echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Generate APP_KEY nếu chưa có, rỗng, hoặc không đúng format
# PHẢI generate TRƯỚC KHI cache config
# Laravel yêu cầu format: base64:... với độ dài 32 bytes (44 chars sau base64:)
echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ] || [ ${#APP_KEY} -lt 50 ]; then
    echo "APP_KEY is missing or invalid, generating new key..."
    # Generate key - Laravel sẽ tự động lưu vào .env file
    # Nếu không có .env, sẽ tạo mới
    php artisan key:generate --force --show
    echo "APP_KEY generated successfully"
    # Reload environment để đảm bảo Laravel đọc APP_KEY mới từ .env
    # Clear config lại sau khi generate để reload APP_KEY
    php artisan config:clear || true
else
    echo "APP_KEY exists and looks valid: ${APP_KEY:0:20}..."
fi

# Chạy migration (sẽ bỏ qua nếu đã chạy rồi)
echo "Running migrations..."
php artisan migrate --force || echo "Migration completed or failed, continuing..."

# Cache config, routes, views - PHẢI sau khi generate APP_KEY
echo "Caching configuration..."
php artisan config:cache || echo "Config cache failed, continuing..."
php artisan route:cache || echo "Route cache failed, continuing..."
php artisan view:cache || echo "View cache failed, continuing..."

# Start server
echo "Starting Laravel server on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}

