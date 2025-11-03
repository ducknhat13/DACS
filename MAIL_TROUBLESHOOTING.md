# Mail Troubleshooting Guide

## Vấn đề: Contact form timeout khi gửi mail

### Nguyên nhân có thể:

1. **SMTP connection timeout**: Gmail SMTP không thể kết nối từ Render
2. **Queue connection = database nhưng không có worker**: Mail được queue nhưng không được process
3. **Gmail block IP**: Gmail có thể block connection từ Render IP
4. **Credentials sai**: MAIL_USERNAME hoặc MAIL_PASSWORD không đúng

### Giải pháp 1: Dùng QUEUE_CONNECTION=sync (Khuyến nghị cho Render Free Tier)

**Vấn đề**: Nếu `QUEUE_CONNECTION=database`, mail sẽ được đẩy vào queue table, nhưng **Render free tier không hỗ trợ background worker** để process queue. Mail sẽ bị stuck trong queue và không được gửi.

**Giải pháp**: Dùng `sync` để gửi mail trực tiếp (synchronous):

1. Vào **Render Dashboard** > **Environment**
2. Tìm `QUEUE_CONNECTION`
3. Đặt giá trị: `sync`
4. **Deploy lại** service

### Giải pháp 2: Kiểm tra Mail Credentials

Đảm bảo các biến sau được set đúng trong Render Environment:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD="your-16-char-app-password"  # Phải có quotes nếu có spaces
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="DACS Poll System"
```

**Lưu ý về Gmail App Password**:
- Không dùng password thông thường
- Phải tạo **App Password** từ Gmail settings:
  1. Vào Google Account > Security
  2. Bật 2-Step Verification (nếu chưa bật)
  3. Tạo App Password
  4. Copy 16-char password (có spaces)
  5. Đặt trong quotes trong env: `"abcd efgh ijkl mnop"`

### Giải pháp 3: Xem Logs Chi Tiết

Sau khi deploy code mới, logs sẽ hiển thị chi tiết:

1. Vào **Render Dashboard** > **Logs**
2. Tìm các dòng:
   - `=== Contact Form: Attempting to send email ===`
   - `Mail Config: {...}` - Kiểm tra config có đúng không
   - `=== Contact Form: SMTP Transport Exception ===` - Error cụ thể nếu có

### Giải pháp 4: Test với Mail Service Khác

Nếu Gmail vẫn không hoạt động, có thể thử:

**Option A: SendGrid (Free tier có 100 emails/day)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

**Option B: Mailtrap (Cho testing)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
```

### Giải pháp 5: Tạm thời Log Mail thay vì Gửi

Để test contact form hoạt động, có thể tạm thời log mail thay vì gửi:

1. Set `MAIL_MAILER=log` trong Render Environment
2. Mail sẽ được log vào `storage/logs/laravel.log`
3. Deploy lại và test

### Kiểm tra Queue Status

Nếu đang dùng `QUEUE_CONNECTION=database`, kiểm tra:

1. Queue table đã được tạo chưa (migration `create_jobs_table` đã chạy)
2. Có jobs trong queue không:
   ```sql
   SELECT * FROM jobs ORDER BY created_at DESC LIMIT 10;
   ```
3. Có failed jobs không:
   ```sql
   SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
   ```

### Render Free Tier Limitations

- **Không có background worker**: Không thể chạy `php artisan queue:work`
- **Giải pháp**: Dùng `QUEUE_CONNECTION=sync` để gửi mail trực tiếp
- **Hạn chế**: Nếu SMTP chậm, request sẽ timeout (đã set timeout 30s)

### Next Steps

1. **Deploy code mới** với enhanced logging
2. **Set `QUEUE_CONNECTION=sync`** trong Render Environment
3. **Kiểm tra logs** sau khi gửi contact form
4. **Copy error message** từ logs và gửi cho tôi nếu vẫn lỗi

