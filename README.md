# QuickPoll - Ứng Dụng Tạo Khảo Sát Nhanh

<p align="center">
    <div style="font-family: 'Product Sans', sans-serif; font-size: 48px; font-weight: bold; text-align: center; margin: 20px 0;">
        <span style="color: #2563eb;">Q</span><span style="color: #2563eb;">u</span><span style="color: #2563eb;">i</span><span style="color: #2563eb;">c</span><span style="color: #2563eb;">k</span><span style="color: #ef4444;">P</span><span style="color: #eab308;">o</span><span style="color: #3b82f6;">l</span><span style="color: #22c55e;">l</span>
    </div>
</p>

## 📝 Mô Tả

QuickPoll là một ứng dụng web được xây dựng bằng Laravel cho phép người dùng tạo và quản lý các cuộc khảo sát trực tuyến một cách nhanh chóng và dễ dàng. Ứng dụng hỗ trợ nhiều loại câu hỏi khác nhau, bảo mật cao và giao diện thân thiện.

## ✨ Tính Năng Chính

### 🔐 Xác Thực & Bảo Mật
- **Đăng ký/Đăng nhập**: Hệ thống xác thực hoàn chỉnh với Laravel Breeze
- **Bảo mật poll**: Hỗ trợ poll riêng tư với mã truy cập
- **Kiểm soát quyền truy cập**: Chỉ người tạo mới có thể quản lý poll của mình

### 📊 Tạo & Quản Lý Poll
- **Tạo poll nhanh**: Giao diện thân thiện để tạo poll trong vài phút
- **Nhiều loại câu hỏi**: 
  - Poll thông thường (single choice)
  - Poll ranking (xếp hạng)
- **Tùy chọn nâng cao**:
  - Cho phép chọn nhiều đáp án
  - Tự động đóng poll theo thời gian
  - Ẩn kết quả khi đang vote
  - Cho phép bình luận

### 🗳️ Hệ Thống Vote
- **Vote an toàn**: Kiểm soát session để tránh vote nhiều lần
- **Thu thập thông tin**: Yêu cầu tên người vote (cho poll riêng tư)
- **Kết quả real-time**: Hiển thị kết quả ngay lập tức

### 📈 Quản Lý & Phân Tích
- **Dashboard**: Tổng quan tất cả poll đã tạo
- **Lọc & Tìm kiếm**: Tìm poll theo tên, trạng thái
- **Xuất dữ liệu**: Export kết quả ra file CSV
- **Thống kê chi tiết**: Số lượng vote, bình luận

### 🌐 Đa Ngôn Ngữ
- **Hỗ trợ tiếng Việt**: Giao diện đầy đủ tiếng Việt
- **Chuyển đổi ngôn ngữ**: Dễ dàng chuyển đổi giữa tiếng Việt và tiếng Anh

## 🛠️ Công Nghệ Sử Dụng

### Backend
- **Laravel 12**: Framework PHP hiện đại
- **PHP 8.2+**: Phiên bản PHP mới nhất
- **SQLite**: Cơ sở dữ liệu nhẹ, dễ triển khai
- **Laravel Breeze**: Authentication scaffolding

### Frontend
- **Tailwind CSS 4**: Framework CSS utility-first
- **Alpine.js**: JavaScript framework nhẹ
- **Vite**: Build tool hiện đại
- **Responsive Design**: Tương thích mọi thiết bị

## 📋 Yêu Cầu Hệ Thống

- **PHP**: >= 8.2
- **Composer**: Để quản lý dependencies PHP
- **Node.js**: >= 18.x (để build frontend assets)
- **npm/yarn**: Package manager cho JavaScript

## 🚀 Hướng Dẫn Cài Đặt

### 1. Clone Repository

```bash
git clone <repository-url>
cd dacs_quickpoll
```

### 2. Cài Đặt Dependencies

```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node.js dependencies
npm install
```

### 3. Cấu Hình Môi Trường

```bash
# Copy file environment
cp .env.example .env

# Tạo application key
php artisan key:generate
```

### 4. Cấu Hình Database

```bash
# Tạo file SQLite database (nếu chưa có)
touch database/database.sqlite

# Chạy migrations
php artisan migrate
```

### 5. Build Frontend Assets

```bash
# Build assets cho production
npm run build

# Hoặc chạy development server
npm run dev
```

## 🏃‍♂️ Chạy Ứng Dụng

### Development Mode

```bash
# Chạy Laravel development server
php artisan serve

# Chạy Vite development server (terminal khác)
npm run dev

# Hoặc chạy tất cả cùng lúc
composer run dev
```

Ứng dụng sẽ chạy tại: `http://localhost:8000`

### Production Mode

```bash
# Build assets
npm run build

# Chạy server
php artisan serve
```

## 📖 Hướng Dẫn Sử Dụng

### 1. Đăng Ký & Đăng Nhập
- Truy cập trang chủ và click "Register" để tạo tài khoản
- Hoặc "Log in" nếu đã có tài khoản

### 2. Tạo Poll Mới
1. Đăng nhập vào hệ thống
2. Click "Create New Poll" trên dashboard
3. Điền thông tin:
   - **Câu hỏi**: Nội dung câu hỏi khảo sát
   - **Loại poll**: Thông thường hoặc Ranking
   - **Các lựa chọn**: Thêm các phương án trả lời
   - **Cài đặt nâng cao**: Bảo mật, thời gian đóng, v.v.

### 3. Chia Sẻ & Vote
1. Sau khi tạo poll, copy link chia sẻ
2. Gửi link cho người tham gia
3. Người tham gia truy cập và vote
4. Xem kết quả real-time

### 4. Quản Lý Poll
- **Dashboard**: Xem tất cả poll đã tạo
- **Tìm kiếm**: Lọc poll theo tên hoặc trạng thái
- **Thống kê**: Xem số lượng vote, bình luận
- **Export**: Xuất kết quả ra file CSV

## 🔧 Cấu Hình Nâng Cao

### Environment Variables

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Application
APP_NAME="QuickPoll"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Mail (tùy chọn)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

### Customization

- **Giao diện**: Chỉnh sửa trong `resources/views/`
- **Styling**: Sửa đổi `resources/css/app.css` và Tailwind config
- **Logic**: Tùy chỉnh trong `app/Http/Controllers/`

## 🧪 Testing

```bash
# Chạy tất cả tests
php artisan test

# Chạy test cụ thể
php artisan test --filter=FeatureTest
```

## 📁 Cấu Trúc Project

```
dacs_quickpoll/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/              # Eloquent Models
│   ├── Events/              # Event Classes
│   └── Support/             # Helper Classes
├── database/
│   ├── migrations/          # Database Migrations
│   ├── factories/           # Model Factories
│   └── seeders/            # Database Seeders
├── resources/
│   ├── views/              # Blade Templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript
├── routes/
│   ├── web.php             # Web Routes
│   └── auth.php            # Auth Routes
└── public/                 # Public Assets
```

## 🤝 Đóng Góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📝 License

Dự án này được phân phối dưới [MIT License](https://opensource.org/licenses/MIT).

## 👥 Tác Giả

- **DACS Team** - *Initial work*

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- Tất cả contributors và người dùng

---

**Lưu ý**: Đây là phiên bản development. Để deploy production, hãy tham khảo [Laravel Deployment Guide](https://laravel.com/docs/deployment).