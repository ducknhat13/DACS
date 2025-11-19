# Frontend cho QuickPoll


## 1) Cấu trúc thư mục chính
- `resources/views/` chứa toàn bộ Blade templates (HTML + Blade + một ít JS inline)
  - `layouts/`: layout khung, header, footer, navigation
  - `components/`: các Blade component tái sử dụng (nút, input, modal, v.v.)
  - `polls/`: các trang liên quan đến khảo sát (tạo, bỏ phiếu, xem kết quả, truy cập)
  - `stats/`: trang thống kê/lịch sử
  - `auth/`, `profile/`, `emails/`, v.v.: các khu vực chức năng khác

Front CSS/JS chủ yếu dùng class tiện ích (Tailwind/MD3-Style Utility) và JS inline cho tương tác nhỏ (modal, menu, drag&drop).

## 2) Layout và điều hướng
- `resources/views/layouts/app.blade.php`
  - Layout sau đăng nhập; nạp CSS/JS (vite), meta csrf; bao quanh nội dung qua `<x-app-layout>`.
- `resources/views/layouts/guest.blade.php`
  - Layout cho các trang khách (login/register/landing) đơn giản.
- `resources/views/layouts/navigation.blade.php`
  - Thanh điều hướng: logo, menu, user dropdown, chuyển Dark/Light.
  - Gợi ý: thêm JS toggle menu mobile bằng cách add/remove class ẩn/hiện.
- `resources/views/layouts/footer.blade.php`
  - Footer toàn site.

Mọi trang thường bọc bởi `<x-app-layout>` hoặc layout guest và truyền tiêu đề qua `<x-slot name="header">`.

## 3) Components tái sử dụng (UI primitives)
- Nút: `components/primary-button.blade.php`, `secondary-button`, `danger-button`
- Input & Label & Error: `components/text-input.blade.php`, `input-label`, `input-error`
- Điều hướng: `components/nav-link`, `responsive-nav-link`, `dropdown`, `dropdown-link`
- Modal: `components/modal`
- Khác: `components/fab` (Floating Action Button), `toast`, `skeleton-loader`, `progress-bar`, `dark-mode-toggle`, `application-logo`, `auth-session-status`

Mỗi component có chú thích đầu file mô tả mục đích và cách dùng.

## 4) Các trang (views) quan trọng và chức năng
- Trang chủ/landing:
  - `resources/views/home.blade.php`
    - Các section: Hero, Statistics, Features, Live Demo, CTA
    - Có modal Demo (JS: mở/đóng, giả lập vote, hiển thị kết quả)
  - `resources/views/welcome.blade.php`
- Dashboard:
  - `resources/views/dashboard.blade.php`
    - Danh sách polls (grid), ô tìm kiếm, chips lọc/sắp xếp, bulk actions
    - Action menu từng poll (đóng/mở, export, xóa) và hai modal: `singleDeleteModal`, `bulkDeleteModal`
    - JS chính:
      - Filter Chips: cập nhật input hidden và submit form
      - Action Menu: `toggleActionMenu(id)`; đóng khi click ra ngoài/scroll/resize
      - Ripple effect cho card
      - Modal tạo poll (open/close; add/remove options; toggle private/access_key; đổi poll type)
      - Bulk Selection: chọn nhiều, đóng/mở/export/xóa hàng loạt
- Polls:
  - Tạo poll: `resources/views/polls/create.blade.php`
    - Form 2 cột (thông tin cơ bản | cài đặt):
      - Chọn loại poll (standard/ranking/image)
      - Chọn kiểu chọn (single/multiple) + max choices
      - Options dạng text hoặc ảnh; upload/URL; preview; xóa option; auto-fill để tránh lỗi validate
      - Cài đặt nâng cao: auto close, cho phép comment, ẩn share
    - JS: quản lý options, media upload/validate URL, toggle sections theo poll type, auto-generate access key
  - Bỏ phiếu: `resources/views/polls/vote.blade.php`
    - Hiển thị media mô tả, banner closed, form vote theo loại poll
    - Ranking: drag&drop (HTML5 drag API), ghi JSON thứ hạng vào input ẩn
    - Image: chọn ảnh với giới hạn lựa chọn tối đa (nếu có)
    - Regular: radio/checkbox; hỗ trợ ô "Other"
    - Lightbox xem ảnh toàn màn hình (JS riêng cuối file)
  - Xem kết quả: `resources/views/polls/show.blade.php`
  - Bảo vệ poll private (access key): `resources/views/polls/access.blade.php`
  - Thu thập tên (nếu dùng): `resources/views/polls/name.blade.php`
- Thống kê/Lịch sử:
  - `resources/views/stats/index.blade.php`
    - Thống kê tổng hợp, biểu đồ Chart.js, danh sách polls theo scope (created/joined)
    - Menu hành động từng dòng: toggle, export, delete (modal confirm)
- Liên hệ:
  - `resources/views/contact.blade.php`
    - Form liên hệ: name/email/subject/message, flash message, nút gửi có trạng thái loading

Các trang `terms.blade.php`, `privacy.blade.php`, `about.blade.php` là tĩnh, có chú thích bố cục gợi ý.

## 5) Liên kết route ↔ view (tham khảo nhanh)
- Dashboard: `GET /dashboard` → `dashboard.blade.php`
- Polls:
  - Tạo: `GET /polls/create` → `polls/create.blade.php`; Submit: `POST /polls`
  - Vote: `GET /polls/{slug}/vote` hoặc tương đương → `polls/vote.blade.php`; Submit: `POST /polls/{slug}/vote`
  - Kết quả: `GET /polls/{slug}` → `polls/show.blade.php`
  - Export CSV: `GET /polls/{slug}/export.csv`
  - Đóng/Mở: `POST /polls/{slug}/toggle`
  - Xóa: `DELETE /polls/{slug}` (qua form ẩn + modal confirm)
- Stats: `GET /stats` → `stats/index.blade.php` (filter qua query string)
- Contact: `GET /contact`, `POST /contact`

Các route xem chi tiết ở `routes/web.php` (đã có comment tổng quan).

## 6) Mẫu tương tác JavaScript thường dùng
- Modal xác nhận xóa (đơn lẻ): `openDeleteModal(slug, id, title)`
  - Cập nhật nội dung, gắn handler submit form ẩn tương ứng, hiện modal.
- Bulk selection (Dashboard):
  - Chế độ chọn nhiều: hiển thị thanh bulk bar, enable nút hành động
  - Hành động: gọi POST/DELETE (fetch) tuần tự và reload trang
- Filter chips: toggle lớp `active`, cập nhật input hidden, submit form GET
- Ranking drag&drop: cập nhật input ẩn JSON thứ hạng sau mỗi lần sắp xếp
- Lightbox ảnh: mở overlay, điều hướng prev/next, hỗ trợ phím ESC/←/→

Tất cả hàm/khối JS đều đã có comment ngay trước định nghĩa để người mới đọc hiểu nhanh.

## 7) Quy ước UI/UX & Style
- Màu & token theo Material Design 3: `--surface`, `--surface-variant`, `--on-surface`, `--outline`, `--primary`
- Class tiện ích dạng Tailwind-like (flex, grid, gap, rounded, border, text-*, bg-*)
- Modal dạng overlay: `fixed inset-0 ...`, hiển thị bằng thêm lớp `flex`, ẩn bằng `hidden`
- Button style: `btn`, biến thể `btn-primary`, `btn-neutral`, `danger`, `icon-button`
- Chips: `filter-chip`, `assist-chip` với trạng thái `active`

## 8) Lưu ý phát triển frontend
- Giữ comment hiện có: đã ghi rõ mục đích từng phần/từng hàm trong Blade
- Khi thêm UI mới:
  - Ưu tiên tái sử dụng component trong `resources/views/components`
  - Nếu cần JS: thêm ngay dưới cuối file view tương ứng, viết comment mô tả input/output/side-effects
  - Tránh phụ thuộc nặng vào build tool trong test: các trang đã cố gắng không phụ thuộc Vite runtime

## 9) Checklist thao tác nhanh cho người mới
- Muốn thêm một thao tác vào poll card (Dashboard):
  1. Thêm nút trong action menu của card
  2. Thêm form/route tương ứng (nếu cần)
  3. Cập nhật JS xử lý submit hoặc mở modal
- Muốn thêm bộ lọc mới (Dashboard/Stats):
  1. Thêm chip và input hidden
  2. Cập nhật JS thay đổi giá trị và submit form
- Muốn bật/tắt modal confirm xóa:
  1. Gọi `openDeleteModal(...)` với slug/id/title phù hợp
  2. Đảm bảo có form ẩn `deleteForm{ID}` để submit

## 10) Nơi bắt đầu tốt nhất
- Đọc qua `dashboard.blade.php`: nắm grid, chips, menu, bulk actions
- Xem `polls/create.blade.php` và `polls/vote.blade.php` để hiểu 2 luồng quan trọng
- Lướt `components/*` để biết các khối UI có sẵn
- Thử sửa label/nút/JS nhỏ và xem tác động


