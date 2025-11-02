# Hướng Dẫn Deploy Laravel Project Lên Render

Tài liệu này hướng dẫn chi tiết cách deploy project Laravel Poll System lên Render.

## 📋 Mục Lục

1. [Chuẩn Bị](#chuẩn-bị)
2. [Cách 1: Deploy Tự Động Với Blueprint](#cách-1-deploy-tự-động-với-blueprint) ⭐ **Khuyến nghị**
3. [Cách 2: Deploy Thủ Công](#cách-2-deploy-thủ-công)
4. [Cấu Hình Môi Trường](#cấu-hình-môi-trường)
5. [Troubleshooting](#troubleshooting)

---

## 🚀 Chuẩn Bị

1. **Đảm bảo code đã được push lên GitHub**
   ```bash
   git add .
   git commit -m "Prepare for Render deployment"
   git push origin master
   ```

2. **Tạo tài khoản Render**
   - Truy cập: https://render.com
   - Đăng ký/Đăng nhập bằng GitHub account (khuyến nghị)

---

## 🎯 Cách 1: Deploy Tự Động Với Blueprint ⭐

### Bước 1: Vào Render Dashboard
1. Đăng nhập Render Dashboard
2. Click **"New +"** ở góc trên bên trái
3. Chọn **"Blueprint"**

### Bước 2: Kết Nối Repository
1. Click **"Connect account"** nếu chưa kết nối GitHub
2. Chọn repository: `ducknhat13/DACS`
3. Render sẽ tự động phát hiện file `render.yaml`

### Bước 3: Review và Deploy
1. Review service sẽ được tạo:
   - **Web Service** (Free tier)
   - **Lưu ý**: Database sử dụng TiDB Cloud (đã setup sẵn), không tạo PostgreSQL
2. Click **"Apply"**
3. Render sẽ tự động:
   - Clone code từ GitHub
   - Build project
   - Deploy web service

### Bước 4: Cấu Hình Environment Variables
Sau khi deploy, cần cấu hình các biến môi trường:

**Vào Web Service > Environment:**

**Database - TiDB Cloud:**
1. `DB_CONNECTION` - `mysql` (đã có sẵn)
2. `DB_HOST` - Host từ TiDB Cloud (ví dụ: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`)
3. `DB_PORT` - `4000` (port mặc định của TiDB Cloud)
4. `DB_DATABASE` - Tên database trên TiDB Cloud
5. `DB_USERNAME` - Username từ TiDB Cloud
6. `DB_PASSWORD` - Password từ TiDB Cloud
7. `MYSQL_ATTR_SSL_CA` - Để trống hoặc URL tới public CA certificate (xem phần SSL bên dưới)

**Application:**
1. `APP_URL` - URL của web service (Render tự động tạo, ví dụ: `https://dacs-web.onrender.com`)

**Mail:**
1. `MAIL_USERNAME` - Gmail của bạn
2. `MAIL_PASSWORD` - App Password từ Gmail (xem hướng dẫn bên dưới)
3. `MAIL_FROM_ADDRESS` - Email gửi đi

**OAuth (nếu có):**
1. `GOOGLE_CLIENT_ID` - Google OAuth Client ID
2. `GOOGLE_CLIENT_SECRET` - Google OAuth Client Secret
3. `GOOGLE_REDIRECT_URI` - `https://your-app.onrender.com/auth/google/callback`

**Generate APP_KEY:**
```bash
# Chạy trong Render Shell hoặc Deploy Log
php artisan key:generate
```

**Chạy Migration:**
```bash
php artisan migrate --force
```

---

## 🛠️ Cách 2: Deploy Thủ Công

**Lưu ý**: Project này sử dụng **TiDB Cloud** làm database (đã setup sẵn), không cần tạo database trên Render.

### Bước 1: Lấy Thông Tin TiDB Cloud

1. **Đăng nhập TiDB Cloud Console**: https://tidbcloud.com
2. **Vào cluster của bạn** > **Connection** tab
3. **Lưu lại thông tin**:
   - **Host**: (ví dụ: `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`)
   - **Port**: `4000`
   - **Database**: Tên database bạn đã tạo
   - **Username**: Username của bạn
   - **Password**: Password của bạn

### Bước 2: Tạo Web Service

1. **Vào Render Dashboard > New + > Web Service**
2. **Connect Repository**: Chọn `ducknhat13/DACS`
3. **Cấu hình cơ bản:**
   - **Name**: `dacs-web`
   - **Environment**: `PHP`
   - **Region**: Singapore (cùng region với database)
   - **Branch**: `master`
   - **Root Directory**: `.` (để trống)

4. **Build Command:**
```bash
composer install --no-dev --optimize-autoloader && php artisan config:cache && php artisan route:cache && php artisan view:cache && npm ci && npm run build
```

5. **Start Command:**
```bash
php artisan serve --host=0.0.0.0 --port=$PORT
```

6. **Environment Variables:**
Thêm các biến sau (Settings > Environment):

```
# Application
APP_NAME=DACS Poll System
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

# Database - TiDB Cloud
DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
MYSQL_ATTR_SSL_CA=

# Laravel Key (sẽ generate sau)
APP_KEY=base64:...

# Cache & Session
CACHE_STORE=file
SESSION_DRIVER=database
QUEUE_CONNECTION=sync

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Google OAuth (nếu có)
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=https://your-app.onrender.com/auth/google/callback
```

7. Click **"Create Web Service"**

### Bước 3: Cấu Hình SSL Cho TiDB Cloud

**TiDB Cloud yêu cầu SSL connection.** Trên Render, có 2 cách:

**Cách 1: Để trống MYSQL_ATTR_SSL_CA (Khuyến nghị)**
- Để `MYSQL_ATTR_SSL_CA` trống trong Environment Variables
- Laravel sẽ tự động verify SSL với system CA certificates

**Cách 2: Download Certificate khi Build**
Nếu Cách 1 không hoạt động, thêm vào build command:
```bash
curl -o /tmp/cacert.pem https://curl.se/ca/cacert.pem && 
composer install --no-dev --optimize-autoloader &&
php artisan config:cache &&
php artisan route:cache &&
php artisan view:cache &&
npm ci &&
npm run build
```
Và set `MYSQL_ATTR_SSL_CA=/tmp/cacert.pem`

### Bước 4: Chạy Migration và Setup

**Sau khi deploy thành công:**

1. **Vào Deploy Log** và tìm Shell icon hoặc dùng **Manual Deploy**
2. Chạy các lệnh:

```bash
# Generate APP_KEY nếu chưa có
php artisan key:generate --force

# Test kết nối database
php artisan db:show

# Chạy migration
php artisan migrate --force

# (Optional) Seed database nếu có
php artisan db:seed --force
```

---

## ⚙️ Cấu Hình Môi Trường

### 🔥 Cấu Hình TiDB Cloud Network Access

**QUAN TRỌNG**: TiDB Cloud cần cho phép kết nối từ Render.

1. **Đăng nhập TiDB Cloud Console**: https://tidbcloud.com
2. **Vào cluster của bạn** (click vào tên cluster)
3. **Vào Settings > Networking**:
   - Ở menu bên trái, chọn **"Settings"** > **"Networking"**
   - Hoặc có thể là **"Security"** > **"Network Access"** (tùy version)
4. **Kích hoạt Public Endpoint** (nếu chưa bật):
   - Đảm bảo **"Public Endpoint"** đã được bật
5. **Thêm địa chỉ IP vào Authorized Networks**:
   - Trong phần **"Authorized Networks"**, click **"+ Add Current IP"** (nếu đang từ máy local)
   - Để thêm IP của Render, click **"Add rule"** hoặc **"+ Add IP"**
   - **Option 1**: Thêm IP cụ thể của Render (nếu biết - nhưng Render không có IP tĩnh)
   - **Option 2**: Thêm `0.0.0.0/0` để cho phép tất cả IP (chỉ dùng cho development/demo)
   - **Option 3**: Thêm IP range nếu có

6. **Lưu ý**:
   - Render không có IP tĩnh, IP có thể thay đổi sau mỗi lần deploy
   - Đối với production, nên hạn chế chỉ cho phép IP cụ thể
   - Đối với development/demo, có thể tạm thời dùng `0.0.0.0/0`
   - Sau khi thêm IP, kết nối sẽ có hiệu lực ngay lập tức

### 📧 Cấu Hình Gmail SMTP

1. **Tạo App Password trong Gmail:**
   - Vào: https://myaccount.google.com/apppasswords
   - Hoặc: Google Account > Security > 2-Step Verification > App Passwords
   - Tạo app password mới cho "Mail"
   - Copy password (16 ký tự)

2. **Cấu hình trong Render:**
   - `MAIL_USERNAME`: Gmail của bạn
   - `MAIL_PASSWORD`: App Password vừa tạo
   - `MAIL_FROM_ADDRESS`: Gmail của bạn

### 🔐 Google OAuth (Nếu có)

1. **Tạo OAuth Credentials:**
   - Vào: https://console.cloud.google.com/apis/credentials
   - Tạo OAuth 2.0 Client ID
   - **Authorized redirect URIs**: `https://your-app.onrender.com/auth/google/callback`

2. **Cấu hình trong Render:**
   - `GOOGLE_CLIENT_ID`: Client ID từ Google Console
   - `GOOGLE_CLIENT_SECRET`: Client Secret
   - `GOOGLE_REDIRECT_URI`: URL callback đầy đủ

---

## 🔍 Troubleshooting

### ❌ Build Failed

**Vấn đề**: Build command thất bại

**Giải pháp**:
1. Kiểm tra Deploy Log để xem lỗi cụ thể
2. Đảm bảo `composer.json` và `package.json` hợp lệ
3. Thử build lại: Settings > Manual Deploy > Deploy latest commit

### ❌ APP_KEY chưa được generate

**Vấn đề**: Lỗi "No application encryption key"

**Giải pháp**:
```bash
# Trong Render Shell hoặc Deploy Log
php artisan key:generate --force
```

### ❌ Database Connection Failed

**Vấn đề**: Không kết nối được TiDB Cloud

**Giải pháp**:
1. **Kiểm tra thông tin kết nối**:
   ```bash
   # Test connection trong Render Shell
   php artisan db:show
   ```

2. **Kiểm tra Environment Variables**:
   - `DB_HOST`: Đúng host từ TiDB Cloud (không có protocol)
   - `DB_PORT`: `4000`
   - `DB_USERNAME`, `DB_PASSWORD`: Đúng credentials
   - `DB_DATABASE`: Tên database đúng

3. **Kiểm tra TiDB Cloud Network Access**:
   - Vào TiDB Cloud Console > Cluster > **Settings** > **Networking**
   - Kiểm tra phần **"Authorized Networks"**
   - Đảm bảo đã thêm IP (hoặc `0.0.0.0/0` để test)
   - Render IP có thể thay đổi, nên có thể cần allow `0.0.0.0/0` tạm thời

4. **Kiểm tra SSL**:
   - Nếu lỗi SSL, thử để `MYSQL_ATTR_SSL_CA` trống
   - Hoặc thêm certificate vào build command như hướng dẫn ở trên

5. **Kiểm tra Network**:
   - Render và TiDB Cloud phải có kết nối internet
   - Port 4000 phải được mở trong TiDB Cloud Network Access settings
   - Đảm bảo Public Endpoint đã được enable

### ❌ Migration Failed

**Vấn đề**: Lỗi khi chạy migration

**Giải pháp**:
```bash
# Chạy lại migration
php artisan migrate --force

# Nếu có conflict, rollback trước
php artisan migrate:rollback --force
php artisan migrate --force
```

### ❌ Assets không load (CSS/JS)

**Vấn đề**: Vite assets không được build

**Giải pháp**:
1. Kiểm tra build command có `npm run build`
2. Đảm bảo `vite.config.js` cấu hình đúng
3. Kiểm tra `APP_URL` có đúng domain không

### ⏸️ Service bị Sleep (Free Tier)

**Vấn đề**: Web service ngủ sau 15 phút không hoạt động

**Giải pháp**:
- Free tier sẽ tự động sleep sau 15 phút không có request
- Lần request đầu tiên sau khi sleep sẽ mất ~30-60 giây để wake up
- Upgrade lên Paid plan để tránh sleep (không khuyến nghị cho project nhỏ)

### 🔄 Auto-Deploy không hoạt động

**Vấn đề**: Không tự động deploy khi push code

**Giải pháp**:
1. Kiểm tra Settings > Auto-Deploy: `Yes`
2. Kiểm tra Branch: phải là branch bạn đang push
3. Kiểm tra GitHub webhook: Render tự động tạo, nhưng có thể kiểm tra trong GitHub repo Settings > Webhooks

---

## 📝 Lưu Ý Quan Trọng

### Free Tier Limitations:
- ⏸️ **Sleep Mode**: Service sẽ sleep sau 15 phút không hoạt động
- 🐌 **Cold Start**: Lần đầu truy cập sau khi sleep sẽ chậm (~30-60s)
- 💾 **Database**: 90MB PostgreSQL storage (đủ cho project nhỏ)
- 🚀 **Build Time**: ~5-10 phút mỗi lần deploy

### Security Best Practices:
- ✅ **APP_DEBUG**: Luôn set `false` trong production
- ✅ **APP_KEY**: Phải được generate và giữ bí mật
- ✅ **Database**: Dùng Internal Database URL (không phải External)
- ✅ **Environment Variables**: Không commit vào Git

### Performance Optimization:
- ✅ Cache config, routes, views (đã có trong build command)
- ✅ Optimize autoloader (`--optimize-autoloader`)
- ✅ Build assets trước khi deploy (`npm run build`)

---

## 🎉 Hoàn Thành!

Sau khi deploy thành công:
1. ✅ Web service sẽ có URL: `https://your-app.onrender.com`
2. ✅ Database đã được tạo và migrate
3. ✅ Email đã được cấu hình
4. ✅ Ứng dụng sẵn sàng sử dụng!

**Lưu ý**: Free tier sẽ sleep sau 15 phút, nên lần đầu truy cập sau khi sleep sẽ mất thời gian để wake up.

---

## 📚 Tài Liệu Tham Khảo

- [Render Documentation](https://render.com/docs)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Render PostgreSQL](https://render.com/docs/databases)

**Cần hỗ trợ?** Kiểm tra Deploy Log trong Render Dashboard để xem chi tiết lỗi.

