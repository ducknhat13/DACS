# Hướng dẫn Setup Domain trong SendGrid

## Khi nào cần verify domain?

- **Có domain riêng** (ví dụ: `yourdomain.com`) → Nên verify domain
- **Không có domain riêng** → Dùng Single Sender Verification với email cá nhân (Gmail, etc.)

## Các bước điền form

### 1. Domain

```
Domain: yourdomain.com
```

**Lưu ý:**
- ❌ Không có `https://` hay `www`
- ❌ Không có `http://`
- ✅ Chỉ điền domain: `yourdomain.com` hoặc `example.com`

### 2. Brand the link?

**Chọn: No** (cho lần đầu, đơn giản hơn)

- **Yes**: Tất cả tracking links sẽ dùng domain của bạn thay vì `sendgrid.net`
- **No**: Links sẽ dùng `sendgrid.net` (ổn cho contact form)

Có thể bật sau nếu cần.

### 3. Advanced Settings

#### Use automated security
**Chọn: Enable (khuyến nghị)**

- Tự động rotate DKIM keys để bảo mật
- Khuyến nghị: **Enable**

#### Use custom return path
**Chọn: Disable** (để mặc định)

- Chỉ cần nếu có yêu cầu đặc biệt
- Cho contact form: **Disable** là đủ

#### Use a custom DKIM selector
**Chọn: Disable** (để mặc định)

- Chỉ cần nếu selector "s" đã dùng bởi service khác
- Cho contact form: **Disable** là đủ

## Sau khi điền form

1. Click **"Verify"** hoặc **"Next"**
2. SendGrid sẽ hiển thị **DNS records** cần thêm:
   - **SPF record** (TXT)
   - **DKIM records** (CNAME hoặc TXT)
   - **CNAME records** (cho link branding, nếu đã bật)
3. **Thêm DNS records vào domain**:
   - Đăng nhập vào DNS provider (Cloudflare, GoDaddy, Namecheap, etc.)
   - Thêm từng record theo hướng dẫn của SendGrid
4. **Chờ verify** (thường 5-15 phút)
5. SendGrid sẽ gửi email khi verify thành công

## Ví dụ DNS Records (tham khảo)

### SPF Record (TXT)
```
Type: TXT
Name: @ (hoặc yourdomain.com)
Value: v=spf1 include:sendgrid.net ~all
TTL: 3600 (hoặc default)
```

### DKIM Records (CNAME)
```
Type: CNAME
Name: s1._domainkey.yourdomain.com
Value: s1.domainkey.sendgrid.net
```

```
Type: CNAME
Name: s2._domainkey.yourdomain.com
Value: s2.domainkey.sendgrid.net
```

### Link Branding (nếu bật)
```
Type: CNAME
Name: em1234.yourdomain.com
Value: sendgrid.net
```

## Nếu không có domain riêng

**Skip bước này** và dùng **Single Sender Verification**:

1. Vào **Settings** > **Sender Authentication**
2. Chọn **"Verify a Single Sender"**
3. Điền email cá nhân (Gmail, Outlook, etc.)
4. Verify qua email
5. Dùng email đó làm `MAIL_FROM_ADDRESS`

## Troubleshooting

### Domain verify failed

- Kiểm tra DNS records đã thêm đúng chưa
- Chờ thêm 15-30 phút (DNS propagation cần thời gian)
- Kiểm tra tại https://mxtoolbox.com/SuperTool.aspx
- Đảm bảo không có record trùng lặp

### DNS không có quyền chỉnh sửa

- Liên hệ người quản trị domain
- Hoặc dùng Single Sender Verification với email cá nhân

## Quick Setup (Khuyến nghị)

**Cho contact form đơn giản:**

1. Chọn **"Verify a Single Sender"** thay vì Domain
2. Điền email cá nhân
3. Verify qua email
4. Dùng email đó trong Render Environment

**Domain verification chỉ cần nếu:**
- Muốn dùng email @yourdomain.com
- Gửi volume lớn
- Cần brand chuyên nghiệp hơn

