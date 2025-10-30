# LUỒNG HOẠT ĐỘNG CỦA HỆ THỐNG QUICKPOLL

## 1. LUỒNG ĐĂNG KÝ TÀI KHOẢN

### 1.1 Bước 1: Truy cập trang đăng ký
- Người dùng truy cập website QuickPoll
- Click vào nút "Register" trên trang chủ
- Hệ thống chuyển đến trang đăng ký (`/register`)

### 1.2 Bước 2: Nhập thông tin tài khoản
- Người dùng nhập các thông tin:
  - **Tên đầy đủ** (name)
  - **Email** (email)
  - **Mật khẩu** (password)
  - **Xác nhận mật khẩu** (password_confirmation)

### 1.3 Bước 3: Kiểm tra thông tin tài khoản
- Hệ thống kiểm tra sự tồn tại của email trong database
  - **Nếu email đã tồn tại**: Hiển thị thông báo "Email đã được sử dụng" → Quay lại bước nhập thông tin
  - **Nếu email chưa tồn tại**: Tiếp tục bước tiếp theo

### 1.4 Bước 4: Kiểm tra mật khẩu
- Hệ thống so sánh mật khẩu và xác nhận mật khẩu
  - **Nếu mật khẩu không khớp**: Hiển thị thông báo "Mật khẩu xác nhận không khớp" → Quay lại bước nhập thông tin
  - **Nếu mật khẩu khớp**: Tiếp tục tạo tài khoản

### 1.5 Bước 5: Tạo tài khoản thành công
- Hệ thống mã hóa mật khẩu và lưu vào database
- Tài khoản được tạo thành công
- Tự động đăng nhập và chuyển đến Dashboard

## 2. LUỒNG ĐĂNG NHẬP

### 2.1 Bước 1: Truy cập trang đăng nhập
- Người dùng click vào nút "Login" trên trang chủ
- Hệ thống chuyển đến trang đăng nhập (`/login`)

### 2.2 Bước 2: Nhập thông tin đăng nhập
- Người dùng nhập:
  - **Email** (email)
  - **Mật khẩu** (password)
  - **Ghi nhớ đăng nhập** (remember me) - tùy chọn

### 2.3 Bước 3: Kiểm tra thông tin đăng nhập
- Hệ thống xác thực thông tin đăng nhập
  - **Nếu thông tin đúng**: Chuyển đến Dashboard với thông báo thành công
  - **Nếu thông tin sai**: Hiển thị thông báo "Email hoặc mật khẩu không đúng" → Quay lại bước nhập thông tin

## 3. LUỒNG HOẠT ĐỘNG TỪ DASHBOARD

### 3.1 Truy cập Dashboard
- Sau khi đăng nhập thành công, người dùng được chuyển đến Dashboard (`/dashboard`)
- Dashboard hiển thị:
  - Danh sách các poll đã tạo
  - Thống kê tổng quan
  - Nút "Create Poll" để tạo poll mới

### 3.2 Tạo Poll Mới

#### 3.2.1 Bước 1: Truy cập trang tạo poll
- Click nút "Create Poll" trên Dashboard
- Hệ thống chuyển đến trang tạo poll (`/polls/create`)

#### 3.2.2 Bước 2: Nhập thông tin poll
- Người dùng nhập:
  - **Tiêu đề poll** (title)
  - **Mô tả** (description) - tùy chọn
  - **Loại poll**: Standard, Ranking, hoặc Image
  - **Loại lựa chọn**: Single choice hoặc Multiple choice
  - **Các options** (tối thiểu 2 options)

#### 3.2.3 Bước 3: Cài đặt bảo mật
- Chọn **Voting Security**:
  - **Session-based**: Một vote mỗi session
  - **Private with key**: Poll riêng tư với access key
- Nếu chọn Private:
  - Nhập access key tùy chỉnh hoặc để trống (tự động tạo)
  - Hệ thống tự động tạo key 8 ký tự nếu để trống

#### 3.2.4 Bước 4: Cài đặt nâng cao
- **Auto Close**: Tự động đóng poll theo thời gian
- **Allow Comments**: Cho phép bình luận
- **Hide Share**: Ẩn nút chia sẻ

#### 3.2.5 Bước 5: Tạo poll thành công
- Hệ thống tạo poll với slug duy nhất (format: word-word-word)
- Lưu vào database
- Chuyển về Dashboard với thông báo thành công

### 3.3 Quản lý Poll

#### 3.3.1 Xem danh sách poll
- Dashboard hiển thị tất cả poll của user
- Có thể:
  - **Tìm kiếm** theo tên hoặc slug
  - **Lọc** theo trạng thái (mở/đóng)
  - **Sắp xếp** theo thời gian tạo hoặc số vote

#### 3.3.2 Thao tác với poll
- **Mở/Đóng poll**: Toggle trạng thái poll
- **Xem kết quả**: Truy cập trang results
- **Export CSV**: Xuất dữ liệu vote
- **Xóa poll**: Xóa poll và tất cả dữ liệu liên quan

### 3.4 Vote trên Poll

#### 3.4.1 Truy cập poll
- Người dùng có link poll hoặc sử dụng Quick Access
- Truy cập `/polls/{slug}` hoặc `/quick-access/{code}`

#### 3.4.2 Kiểm tra quyền truy cập
- **Nếu poll public**: Truy cập trực tiếp
- **Nếu poll private**: 
  - Hiển thị form nhập access key
  - Nhập đúng key → Cho phép truy cập
  - Nhập sai key → Hiển thị lỗi và yêu cầu nhập lại

#### 3.4.3 Nhập tên (nếu cần)
- **Nếu là guest**: Yêu cầu nhập tên trước khi vote
- **Nếu đã đăng nhập**: Sử dụng tên từ tài khoản

#### 3.4.4 Thực hiện vote
- **Standard Poll**: Chọn một hoặc nhiều options
- **Ranking Poll**: Kéo thả để sắp xếp thứ tự
- **Image Poll**: Chọn hình ảnh (có thể nhiều)

#### 3.4.5 Xác nhận vote
- Hệ thống kiểm tra đã vote chưa
- **Nếu đã vote**: Hiển thị thông báo "Bạn đã vote rồi"
- **Nếu chưa vote**: Lưu vote và hiển thị thông báo cảm ơn

### 3.5 Xem kết quả Poll

#### 3.5.1 Truy cập trang kết quả
- Click "View Results" hoặc truy cập `/polls/{slug}/results`
- Hiển thị:
  - **Biểu đồ kết quả** (Chart.js)
  - **Thống kê chi tiết**
  - **Timeline votes** (nếu là owner)

#### 3.5.2 Thông tin cho Owner
- **Danh sách voters**: Tên, lựa chọn, thời gian vote
- **Tìm kiếm voters**: Theo tên hoặc option
- **Lọc theo thời gian**: Từ ngày - đến ngày
- **Access Key**: Hiển thị và copy key (nếu poll private)

#### 3.5.3 Chia sẻ Poll
- **Share Code**: Poll slug (ví dụ: meo-chay-xanh)
- **Share URL**: Link trực tiếp đến poll
- **QR Code**: Mã QR để scan
- **Copy to Clipboard**: Sao chép các thông tin chia sẻ

### 3.6 Bình luận (nếu được bật)

#### 3.6.1 Thêm bình luận
- Nhập nội dung bình luận
- Submit → Lưu vào database
- Hiển thị ngay lập tức

#### 3.6.2 Xem bình luận
- Hiển thị danh sách bình luận theo thời gian
- Hiển thị tên người bình luận và thời gian

## 4. LUỒNG QUICK ACCESS

### 4.1 Sử dụng Quick Access
- Nhập poll slug vào ô Quick Access trên header
- Submit → Truy cập `/quick-access/{code}`
- Hệ thống tìm poll theo slug
- **Nếu poll public**: Chuyển trực tiếp đến trang vote
- **Nếu poll private**: Chuyển đến form nhập access key

## 5. LUỒNG QUẢN LÝ PROFILE

### 5.1 Chỉnh sửa thông tin
- Truy cập `/profile`
- Chỉnh sửa tên và email
- Cập nhật → Lưu vào database

### 5.2 Thay đổi mật khẩu
- Nhập mật khẩu hiện tại
- Nhập mật khẩu mới và xác nhận
- Cập nhật → Mã hóa và lưu

### 5.3 Xóa tài khoản
- Xác nhận xóa tài khoản
- Xóa tất cả dữ liệu liên quan
- Chuyển về trang chủ

## 6. LUỒNG XỬ LÝ LỖI

### 6.1 Lỗi đăng nhập
- Email không tồn tại
- Mật khẩu sai
- Tài khoản bị khóa

### 6.2 Lỗi truy cập poll
- Poll không tồn tại
- Access key sai
- Poll đã đóng

### 6.3 Lỗi vote
- Đã vote rồi
- Poll đã đóng
- Không chọn option nào

## 7. LUỒNG BẢO MẬT

### 7.1 Session Management
- Mỗi user có session riêng
- Session timeout sau thời gian không hoạt động
- CSRF protection cho tất cả form

### 7.2 Access Control
- Chỉ owner mới có thể quản lý poll
- Guest chỉ có thể vote (nếu có quyền)
- Private poll cần access key

### 7.3 Data Validation
- Validate tất cả input
- Sanitize dữ liệu trước khi lưu
- SQL injection protection

## 8. LUỒNG RESPONSIVE DESIGN

### 8.1 Desktop
- Layout đầy đủ với sidebar
- Grid layout cho polls
- Hover effects và animations

### 8.2 Mobile
- Responsive navigation
- Touch-friendly buttons
- Optimized forms
- Mobile-specific layouts

## 9. LUỒNG ĐA NGÔN NGỮ

### 9.1 Chuyển đổi ngôn ngữ
- Click language switcher
- Chọn tiếng Việt hoặc tiếng Anh
- Lưu vào session
- Reload trang với ngôn ngữ mới

## 10. LUỒNG EXPORT DỮ LIỆU

### 10.1 Export CSV
- Click "Export CSV" trên trang results
- Tạo file CSV với:
  - Thông tin poll
  - Kết quả vote
  - Chi tiết từng vote (thời gian, tên, IP, session)
- Download file về máy

---

**Tóm tắt**: Hệ thống QuickPoll cung cấp một luồng hoạt động hoàn chỉnh từ đăng ký/đăng nhập, tạo và quản lý poll, vote, xem kết quả, đến các tính năng nâng cao như bảo mật, chia sẻ và export dữ liệu. Tất cả được thiết kế với giao diện Material Design 3 và hỗ trợ responsive trên mọi thiết bị.






