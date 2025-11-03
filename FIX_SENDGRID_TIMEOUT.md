# Fix SendGrid Connection Timeout trên Render

## Vấn đề

```
Connection timed out khi kết nối smtp.sendgrid.net:587
```

**Nguyên nhân**: Render có thể block outbound SMTP connections trên port 587.

## Giải pháp 1: Thử Port 465 với SSL (Khuyến nghị)

### Thay đổi trong Render Environment:

1. Vào **Render Dashboard** > **Environment**
2. Thay đổi:
   ```bash
   MAIL_PORT=465        # Đổi từ 587
   MAIL_ENCRYPTION=ssl  # Đổi từ tls
   ```
3. **Save và deploy lại**

Port 465 thường ít bị block hơn port 587.

## Giải pháp 2: Dùng SendGrid API thay vì SMTP (Nếu port 465 vẫn timeout)

Nếu cả port 465 và 587 đều timeout, có thể Render block tất cả outbound SMTP connections. Giải pháp: Dùng **SendGrid API** thay vì SMTP.

### Cài đặt SendGrid Laravel Package

1. **Thêm package vào `composer.json`**:
   ```json
   {
       "require": {
           "sendgrid/sendgrid": "^8.0"
       }
   }
   ```

2. **Install**:
   ```bash
   composer require sendgrid/sendgrid
   ```

3. **Update `ContactController.php`** để dùng SendGrid API trực tiếp

### Hoặc dùng Laravel Mail with SendGrid Driver

Laravel có thể dùng SendGrid API nếu cấu hình đúng.

## Giải pháp 3: Kiểm tra Render Network Restrictions

Render free tier có thể có network restrictions. Kiểm tra:

1. **Render Dashboard** > **Service Settings**
2. Tìm phần **Network** hoặc **Firewall**
3. Xem có block outbound connections không

## Giải pháp 4: Test với Mailgun (Alternative)

Nếu SendGrid không hoạt động, thử Mailgun:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.mailgun.org
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
```

Mailgun có thể có IP ranges khác, ít bị block hơn.

## Quick Fix: Thử Port 465 trước

**Bước nhanh nhất**:

1. Render Dashboard > Environment
2. Đổi:
   - `MAIL_PORT`: `465`
   - `MAIL_ENCRYPTION`: `ssl`
3. Save và deploy lại
4. Test contact form

Nếu vẫn timeout → Render có thể block tất cả SMTP, cần dùng SendGrid API.

## Troubleshooting

### Nếu port 465 vẫn timeout

**Có thể**:
- Render free tier block tất cả outbound SMTP
- Network restrictions từ Render
- SendGrid block Render IPs (ít khả năng)

**Giải pháp**:
1. Dùng SendGrid API (không qua SMTP)
2. Hoặc dùng Mailgun
3. Hoặc upgrade Render plan (có thể có network restrictions khác)

## So sánh Methods

| Method | Port | Encryption | Works on Render? |
|--------|------|-----------|------------------|
| SMTP 587 | 587 | TLS | ❌ Timeout |
| SMTP 465 | 465 | SSL | ❓ Chưa test (nên thử) |
| SendGrid API | - | HTTPS | ✅ Should work |

**Khuyến nghị**: Thử port 465 trước, nếu không được thì chuyển sang SendGrid API.

