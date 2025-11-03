# Fix Gmail SMTP Connection Timeout trên Render

## Vấn đề

```
Connection timed out khi kết nối smtp.gmail.com:587
```

**Nguyên nhân có thể:**
1. Render firewall block outbound SMTP connections (port 587)
2. Gmail block Render IP addresses
3. Network timeout issues

## Giải pháp 1: Dùng Port 465 với SSL (Khuyến nghị)

Thay vì port 587 với TLS, thử port 465 với SSL:

1. Vào **Render Dashboard** > **Environment**
2. Thay đổi các biến sau:
   ```bash
   MAIL_PORT=465
   MAIL_ENCRYPTION=ssl
   ```
3. **Deploy lại** service

## Giải pháp 2: Dùng Mail Service Khác (Tốt nhất)

Gmail có thể block Render IPs. Khuyến nghị dùng mail service chuyên nghiệp:

### Option A: SendGrid (Free 100 emails/day)

1. Đăng ký tại https://sendgrid.com
2. Tạo API Key
3. Set trong Render Environment:
   ```bash
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.sendgrid.net
   MAIL_PORT=587
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=your-sendgrid-api-key
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   MAIL_FROM_NAME="DACS Poll System"
   ```

### Option B: Mailgun (Free 5,000 emails/month)

1. Đăng ký tại https://mailgun.com
2. Tạo domain và SMTP credentials
3. Set trong Render Environment:
   ```bash
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailgun.org
   MAIL_PORT=587
   MAIL_USERNAME=your-mailgun-username
   MAIL_PASSWORD=your-mailgun-password
   MAIL_ENCRYPTION=tls
   ```

### Option C: AWS SES (Pay as you go, rất rẻ)

1. Đăng ký AWS SES
2. Verify email/domain
3. Set trong Render Environment:
   ```bash
   MAIL_MAILER=smtp
   MAIL_HOST=email-smtp.us-east-1.amazonaws.com  # Thay region của bạn
   MAIL_PORT=587
   MAIL_USERNAME=your-aws-ses-smtp-username
   MAIL_PASSWORD=your-aws-ses-smtp-password
   MAIL_ENCRYPTION=tls
   ```

## Giải pháp 3: Tạm thời Log Mail (Để test)

Nếu chỉ muốn test contact form hoạt động mà chưa cần gửi mail thật:

1. Set trong Render Environment:
   ```bash
   MAIL_MAILER=log
   ```
2. Mail sẽ được log vào `storage/logs/laravel.log` thay vì gửi
3. Có thể xem log qua Render Dashboard > Logs

## Giải pháp 4: Dùng Laravel Queue với External Worker

Nếu vẫn muốn dùng Gmail:

1. Setup queue worker trên máy khác (VPS, Heroku worker, etc.)
2. Hoặc dùng service như Redis Cloud + Queue worker
3. Mail sẽ được queue và worker sẽ gửi từ IP khác

## Quick Fix: Thử Port 465 trước

Đây là cách nhanh nhất để test:

1. Vào Render Dashboard > Environment
2. Thay đổi:
   - `MAIL_PORT`: `465`
   - `MAIL_ENCRYPTION`: `ssl`
3. Deploy lại
4. Test contact form

Nếu vẫn timeout, chuyển sang dùng SendGrid hoặc Mailgun (giải pháp tốt nhất).

## So sánh Mail Services

| Service | Free Tier | Setup Difficulty | Reliability |
|---------|-----------|------------------|-------------|
| Gmail | Unlimited | Easy (nhưng bị block) | Low |
| SendGrid | 100/day | Easy | High |
| Mailgun | 5,000/month | Medium | High |
| AWS SES | Pay as you go | Medium | Very High |

**Khuyến nghị**: Dùng **SendGrid** hoặc **Mailgun** cho production. Free tier đủ cho hầu hết use cases.

