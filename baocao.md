# BÁO CÁO BÀI TẬP LỚN - QUICKPOLL SYSTEM

## 1. GIỚI THIỆU VỀ PROJECT

### 1.1 Tổng quan
**QuickPoll** là một ứng dụng web được xây dựng bằng Laravel cho phép người dùng tạo và quản lý các cuộc khảo sát trực tuyến một cách nhanh chóng và dễ dàng. Ứng dụng hỗ trợ nhiều loại câu hỏi khác nhau, bảo mật cao và giao diện thân thiện theo Material Design 3.

### 1.2 Tính năng chính
- **🔐 Xác thực & Bảo mật**: Hệ thống đăng ký/đăng nhập hoàn chỉnh với Laravel Breeze
- **📊 Tạo & Quản lý Poll**: Hỗ trợ 3 loại poll (Standard, Ranking, Image)
- **🗳️ Hệ thống Vote**: Vote an toàn với kiểm soát session
- **📈 Phân tích & Báo cáo**: Dashboard với thống kê chi tiết và export CSV
- **🔒 Bảo mật Poll**: Hỗ trợ poll riêng tư với mã truy cập
- **📱 Responsive Design**: Tương thích mọi thiết bị
- **🌐 Đa ngôn ngữ**: Hỗ trợ tiếng Việt và tiếng Anh

### 1.3 Công nghệ sử dụng
- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze
- **UI Framework**: Material Design 3

## 2. CẤU TRÚC DATABASE

### 2.1 Bảng Users
```sql
users
├── id (Primary Key)
├── name (VARCHAR)
├── email (VARCHAR, UNIQUE)
├── email_verified_at (TIMESTAMP, NULLABLE)
├── password (VARCHAR)
├── remember_token (VARCHAR, NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### 2.2 Bảng Polls
```sql
polls
├── id (Primary Key)
├── user_id (Foreign Key → users.id)
├── title (VARCHAR)
├── description (TEXT, NULLABLE)
├── description_media (JSON, NULLABLE)
├── question (TEXT)
├── slug (VARCHAR, UNIQUE)
├── poll_type (ENUM: 'standard', 'ranking', 'image')
├── max_choices (INTEGER, NULLABLE)
├── max_image_selections (INTEGER, NULLABLE)
├── allow_multiple (BOOLEAN)
├── is_closed (BOOLEAN)
├── is_private (BOOLEAN)
├── access_key (VARCHAR, NULLABLE)
├── voting_security (VARCHAR)
├── auto_close_at (TIMESTAMP, NULLABLE)
├── allow_comments (BOOLEAN)
├── hide_share (BOOLEAN)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### 2.3 Bảng Poll Options
```sql
poll_options
├── id (Primary Key)
├── poll_id (Foreign Key → polls.id)
├── option_text (VARCHAR)
├── image_url (VARCHAR, NULLABLE)
├── image_alt_text (VARCHAR, NULLABLE)
├── image_title (VARCHAR, NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### 2.4 Bảng Votes
```sql
votes
├── id (Primary Key)
├── poll_option_id (Foreign Key → poll_options.id)
├── poll_id (Foreign Key → polls.id)
├── user_id (Foreign Key → users.id, NULLABLE)
├── rank (INTEGER, NULLABLE)
├── ip_address (VARCHAR)
├── session_id (VARCHAR, NULLABLE)
├── voter_identifier (VARCHAR)
├── voter_name (VARCHAR, NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### 2.5 Bảng Comments
```sql
comments
├── id (Primary Key)
├── poll_id (Foreign Key → polls.id)
├── user_id (Foreign Key → users.id, NULLABLE)
├── voter_name (VARCHAR, NULLABLE)
├── content (TEXT)
├── session_id (VARCHAR, NULLABLE)
├── ip_address (VARCHAR, NULLABLE)
├── created_at (TIMESTAMP)
└── updated_at (TIMESTAMP)
```

### 2.6 Các bảng hệ thống
- **sessions**: Quản lý session của user
- **password_reset_tokens**: Reset mật khẩu
- **cache**: Cache hệ thống
- **jobs**: Queue jobs
- **failed_jobs**: Jobs thất bại

## 3. LUỒNG HOẠT ĐỘNG CỦA USER

### 3.1 Luồng Authentication
```
1. User truy cập trang chủ
2. Click "Register" → Đăng ký tài khoản
3. Click "Login" → Đăng nhập
4. Sau khi đăng nhập → Redirect đến Dashboard
5. Click "Logout" → Đăng xuất
```

### 3.2 Luồng Tạo Poll
```
1. User đăng nhập → Dashboard
2. Click "Create Poll" → Form tạo poll
3. Điền thông tin:
   - Tiêu đề poll
   - Mô tả (tùy chọn)
   - Loại poll (Standard/Ranking/Image)
   - Các options
   - Cài đặt bảo mật
4. Submit → Lưu poll vào database
5. Redirect về Dashboard với thông báo thành công
```

### 3.3 Luồng Vote
```
1. User có link poll → Truy cập /polls/{slug}
2. Kiểm tra quyền truy cập (nếu poll private)
3. Nếu poll private → Nhập access key
4. Nếu guest → Nhập tên (nếu cần)
5. Chọn options → Submit vote
6. Kiểm tra đã vote chưa
7. Lưu vote vào database
8. Redirect về trang vote với thông báo
```

### 3.4 Luồng Xem Kết Quả
```
1. User click "View Results" → /polls/{slug}/results
2. Hiển thị:
   - Biểu đồ kết quả
   - Thống kê chi tiết
   - Timeline votes
   - Comments (nếu được bật)
   - Share options (nếu không bật hide_share)
```

### 3.5 Luồng Quick Access
```
1. User nhập poll slug vào Quick Access box
2. JavaScript submit → /quick-access/{code}
3. Tìm poll theo slug
4. Nếu poll private → Redirect đến access form
5. Nếu poll public → Redirect trực tiếp đến trang vote
```

## 4. CÁC ROUTE

### 4.1 Authentication Routes (auth.php)
```php
// Guest routes
GET  /login                    → AuthenticatedSessionController@create
POST /login                    → AuthenticatedSessionController@store
GET  /register                 → RegisteredUserController@create
POST /register                 → RegisteredUserController@store
GET  /forgot-password          → PasswordResetLinkController@create
POST /forgot-password          → PasswordResetLinkController@store
GET  /reset-password/{token}   → NewPasswordController@create
POST /reset-password           → NewPasswordController@store

// Auth routes
POST /logout                   → AuthenticatedSessionController@destroy
GET  /verify-email             → EmailVerificationPromptController
GET  /verify-email/{id}/{hash} → VerifyEmailController
POST /email/verification-notification → EmailVerificationNotificationController@store
GET  /confirm-password         → ConfirmablePasswordController@show
POST /confirm-password         → ConfirmablePasswordController@store
PUT  /password                → PasswordController@update
```

### 4.2 Main Routes (web.php)
```php
// Public routes
GET  /                         → Welcome page
GET  /locale/{lang}            → Switch language

// Auth required routes
GET  /dashboard                → Dashboard (with polls list)
GET  /profile                  → ProfileController@edit
PATCH /profile                 → ProfileController@update
DELETE /profile                → ProfileController@destroy

// Poll management (Auth required)
GET  /polls/create             → PollController@create
POST /polls                    → PollController@store
POST /polls/{slug}/toggle      → PollController@toggle
GET  /polls/{slug}/export.csv → PollController@exportCsv
DELETE /polls/{slug}           → PollController@destroy

// Poll access (Public)
GET  /quick-access/{code}      → PollController@quickAccess
GET  /polls/{slug}/access      → PollController@accessForm
POST /polls/{slug}/access      → PollController@accessCheck

// Poll interaction (Public with middleware)
GET  /polls/{slug}/name        → PollController@nameForm
POST /polls/{slug}/name        → PollController@saveName
GET  /polls/{slug}             → PollController@vote
GET  /polls/{slug}/results     → PollController@show
POST /polls/{slug}/vote        → VoteController@store
POST /polls/{slug}/comments    → PollController@comment

// API routes
POST /api/media/upload         → ImageUploadController@upload
POST /api/media/validate-url   → ImageUploadController@validateUrl
DELETE /api/media/delete       → ImageUploadController@delete
```

## 5. CÁC CONTROLLER

### 5.1 PollController
**File**: `app/Http/Controllers/PollController.php`

**Chức năng chính**:
- `create()`: Hiển thị form tạo poll
- `store()`: Lưu poll mới vào database
- `vote()`: Hiển thị trang vote
- `show()`: Hiển thị kết quả poll
- `toggle()`: Đóng/mở poll
- `destroy()`: Xóa poll
- `comment()`: Thêm bình luận
- `exportCsv()`: Xuất kết quả CSV
- `accessForm()`: Hiển thị form nhập access key
- `accessCheck()`: Kiểm tra access key
- `quickAccess()`: Truy cập nhanh bằng slug
- `nameForm()`: Form nhập tên guest
- `saveName()`: Lưu tên guest

### 5.2 VoteController
**File**: `app/Http/Controllers/VoteController.php`

**Chức năng chính**:
- `store()`: Xử lý vote của user
- `handleRegularVote()`: Xử lý vote thường
- `handleRankingVote()`: Xử lý vote ranking

### 5.3 ProfileController
**File**: `app/Http/Controllers/ProfileController.php`

**Chức năng chính**:
- `edit()`: Hiển thị form chỉnh sửa profile
- `update()`: Cập nhật thông tin profile
- `destroy()`: Xóa tài khoản

### 5.4 ImageUploadController
**File**: `app/Http/Controllers/ImageUploadController.php`

**Chức năng chính**:
- `upload()`: Upload hình ảnh
- `validateUrl()`: Validate URL hình ảnh
- `delete()`: Xóa file

### 5.5 Auth Controllers
**Thư mục**: `app/Http/Controllers/Auth/`

- **AuthenticatedSessionController**: Đăng nhập/đăng xuất
- **RegisteredUserController**: Đăng ký
- **PasswordResetLinkController**: Reset mật khẩu
- **EmailVerificationController**: Xác thực email
- **ConfirmablePasswordController**: Xác nhận mật khẩu
- **PasswordController**: Thay đổi mật khẩu

## 6. CÁC MODEL

### 6.1 User Model
**File**: `app/Models/User.php`

**Quan hệ**:
- `hasMany(Poll::class)`: User có nhiều polls
- `hasMany(Vote::class)`: User có nhiều votes
- `hasMany(Comment::class)`: User có nhiều comments

**Thuộc tính**:
- `fillable`: ['name', 'email', 'password']
- `hidden`: ['password', 'remember_token']
- `casts`: ['email_verified_at' => 'datetime', 'password' => 'hashed']

### 6.2 Poll Model
**File**: `app/Models/Poll.php`

**Quan hệ**:
- `belongsTo(User::class)`: Poll thuộc về User
- `hasMany(PollOption::class)`: Poll có nhiều Options
- `hasMany(Vote::class)`: Poll có nhiều Votes
- `hasMany(Comment::class)`: Poll có nhiều Comments

**Thuộc tính**:
- `fillable`: ['user_id', 'title', 'description', 'question', 'slug', 'poll_type', 'allow_multiple', 'is_closed', 'is_private', 'access_key', 'voting_security', 'auto_close_at', 'allow_comments', 'hide_share']
- `casts`: ['allow_multiple' => 'boolean', 'is_closed' => 'boolean', 'is_private' => 'boolean', 'allow_comments' => 'boolean', 'hide_share' => 'boolean', 'auto_close_at' => 'datetime', 'description_media' => 'array']

**Methods**:
- `isImagePoll()`: Kiểm tra poll có phải image poll không
- `getMaxSelections()`: Lấy số lượng chọn tối đa
- `getDescriptionMedia()`: Lấy media mô tả
- `hasDescriptionMedia()`: Kiểm tra có media mô tả không

### 6.3 PollOption Model
**File**: `app/Models/PollOption.php`

**Quan hệ**:
- `belongsTo(Poll::class)`: Option thuộc về Poll
- `hasMany(Vote::class)`: Option có nhiều Votes

**Thuộc tính**:
- `fillable`: ['poll_id', 'option_text', 'image_url', 'image_alt_text', 'image_title']

**Methods**:
- `hasImage()`: Kiểm tra option có hình ảnh không
- `getDisplayText()`: Lấy text hiển thị
- `getImageAltText()`: Lấy alt text cho hình ảnh

### 6.4 Vote Model
**File**: `app/Models/Vote.php`

**Quan hệ**:
- `belongsTo(Poll::class)`: Vote thuộc về Poll
- `belongsTo(PollOption::class)`: Vote thuộc về Option
- `belongsTo(User::class)`: Vote thuộc về User

**Thuộc tính**:
- `fillable`: ['poll_option_id', 'poll_id', 'user_id', 'ip_address', 'session_id', 'voter_identifier', 'voter_name', 'rank']

### 6.5 Comment Model
**File**: `app/Models/Comment.php`

**Quan hệ**:
- `belongsTo(Poll::class)`: Comment thuộc về Poll
- `belongsTo(User::class)`: Comment thuộc về User

**Thuộc tính**:
- `fillable`: ['poll_id', 'user_id', 'voter_name', 'content', 'session_id', 'ip_address']

## 7. MIDDLEWARE VÀ SECURITY

### 7.1 EnsurePollAccess Middleware
**File**: `app/Http/Middleware/EnsurePollAccess.php`

**Chức năng**:
- Kiểm tra quyền truy cập poll private
- Auto-close poll khi đến thời gian
- Chia sẻ dữ liệu poll với request

### 7.2 Security Features
- **Session Control**: Ngăn chặn vote nhiều lần
- **Access Key**: Bảo mật poll private
- **CSRF Protection**: Bảo vệ khỏi CSRF attacks
- **Input Validation**: Validate tất cả input
- **SQL Injection Protection**: Sử dụng Eloquent ORM

## 8. FRONTEND VÀ UI/UX

### 8.1 Blade Templates
- **Layouts**: `app.blade.php`, `guest.blade.php`
- **Auth**: `login.blade.php`, `register.blade.php`
- **Polls**: `create.blade.php`, `vote.blade.php`, `show.blade.php`
- **Components**: Reusable components

### 8.2 Styling
- **CSS Framework**: Tailwind CSS
- **Design System**: Material Design 3
- **Responsive**: Mobile-first approach
- **Dark Mode**: Hỗ trợ dark/light theme

### 8.3 JavaScript
- **Alpine.js**: Reactive components
- **QR Code Generation**: Cho share functionality
- **Copy to Clipboard**: Share functionality
- **Drag & Drop**: Ranking polls

## 9. TÍNH NĂNG ĐẶC BIỆT

### 9.1 Poll Types
- **Standard Poll**: Vote đơn giản (single/multiple choice)
- **Ranking Poll**: Xếp hạng các options với drag & drop
- **Image Poll**: Vote bằng hình ảnh với lightbox

### 9.2 Security Features
- **Private Polls**: Với access key
- **Session-based Voting**: Ngăn chặn vote spam
- **Guest Name Capture**: Thu thập tên người vote
- **Auto-close**: Tự động đóng poll theo thời gian

### 9.3 Analytics & Reporting
- **Real-time Results**: Kết quả cập nhật ngay lập tức
- **Detailed Statistics**: Thống kê chi tiết
- **CSV Export**: Xuất dữ liệu ra file CSV
- **Timeline Charts**: Biểu đồ theo thời gian

### 9.4 User Experience
- **Quick Access**: Truy cập nhanh bằng slug
- **Share Options**: Code, URL, QR Code
- **Responsive Design**: Tương thích mọi thiết bị
- **Multi-language**: Tiếng Việt và tiếng Anh

## 10. KẾT LUẬN

QuickPoll là một hệ thống polling hoàn chỉnh với đầy đủ tính năng từ cơ bản đến nâng cao. Project được xây dựng theo kiến trúc MVC của Laravel, sử dụng các best practices về security, performance và user experience. Hệ thống hỗ trợ nhiều loại poll khác nhau, có tính bảo mật cao và giao diện thân thiện với người dùng.

**Điểm mạnh**:
- Kiến trúc rõ ràng, dễ maintain
- Bảo mật tốt với nhiều lớp protection
- UI/UX hiện đại theo Material Design 3
- Responsive và tương thích đa thiết bị
- Tính năng phong phú và linh hoạt

**Hướng phát triển**:
- Thêm real-time notifications
- Hỗ trợ thêm loại poll mới
- Tích hợp social media sharing
- Mobile app development
- Advanced analytics dashboard
