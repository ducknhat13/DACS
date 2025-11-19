# QuickPoll – Hệ thống khảo sát đơn giản, hiện đại

QuickPoll là ứng dụng khảo sát (poll) xây dựng trên Laravel, tập trung vào trải nghiệm tạo/bỏ phiếu nhanh, dễ chia sẻ, có thống kê cơ bản và hỗ trợ ảnh.

## Dự án có gì?

### Tính năng
- Đăng nhập/Đăng ký (hỗ trợ Google OAuth)
- Tạo poll nhanh (standard / ranking / image)
- Bỏ phiếu (single/multiple, giới hạn số lựa chọn, tùy chọn "Other")
- Xem kết quả, export CSV, thống kê (Chart.js)
- Tự động đóng poll theo thời gian (scheduler)
- Giao diện Material Design, responsive, đa ngôn ngữ

### Kiến trúc & công nghệ
- Laravel ^12, PHP >= 8.2
- Database: MySQL (khuyến nghị) / SQLite (dev)
- Frontend: Blade + utility classes (MD3 style), JS inline cho tương tác nhỏ

### Các module/chức năng chính
- Polls:
  - Tạo: standard / ranking / image; tuỳ chọn multiple, giới hạn chọn, auto-close, comment, ẩn share
  - Vote: ranking drag&drop, image selection (giới hạn), regular (radio/checkbox), hỗ trợ "Other"
  - Kết quả: xem tổng quan, export CSV
  - Private poll: truy cập bằng access key
- Stats/History: thống kê tổng quan, biểu đồ, danh sách poll đã tạo/đã tham gia
- Contact: form liên hệ (đã tích hợp gửi qua SendGrid HTTP API khi có API key)
- Scheduler: lệnh `polls:auto-close` chạy theo phút để tự đóng poll hết hạn

### Mapping View ↔ Route (tóm tắt)
- Dashboard: `GET /dashboard` → `resources/views/dashboard.blade.php`
- Polls:
  - Create: `GET /polls/create` → `polls/create.blade.php`; Submit: `POST /polls`
  - Vote: `GET /polls/{slug}/vote` → `polls/vote.blade.php`; Submit: `POST /polls/{slug}/vote`
  - Show: `GET /polls/{slug}` → `polls/show.blade.php`
  - Export CSV: `GET /polls/{slug}/export.csv`
  - Toggle: `POST /polls/{slug}/toggle`
  - Delete: `DELETE /polls/{slug}` (form ẩn + modal confirm)
- Stats: `GET /stats` → `stats/index.blade.php`
- Contact: `GET /contact`, `POST /contact`

### Thư mục/Views quan trọng
- `resources/views/layouts/`: `app`, `guest`, `navigation`, `footer`
- `resources/views/components/`: nút, input/label/error, modal, nav-link, dropdown, fab, toast, skeleton-loader, progress-bar, dark-mode-toggle, v.v.
- `resources/views/polls/`: `create`, `vote`, `show`, `access`, `name`
- `resources/views/stats/`: `index`
- `resources/views/contact.blade.php`

Mỗi file đã có comment đầu file và chú thích ngay trên các hàm/khối JS/element quan trọng để dev frontend đọc nhanh.

### Hành vi JS tiêu biểu
- Modal xác nhận xoá: `openDeleteModal(slug, id, title)` → submit form ẩn khi xác nhận
- Bulk selection (Dashboard): chọn nhiều poll, đóng/mở/export/xoá hàng loạt
- Filter chips (Dashboard/Stats): cập nhật hidden inputs và submit form GET
- Ranking drag&drop (Vote): ghi JSON thứ hạng vào input ẩn mỗi lần sắp xếp
- Lightbox ảnh (Vote): xem ảnh full, prev/next, hỗ trợ phím ESC/←/→

### Email & OAuth (hành vi ứng dụng)
- Email: nếu `MAIL_PASSWORD` có dạng `SG.*` → hệ thống tự dùng SendGrid HTTP API (không dùng SMTP)
- Google OAuth: sử dụng Socialite; cần redirect URI trùng khớp trong Google Cloud Console

### Tài liệu frontend
Xem `FRONTEND_GUIDE.md` để nắm cấu trúc giao diện, components và các hành vi JS cụ thể.

### Giấy phép
MIT License.
