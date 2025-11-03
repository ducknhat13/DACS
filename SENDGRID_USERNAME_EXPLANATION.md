# Giải thích MAIL_USERNAME trong SendGrid

## Câu hỏi: MAIL_USERNAME phải điền "apikey" thật không?

**Câu trả lời: ĐÚNG!** ✅

## Chi tiết

### MAIL_USERNAME = `apikey`

Khi dùng **SendGrid API Key** để gửi mail qua SMTP, SendGrid yêu cầu:

- **Username**: Luôn luôn là `apikey` (chữ thường, không có quotes)
- **Password**: Là API Key của bạn (bắt đầu với `SG.`)

## Tại sao?

SendGrid sử dụng cơ chế xác thực đặc biệt:
- Username = `apikey` để báo cho SendGrid biết bạn đang dùng API Key
- Password = API Key thực tế để xác thực

## Cách điền trong Render

### ✅ ĐÚNG:
```
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.xxxxxxxxxxxxxxxxxxxxx
```

### ❌ SAI:
```
MAIL_USERNAME="apikey"          # Không có quotes
MAIL_USERNAME=your-email@gmail.com  # Không phải email
MAIL_USERNAME=sendgrid          # Không phải sendgrid
MAIL_USERNAME=SG.xxxxx          # Không phải API Key
```

## Ví dụ đầy đủ

Trong Render Environment Variables:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey                    ← CHỈ điền "apikey" (không có quotes)
MAIL_PASSWORD=SG.abcdefghijklmnopqrstuvwxyz  ← API Key của bạn
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-verified-email@gmail.com
MAIL_FROM_NAME="DACS Poll System"
```

## Lưu ý

- **`apikey`** là từ khóa cố định của SendGrid, không phải giá trị tùy chỉnh
- Phải viết **chữ thường** (`apikey` không phải `APIKEY` hay `ApiKey`)
- **KHÔNG có quotes** trong environment variable
- Đây là cách duy nhất để SendGrid nhận diện bạn đang dùng API Key

## Nếu dùng SendGrid Username/Password (không dùng)

Nếu bạn dùng SendGrid Username/Password thay vì API Key (không khuyến nghị):
- `MAIL_USERNAME` = SendGrid username của bạn
- `MAIL_PASSWORD` = SendGrid password của bạn

Nhưng **API Key an toàn hơn** và là cách khuyến nghị của SendGrid.

## Kết luận

**Có, `MAIL_USERNAME` phải điền `apikey` (chữ thường, không có quotes)** - Đây là yêu cầu của SendGrid khi dùng API Key.

