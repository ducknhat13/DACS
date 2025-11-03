# Hướng dẫn Setup Single Sender Verification trong SendGrid

## Bước 1: Chọn Single Sender Verification

✅ **Đúng rồi!** Chọn **"Single Sender Verification"** và click **"Verify an Address"**

## Bước 2: Điền Form "Create a Sender"

### Các trường bắt buộc:

#### 1. From Name
```
From Name: DACS Poll System
```
- Tên hiển thị khi email được gửi
- User sẽ thấy: "DACS Poll System <your-email@gmail.com>"

#### 2. From Email Address ⭐ (Quan trọng nhất)
```
From Email Address: your-email@gmail.com
```
- **Email cá nhân của bạn** (Gmail, Outlook, Yahoo, etc.)
- Email này sẽ nhận verification link
- **Phải là email bạn có quyền truy cập inbox**
- Email này sẽ dùng làm `MAIL_FROM_ADDRESS` trong Render

#### 3. Reply To
```
Reply To: (có thể để trống hoặc dùng email khác)
```
- Email nhận reply từ người nhận
- Có thể để trống → sẽ reply về From Email Address
- Hoặc điền email khác nếu muốn

#### 4. Company Address (Bắt buộc)
```
Company Address: Your address here
Company Address Line 2: (tùy chọn)
City: Your city
State: Select State (chọn state của bạn)
Zip Code: Your zip code
Country: Select Country (chọn quốc gia)
```
- **Lưu ý**: Theo CAN-SPAM và CASL laws, cần có địa chỉ vật lý trong promotional emails
- Cho contact form: Có thể dùng địa chỉ cá nhân hoặc công ty
- SendGrid sẽ tự động thêm vào footer của email (nếu dùng email templates)

#### 5. Nickname (Tùy chọn)
```
Nickname: Contact Form Sender
```
- Tên gợi nhớ cho sender này trong SendGrid dashboard
- Có thể để trống hoặc đặt tên dễ nhớ

## Ví dụ điền Form

```
From Name: DACS Poll System
From Email Address: ducnhatnguyen13082004@gmail.com
Reply To: (để trống)
Company Address: 123 Main Street
Company Address Line 2: (để trống)
City: Ho Chi Minh City
State: (nếu ở US) hoặc để trống
Zip Code: 70000
Country: Vietnam (hoặc quốc gia của bạn)
Nickname: QuickPoll Contact Form
```

## Bước 3: Submit và Verify Email

1. **Click "Create"** sau khi điền xong
2. SendGrid sẽ gửi **verification email** đến địa chỉ bạn đã điền
3. **Check inbox** (có thể ở spam folder)
4. **Click link verification** trong email
5. Email sẽ được verify và status chuyển thành **"Verified"**

## Bước 4: Cấu hình trong Render

Sau khi verify email thành công, cập nhật Render Environment:

```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your-api-key-here  # API Key đã tạo trước đó
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=ducnhatnguyen13082004@gmail.com  # Email đã verify
MAIL_FROM_NAME="DACS Poll System"
```

**Quan trọng**: `MAIL_FROM_ADDRESS` phải **chính xác** email đã verify trong SendGrid!

## Lưu ý

### ⚠️ Email Verification

- Email sẽ được verify qua link trong email
- **Phải check inbox** (có thể ở spam folder)
- Link verification có thể expire sau 24-48 giờ
- Nếu không nhận được email, check spam hoặc request resend

### ⚠️ Address Requirement

- **CAN-SPAM Act** (US) yêu cầu có địa chỉ vật lý trong promotional emails
- SendGrid tự động thêm vào footer nếu dùng email templates
- Cho **transactional emails** (như contact form): Vẫn cần điền address nhưng ít strict hơn

### ⚠️ Single Sender Limitations

- **Chỉ dùng được 1 email** (email đã verify)
- Không thể dùng bất kỳ email nào @domain
- Nếu muốn dùng nhiều emails, cần verify từng cái hoặc dùng Domain Authentication

## Checklist

- [ ] Đã chọn "Single Sender Verification"
- [ ] Đã điền form "Create a Sender" đầy đủ
- [ ] Đã click "Create"
- [ ] Đã nhận verification email trong inbox
- [ ] Đã click verification link
- [ ] Email status là "Verified" trong SendGrid
- [ ] Đã cập nhật `MAIL_FROM_ADDRESS` trong Render Environment
- [ ] Đã deploy lại service trên Render
- [ ] Đã test contact form

## Troubleshooting

### Không nhận được verification email

1. Check **spam/junk folder**
2. Đợi 5-10 phút (email có thể delay)
3. Request **resend verification email** trong SendGrid dashboard
4. Kiểm tra email address đã đúng chưa

### Email verify failed

1. Click link verification **ngay sau khi nhận** (có thể expire)
2. Đảm bảo không có spaces trong link khi copy
3. Thử **request new verification email**

### Still not working after verify

1. Kiểm tra `MAIL_FROM_ADDRESS` trong Render có đúng email đã verify không
2. Kiểm tra API Key đã đúng chưa
3. Check logs trong Render để xem error cụ thể
4. Đảm bảo đã deploy lại sau khi đổi env vars

## Next Steps

Sau khi verify xong:
1. Update Render Environment variables
2. Deploy lại service
3. Test contact form
4. Kiểm tra email có được gửi không

