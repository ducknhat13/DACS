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

# Fix: Nếu APP_KEY có chứa "APP_KEY=" prefix, remove nó
if [[ "$APP_KEY" == APP_KEY=* ]]; then
    echo "Fixing APP_KEY: removing 'APP_KEY=' prefix..."
    APP_KEY="${APP_KEY#APP_KEY=}"
    export APP_KEY
    echo "Fixed APP_KEY, new length: ${#APP_KEY}, prefix: ${APP_KEY:0:10}..."
    
    # Lưu APP_KEY đã fix vào .env để Laravel đọc
    echo "Saving fixed APP_KEY to .env file..."
    if [ -f .env ]; then
        # Nếu có APP_KEY trong .env, replace nó
        if grep -q "^APP_KEY=" .env; then
            sed -i "s/^APP_KEY=.*/APP_KEY=$APP_KEY/" .env
        else
            echo "APP_KEY=$APP_KEY" >> .env
        fi
    else
        # Tạo .env mới nếu chưa có
        echo "APP_KEY=$APP_KEY" > .env
    fi
    
    # Clear config cache sau khi fix để Laravel đọc APP_KEY mới
    echo "Clearing config cache after APP_KEY fix..."
    php artisan config:clear || true
fi

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
    APP_KEY_LENGTH=${#APP_KEY}
    echo "APP_KEY exists (length: $APP_KEY_LENGTH), first 30 chars: ${APP_KEY:0:30}..."
fi

# Chạy migration (sẽ bỏ qua nếu đã chạy rồi)
echo "Running migrations..."
php artisan migrate --force || echo "Migration completed or failed, continuing..."

# Tạo storage link để public/storage -> storage/app/public
# Cần thiết để truy cập uploaded files qua URL
echo "Creating storage link..."
php artisan storage:link || echo "Storage link already exists or failed, continuing..."

# Cache config, routes, views - PHẢI sau khi generate APP_KEY
echo "Caching configuration..."
# Đảm bảo APP_KEY có giá trị trước khi cache
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "WARNING: APP_KEY is empty before caching config!"
    php artisan config:cache || echo "Config cache failed"
else
    echo "APP_KEY is valid, proceeding with config cache..."
    php artisan config:cache || echo "Config cache failed, continuing..."
fi
php artisan route:cache || echo "Route cache failed, continuing..."
php artisan view:cache || echo "View cache failed, continuing..."

# Không clear config cache sau khi cache (sẽ mất cache)
# Laravel sẽ đọc từ config cache khi runtime

# Verify APP_KEY trước khi start
echo "Verifying APP_KEY before server start..."
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "ERROR: APP_KEY is still empty or invalid!"
    exit 1
fi

# Start server
echo "Starting Laravel server on port ${PORT:-10000}..."
echo "Final APP_KEY check (length: ${#APP_KEY}, prefix: ${APP_KEY:0:10}...)"

# Enable error logging to stderr để có thể xem trong Render logs
# Laravel sẽ log errors vào storage/logs/laravel.log, nhưng cũng output ra stderr
export LOG_CHANNEL=stderr || true

# Test Laravel có thể load được không
echo "Testing Laravel application..."
php artisan about --env=production 2>&1 || echo "Warning: artisan about failed, but continuing..."

exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}

