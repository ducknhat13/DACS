# QuickPoll - Ứng Dụng Tạo Khảo Sát Nhanh

<p align="center">
    <div style="font-family: 'Product Sans', sans-serif; font-size: 48px; font-weight: bold; text-align: center; margin: 20px 0;">
        <span style="color: #2563eb;">Q</span><span style="color: #2563eb;">u</span><span style="color: #2563eb;">i</span><span style="color: #2563eb;">c</span><span style="color: #2563eb;">k</span><span style="color: #ef4444;">P</span><span style="color: #eab308;">o</span><span style="color: #3b82f6;">l</span><span style="color: #22c55e;">l</span>
    </div>
</p>

## 📝 Mô Tả

QuickPoll là một ứng dụng web được xây dựng bằng Laravel cho phép người dùng tạo và quản lý các cuộc khảo sát trực tuyến một cách nhanh chóng và dễ dàng. Ứng dụng hỗ trợ nhiều loại câu hỏi khác nhau, bảo mật cao và giao diện thân thiện theo Material Design 3.

## ✨ Tính Năng Chính

### 🔐 Xác Thực & Bảo Mật
- **Đăng ký/Đăng nhập**: Hệ thống xác thực hoàn chỉnh với Laravel Breeze
- **Bảo mật poll**: Hỗ trợ poll riêng tư với mã truy cập
- **Kiểm soát quyền truy cập**: Chỉ người tạo mới có thể quản lý poll của mình
- **Quick Access**: Truy cập nhanh poll bằng slug từ header

### 📊 Tạo & Quản Lý Poll
- **Tạo poll nhanh**: Giao diện Material Design thân thiện
- **Nhiều loại câu hỏi**: 
  - Poll thông thường (single choice)
  - Poll ranking (xếp hạng)
- **Tùy chọn nâng cao**:
  - Cho phép chọn nhiều đáp án
  - Tự động đóng poll theo thời gian
  - Ẩn nút chia sẻ
  - Cho phép bình luận

### 🗳️ Hệ Thống Vote
- **Vote an toàn**: Kiểm soát session để tránh vote nhiều lần
- **Thu thập thông tin**: Yêu cầu tên người vote (cho poll riêng tư)
- **Kết quả real-time**: Hiển thị kết quả với biểu đồ và thống kê chi tiết

### 📈 Quản Lý & Phân Tích
- **Dashboard Material Design**: Tổng quan tất cả poll đã tạo
- **Lọc & Tìm kiếm**: Tìm poll theo tên, trạng thái
- **Xuất dữ liệu**: Export kết quả ra file CSV
- **Thống kê chi tiết**: Số lượng vote, bình luận, biểu đồ tròn

### 🌐 Đa Ngôn Ngữ
- **Hỗ trợ tiếng Việt**: Giao diện đầy đủ tiếng Việt
- **Chuyển đổi ngôn ngữ**: Dễ dàng chuyển đổi giữa tiếng Việt và tiếng Anh

## 🛠️ Công Nghệ Sử Dụng

### Backend
- **Laravel 12**: Framework PHP hiện đại
- **PHP 8.2+**: Phiên bản PHP mới nhất
- **TiDB Cloud**: Cơ sở dữ liệu MySQL tương thích
- **Laravel Breeze**: Authentication scaffolding

### Frontend
- **Tailwind CSS 4**: Framework CSS utility-first
- **Material Design 3**: Giao diện theo chuẩn Google
- **Alpine.js**: JavaScript framework nhẹ
- **Vite**: Build tool hiện đại
- **Chart.js**: Biểu đồ tương tác
- **FontAwesome**: Icon library
- **Responsive Design**: Tương thích mọi thiết bị

## 📋 Yêu Cầu Hệ Thống

- **PHP**: >= 8.2
- **Composer**: Để quản lý dependencies PHP
- **Node.js**: >= 18.x (để build frontend assets)
- **npm/yarn**: Package manager cho JavaScript
- **XAMPP/WAMP**: Môi trường phát triển local
- **TiDB Cloud Account**: Để sử dụng database cloud

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

#### 4.2. Cấu Hình Environment Variables

Chỉnh sửa file `.env`:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=[Host của TiDB Cloud]
DB_PORT=4000
DB_DATABASE=quickpoll
DB_USERNAME=[User của TiDB Cloud]
DB_PASSWORD=[Password của TiDB Cloud]

# SSL Certificate (bắt buộc cho TiDB Cloud)
MYSQL_ATTR_SSL_CA="C:/xampp/php/certs/cacert.pem"

# Application Settings
APP_NAME=Laravel
APP_ENV=local
APP_KEY= ...
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file


### 5. Cài Đặt Chứng Chỉ SSL

#### 5.1. Tải Certificate
 Tải file `cacert.pem` từ [Mozilla CA Certificate Store](https://curl.se/ca/cacert.pem)


#### 5.2. Cài Đặt Certificate
1. Tạo thư mục `certs` trong `C:/xampp/php/`
2. Copy file `cacert.pem` vào `C:/xampp/php/certs/cacert.pem`

```bash
# Tạo thư mục (nếu chưa có)
mkdir C:/xampp/php/certs

# Copy certificate file
copy cacert.pem C:/xampp/php/certs/cacert.pem
```


**Lưu ý quan trọng**: phải tải file `cacert.pem` và lưu nó vào chính xác cùng một đường dẫn `C:/xampp/php/certs/cacert.pem` trên máy để kết nối được với TiDB Cloud.

### 6. Chạy Migrations

```bash
# Chạy migrations để tạo bảng
php artisan migrate

# Tạo dữ liệu mẫu (tùy chọn)
php artisan db:seed
```

### 7. Build Frontend Assets

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
2. Click nút "Create New Poll" (FAB) hoặc từ navigation
3. Điền thông tin theo Material Design form:
   - **Câu hỏi**: Nội dung câu hỏi khảo sát
   - **Loại poll**: Thông thường hoặc Ranking
   - **Các lựa chọn**: Thêm các phương án trả lời
   - **Cài đặt nâng cao**: Bảo mật, thời gian đóng, v.v.

### 3. Chia Sẻ & Vote
1. Sau khi tạo poll, sử dụng Quick Access trong header
2. Hoặc copy link chia sẻ từ trang kết quả
3. Người tham gia truy cập và vote
4. Xem kết quả real-time với biểu đồ

### 4. Quản Lý Poll
- **Dashboard**: Xem tất cả poll đã tạo với Material Design cards
- **Tìm kiếm**: Lọc poll theo tên hoặc trạng thái
- **Thống kê**: Xem số lượng vote, bình luận
- **Export**: Xuất kết quả ra file CSV

## 🔧 Cấu Hình Nâng Cao

### Database Connection với SSL

Để kết nối an toàn với TiDB Cloud, đảm bảo:

1. **SSL Certificate**: File `cacert.pem` được cài đặt đúng vị trí
2. **Environment Variables**: Cấu hình đầy đủ trong `.env`
3. **Firewall**: Port 4000 được mở cho TiDB Cloud
4. **Network**: Kết nối internet ổn định

### Customization

- **Giao diện**: Chỉnh sửa trong `resources/views/`
- **Styling**: Sửa đổi `resources/css/app.css` và Material Design variables
- **Logic**: Tùy chỉnh trong `app/Http/Controllers/`
- **Database**: Thay đổi migrations trong `database/migrations/`

## 🧪 Testing

```bash
# Chạy tất cả tests
php artisan test

# Chạy test cụ thể
php artisan test --filter=FeatureTest

# Chạy test với coverage
php artisan test --coverage
```

## 📁 Cấu Trúc Project

```
dacs_quickpoll/
├── app/
│   ├── Http/Controllers/     # Controllers (Poll, Vote, Auth, Stats, Contact)
│   │   ├── PollController.php    # Quản lý Poll: create, store, vote, show, export
│   │   ├── VoteController.php    # Xử lý voting: store, handleRankingVote, handleRegularVote
│   │   ├── StatsController.php   # Thống kê và History page
│   │   ├── ContactController.php # Contact form submission
│   │   └── ProfileController.php # User profile management
│   ├── Models/              # Eloquent Models
│   │   ├── Poll.php             # Poll model với relationships và helper methods
│   │   ├── Vote.php            # Vote model
│   │   ├── PollOption.php      # PollOption model
│   │   └── User.php            # User model với notification preferences
│   ├── Notifications/       # Email Notifications
│   │   ├── NewVoteNotification.php          # Email khi có vote mới
│   │   └── PollClosingReminderNotification.php # Email nhắc nhở trước khi đóng poll
│   ├── Mail/                # Mailable Classes
│   │   └── ContactMail.php  # Email từ contact form
│   ├── Events/              # Event Classes
│   ├── Support/             # Helper Classes
│   │   └── PollCode.php     # Generate unique poll slugs
│   └── Http/Middleware/     # Custom Middleware
│       └── EnsurePollAccess.php # Check access key cho private polls
├── database/
│   ├── migrations/          # Database Migrations
│   ├── factories/           # Model Factories
│   └── seeders/            # Database Seeders
├── resources/
│   ├── views/              # Blade Templates (Material Design)
│   │   ├── layouts/        # Layout components (app, guest, navigation, footer)
│   │   ├── polls/          # Poll-related views (create, vote, show, name, access)
│   │   ├── profile/        # Profile views (edit)
│   │   ├── stats/          # Statistics/History page
│   │   └── components/     # Reusable components
│   ├── css/                # Stylesheets (Tailwind + Material Design)
│   ├── js/                 # JavaScript (Alpine.js, Chart.js)
│   └── lang/               # Localization files (en, vi)
│       ├── en/messages.php
│       └── vi/messages.php
├── routes/
│   ├── web.php             # Web Routes (có comments chi tiết)
│   └── auth.php            # Auth Routes (Laravel Breeze)
├── public/                 # Public Assets
│   └── Logo.png           # Application Logo
└── config/                # Configuration Files
```

## 📝 Code Documentation & Comments

Project này đã được **documented đầy đủ** với comments tiếng Việt để bạn có thể hiểu code dễ dàng:

### ✅ Controllers (Đã comment đầy đủ)

Tất cả Controllers đều có:
- **Header comments**: Giải thích chức năng tổng quan của Controller
- **Method docblocks**: Mô tả từng method, parameters, return types
- **Inline comments**: Giải thích logic phức tạp và business rules

**Đã comment:**
- ✅ `PollController`: Quản lý vòng đời Poll (create → store → vote → show → export)
- ✅ `VoteController`: Xử lý voting logic cho 3 loại poll (standard, ranking, image)
- ✅ `StatsController`: Tính toán thống kê và charts data
- ✅ `ContactController`: Xử lý contact form submission
- ✅ `ProfileController`: User profile management (locale, notifications, delete account)
- ✅ `ImageUploadController`: Upload và validate media files
- ✅ `GoogleAuthController`: Google OAuth authentication và account linking

### ✅ Models (Đã comment đầy đủ)

Models có:
- **Header comments**: Mô tả model, relationships, đặc điểm
- **Method comments**: Giải thích các helper methods và accessors

**Đã comment:**
- ✅ `Poll`: Model chính với relationships, helper methods (getParticipantsCountAttribute, getMaxSelections)
- ✅ `User`: Authentication, notification preferences, OAuth support
- ✅ `Vote`: Vote tracking với voter_identifier và rank
- ✅ `PollOption`: Options với image support và helper methods
- ✅ `Comment`: Comment model với user tracking

### ✅ Routes (Đã comment đầy đủ)

File `routes/web.php` có:
- **Header comment**: Giải thích cấu trúc routes
- **Inline comments**: Nhóm routes theo chức năng (Public, Auth, Poll, etc.)

### ✅ Notifications & Mail (Đã comment đầy đủ)

- ✅ `NewVoteNotification`: Email khi có vote mới
- ✅ `PollClosingReminderNotification`: Email nhắc nhở trước khi đóng poll
- ✅ `ContactMail`: Mailable cho contact form emails

### ✅ Middleware & Support (Đã comment đầy đủ)

- ✅ `EnsurePollAccess`: Kiểm tra quyền truy cập poll (private polls)
- ✅ `SetLocale`: Set application locale cho mỗi request
- ✅ `PollCode`: Helper class để generate unique poll slugs

### ✅ Migrations (Đã comment đầy đủ)

Các migrations quan trọng đã có comments giải thích:
- ✅ `create_polls_table`: Bảng polls chính
- ✅ `create_poll_options_table`: Bảng options
- ✅ `create_votes_table`: Bảng votes với indexes và constraints
- ✅ `create_comments_table`: Bảng comments
- ✅ `add_poll_type_to_polls_table`: Thêm poll_type enum
- ✅ `add_rank_to_votes_table`: Thêm rank cho ranking polls
- ✅ `add_image_support_to_polls_and_options`: Image poll support
- ✅ `add_notification_preferences_to_users_table`: Notification settings

### ✅ Config Files (Đã comment đầy đủ)

- ✅ `config/mail.php`: SMTP configuration với examples
- ✅ `config/app.php`: Application config (social links đã có comments)

### ✅ JavaScript & CSS (Đã comment đầy đủ)

- ✅ `resources/js/app.js`: Main entry point với Alpine.js setup
- ✅ `resources/css/app.css`: Material Design 3 styles và utilities

### Code Conventions

Khi đọc code, bạn sẽ thấy:
- **Tên biến rõ ràng**: `$voterIdentifier`, `$hasVoted`, `$isOwner`
- **Comments giải thích "tại sao"**: Không chỉ "làm gì" mà còn giải thích logic
- **Docblocks đầy đủ**: Parameters và return types được document
- **Tiếng Việt**: Tất cả comments bằng tiếng Việt để dễ hiểu

### Cách đọc code cho người mới

1. **Bắt đầu với Routes** (`routes/web.php`):
   - Xem route nào gọi controller nào
   - Hiểu flow: URL → Route → Controller → View

2. **Đọc Controllers**:
   - Mỗi method xử lý 1 request cụ thể
   - Comments giải thích từng bước xử lý
   - Header comments mô tả tổng quan

3. **Hiểu Models**:
   - Relationships giữa các models
   - Helper methods và accessors
   - Database schema qua migrations

4. **Xem Views**:
   - Blade templates với localization
   - Material Design 3 components

## 🎨 Material Design Features

- **Color Palette**: Primary #176BEF, Success #179C52, Error #FF3E30
- **Typography**: Product Sans, Montserrat fonts
- **Components**: Cards, Buttons, Inputs, Navigation theo Material Design 3
- **Animations**: Smooth transitions và hover effects
- **Responsive**: Mobile-first design approach

## 🤝 Đóng Góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📝 License

Dự án này được phân phối dưới [MIT License](https://opensource.org/licenses/MIT).

## 👥 Tác Giả

- **Nguyen Duc Nhat** - 22010225

## 🙏 Acknowledgments

- Laravel Framework
- TiDB Cloud
- Material Design 3
- Tailwind CSS
- Alpine.js
- Chart.js
- Tất cả contributors và người dùng

 
