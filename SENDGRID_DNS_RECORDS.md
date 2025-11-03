# Hướng dẫn thêm DNS Records cho SendGrid

## Bước 1: Chọn "Setup now"

Click **"Setup now"** để xem danh sách DNS records cần thêm.

## Bước 2: Xem DNS Records cần thêm

SendGrid sẽ hiển thị 3-5 DNS records dạng:

### Type A: SPF Record (TXT)
```
Type: TXT
Name: @ (hoặc yourdomain.com)
Value: v=spf1 include:sendgrid.net ~all
TTL: 3600 (hoặc Auto/Default)
```

### Type B: DKIM Records (CNAME)
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

### Type C: Link Branding (CNAME) - Nếu đã bật
```
Type: CNAME
Name: em1234.yourdomain.com (hoặc subdomain khác)
Value: sendgrid.net
```

## Bước 3: Thêm DNS Records vào Domain Provider

### A. Cloudflare (Khuyến nghị)

1. Đăng nhập Cloudflare Dashboard
2. Chọn domain của bạn
3. Vào **DNS** > **Records**
4. Click **"Add record"**
5. Thêm từng record:
   - **SPF (TXT)**: 
     - Type: `TXT`
     - Name: `@`
     - Content: `v=spf1 include:sendgrid.net ~all`
     - TTL: `Auto`
   - **DKIM 1 (CNAME)**:
     - Type: `CNAME`
     - Name: `s1._domainkey`
     - Target: `s1.domainkey.sendgrid.net`
     - TTL: `Auto`
   - **DKIM 2 (CNAME)**:
     - Type: `CNAME`
     - Name: `s2._domainkey`
     - Target: `s2.domainkey.sendgrid.net`
     - TTL: `Auto`
6. Click **"Save"** sau mỗi record

### B. GoDaddy

1. Đăng nhập GoDaddy
2. Vào **My Products** > **DNS** (bên cạnh domain)
3. Scroll xuống phần **Records**
4. Click **"Add"** để thêm từng record
5. Thêm theo format SendGrid cung cấp

### C. Namecheap

1. Đăng nhập Namecheap
2. Vào **Domain List** > Click **"Manage"** của domain
3. Chọn tab **"Advanced DNS"**
4. Thêm từng record theo hướng dẫn của SendGrid

### D. Google Domains / Other Providers

1. Đăng nhập DNS provider
2. Tìm phần **DNS Settings** hoặc **DNS Management**
3. Thêm records theo format SendGrid cung cấp

## Bước 4: Kiểm tra DNS Records

### Sau khi thêm, đợi 5-15 phút để DNS propagate

Kiểm tra bằng tools:

1. **MXToolbox**: https://mxtoolbox.com/SuperTool.aspx
   - Chọn **"TXT Lookup"** cho SPF
   - Chọn **"CNAME Lookup"** cho DKIM
   - Nhập domain và check

2. **DNS Checker**: https://dnschecker.org
   - Chọn record type (TXT, CNAME)
   - Nhập domain và subdomain
   - Xem propagation status

3. **Command line** (nếu có):
   ```bash
   # Check SPF
   nslookup -type=TXT yourdomain.com
   
   # Check DKIM
   nslookup -type=CNAME s1._domainkey.yourdomain.com
   ```

## Bước 5: Verify trong SendGrid

1. Quay lại SendGrid dashboard
2. SendGrid sẽ tự động kiểm tra DNS records
3. Nếu đúng, sẽ hiển thị **"Verified"** hoặc **"Active"**
4. Nếu chưa verify:
   - Kiểm tra lại DNS records đã đúng chưa
   - Đợi thêm 15-30 phút (DNS propagation cần thời gian)
   - Kiểm tra tại mxtoolbox.com

## Troubleshooting

### DNS records không verify

**Kiểm tra:**
1. Records đã save trong DNS provider chưa?
2. Name (subdomain) đúng chưa? (ví dụ: `s1._domainkey` không có `.yourdomain.com`)
3. Value/Target đúng chưa? (copy chính xác từ SendGrid)
4. TTL đã đủ thời gian chưa? (đợi 15-30 phút)

**Common mistakes:**
- ❌ Thêm `.yourdomain.com` vào Name (sai)
- ✅ Name chỉ là `s1._domainkey` hoặc `@`
- ❌ Copy sai Value/Target
- ✅ Copy chính xác từ SendGrid

### SPF record conflict

Nếu domain đã có SPF record:
- Cần **merge** thay vì replace
- Ví dụ: `v=spf1 include:sendgrid.net include:_spf.google.com ~all`
- Hoặc dùng SPF flattening tools

### DKIM không verify

- Kiểm tra CNAME records đã thêm đúng chưa
- Đảm bảo cả 2 DKIM records (s1 và s2) đều đã thêm
- Kiểm tra tại dnschecker.org

## Quick Checklist

- [ ] SPF (TXT) record đã thêm
- [ ] DKIM 1 (CNAME) đã thêm
- [ ] DKIM 2 (CNAME) đã thêm
- [ ] Link branding (nếu có) đã thêm
- [ ] Đã đợi 15-30 phút
- [ ] Đã kiểm tra tại mxtoolbox.com
- [ ] SendGrid đã verify thành công

## Sau khi verify thành công

1. SendGrid sẽ gửi email xác nhận
2. Có thể dùng bất kỳ email nào @yourdomain.com làm sender
3. Cập nhật `MAIL_FROM_ADDRESS` trong Render:
   ```bash
   MAIL_FROM_ADDRESS=noreply@yourdomain.com
   ```

## Alternative: Nếu không thể thêm DNS

Nếu không có quyền truy cập DNS hoặc không thể thêm records:

1. **Dùng Single Sender Verification** thay vì Domain:
   - Settings > Sender Authentication > Verify a Single Sender
   - Điền email cá nhân và verify qua email link
   - Đơn giản hơn, không cần DNS records

2. **Nhờ người quản trị domain** thêm records:
   - Click **"Send to coworker"** trong SendGrid
   - Gửi hướng dẫn cho họ

