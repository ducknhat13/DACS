# Test SMTP Ports trên Render

## Ports và Encryption cho Gmail

| Port | Encryption | Mô tả | Khuyến nghị |
|------|-----------|-------|-------------|
| 587  | TLS       | STARTTLS (sau khi connect) | Thường bị block trên Render |
| 465  | SSL       | SSL ngay từ đầu | **Nên thử trước** |
| 25   | None/TLS  | Legacy, thường bị block | Không khuyến nghị |

## Thứ tự test:

1. **Port 465 + SSL** (thử trước)
2. **SendGrid** (nếu port 465 vẫn timeout)
3. **Mailgun** (backup option)

## Lưu ý:

- Render free tier có thể block port 587
- Port 465 thường ít bị block hơn
- Nếu cả 2 ports đều timeout → Gmail block Render IP → Dùng SendGrid/Mailgun

