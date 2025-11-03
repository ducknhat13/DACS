# Setup SendGrid cho Contact Form

## Tại sao cần SendGrid?

- Gmail SMTP bị timeout trên Render (cả port 587 và 465)
- SendGrid được thiết kế cho transactional emails
- Free tier: **100 emails/ngày** (đủ cho hầu hết use cases)
- Setup đơn giản, ít bị block

## Bước 1: Đăng ký SendGrid

1. Truy cập: https://sendgrid.com
2. Click **"Start for free"**
3. Điền thông tin và verify email
4. Đăng nhập vào dashboard

## Bước 2: Tạo API Key

1. Vào **Settings** > **API Keys** (hoặc https://app.sendgrid.com/settings/api_keys)
2. Click **"Create API Key"**
3. Đặt tên: `Laravel Contact Form`
4. Chọn permissions: **"Full Access"** (hoặc chỉ **"Mail Send"**)
5. Click **"Create & View"**
6. **Copy API Key ngay** (chỉ hiển thị 1 lần!)

## Bước 3: Verify Sender Email

1. Vào **Settings** > **Sender Authentication**
2. Chọn **"Verify a Single Sender"** (đơn giản nhất)
3. Điền thông tin:
   - Email: email của bạn (ví dụ: `noreply@yourdomain.com`)
   - From name: `DACS Poll System`
4. Verify email qua link trong email SendGrid gửi

**Lưu ý**: Email verify này sẽ dùng làm `MAIL_FROM_ADDRESS`

## Bước 4: Cấu hình trong Render

Vào **Render Dashboard** > **Service `dacs-web`** > **Environment**, thêm/đổi các biến:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxx  # Paste API Key của bạn ở đây
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com  # Email đã verify trong SendGrid
MAIL_FROM_NAME="DACS Poll System"
```

## Bước 5: Deploy và Test

1. **Save** environment variables
2. Render sẽ tự động **redeploy** (hoặc manual deploy)
3. Test contact form trên website
4. Kiểm tra email trong inbox

## Kiểm tra Logs

Nếu vẫn lỗi, xem logs trong Render Dashboard:
- Tìm dòng `=== Contact Form: Attempting to send email ===`
- Kiểm tra `Mail Config` có đúng không
- Nếu có error, copy và gửi cho tôi

## Troubleshooting

### Lỗi "Authentication failed"

- Kiểm tra `MAIL_USERNAME` phải là `apikey` (chính xác)
- Kiểm tra `MAIL_PASSWORD` là API Key đầy đủ (bắt đầu với `SG.`)
- Đảm bảo không có spaces thừa

### Lỗi "Sender not verified"

- Email trong `MAIL_FROM_ADDRESS` phải đã verify trong SendGrid
- Vào SendGrid > Sender Authentication để kiểm tra

### Vẫn timeout

- Thử đổi `MAIL_PORT` từ `587` sang `465` và `MAIL_ENCRYPTION` từ `tls` sang `ssl`
- Hoặc contact SendGrid support

## Alternative: Mailgun

Nếu SendGrid không phù hợp, có thể dùng **Mailgun** (free 5,000 emails/month):

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@yourdomain.mailgun.org
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
```

## So sánh

| Service | Free Tier | Setup | Reliability |
|--------|-----------|-------|-------------|
| Gmail | Unlimited | Easy | ❌ Bị block trên Render |
| SendGrid | 100/day | Easy | ✅ Tốt |
| Mailgun | 5,000/month | Medium | ✅ Tốt |

**Khuyến nghị**: Dùng **SendGrid** cho production.

