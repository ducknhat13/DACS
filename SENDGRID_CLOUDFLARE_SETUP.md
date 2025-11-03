# Thêm SendGrid DNS Records vào Cloudflare

## Danh sách Records cần thêm

Từ SendGrid, bạn có **4 records** cần thêm:

### Record 1: Link Branding (CNAME)
```
Type: CNAME
Name: em915
Target: u57081942.wl193.sendgrid.net
```

### Record 2: DKIM 1 (CNAME)
```
Type: CNAME
Name: s1._domainkey
Target: s1.domainkey.u57081942.wl193.sendgrid.net
```

### Record 3: DKIM 2 (CNAME)
```
Type: CNAME
Name: s2._domainkey
Target: s2.domainkey.u57081942.wl193.sendgrid.net
```

### Record 4: DMARC (TXT)
```
Type: TXT
Name: _dmarc
Content: v=DMARC1; p=none; sp=none; rua=mailto:re+43f616552aef@inbound.dmarcdigests.com; pct=100
```

## Cách thêm vào Cloudflare

### Bước 1: Đăng nhập Cloudflare

1. Truy cập: https://dash.cloudflare.com
2. Đăng nhập vào account của bạn
3. Chọn domain: **`dacs-web.onrender.com`**

### Bước 2: Vào DNS Settings

1. Click vào domain **`dacs-web.onrender.com`**
2. Click tab **"DNS"** ở menu bên trái
3. Scroll xuống phần **"Records"**
4. Click nút **"Add record"**

### Bước 3: Thêm từng Record

#### Record 1: Link Branding

1. **Type**: Chọn `CNAME`
2. **Name**: Điền `em915` (chỉ phần này, KHÔNG có `.dacs-web.onrender.com`)
3. **Target**: Điền `u57081942.wl193.sendgrid.net`
4. **Proxy status**: Chọn **"DNS only"** (màu xám, KHÔNG proxy)
5. **TTL**: Chọn `Auto`
6. Click **"Save"**

#### Record 2: DKIM 1

1. Click **"Add record"** lại
2. **Type**: Chọn `CNAME`
3. **Name**: Điền `s1._domainkey` (chỉ phần này)
4. **Target**: Điền `s1.domainkey.u57081942.wl193.sendgrid.net`
5. **Proxy status**: **"DNS only"** (KHÔNG proxy)
6. **TTL**: `Auto`
7. Click **"Save"**

#### Record 3: DKIM 2

1. Click **"Add record"** lại
2. **Type**: Chọn `CNAME`
3. **Name**: Điền `s2._domainkey` (chỉ phần này)
4. **Target**: Điền `s2.domainkey.u57081942.wl193.sendgrid.net`
5. **Proxy status**: **"DNS only"** (KHÔNG proxy)
6. **TTL**: `Auto`
7. Click **"Save"**

#### Record 4: DMARC

1. Click **"Add record"** lại
2. **Type**: Chọn `TXT`
3. **Name**: Điền `_dmarc` (chỉ phần này)
4. **Content**: Paste toàn bộ: `v=DMARC1; p=none; sp=none; rua=mailto:re+43f616552aef@inbound.dmarcdigests.com; pct=100`
5. **TTL**: `Auto`
6. Click **"Save"**

## Lưu ý quan trọng

### ⚠️ Name trong Cloudflare

- ❌ **KHÔNG** điền: `em915.dacs-web.onrender.com`
- ✅ **ĐÚNG** điền: `em915` (Cloudflare tự động thêm domain)

- ❌ **KHÔNG** điền: `s1._domainkey.dacs-web.onrender.com`
- ✅ **ĐÚNG** điền: `s1._domainkey`

### ⚠️ Proxy Status

**TẤT CẢ records phải là "DNS only" (màu xám)**, KHÔNG được proxy (màu cam):

- ✅ **"DNS only"** (Proxied: OFF) - Đúng
- ❌ **"Proxied"** (màu cam) - Sai (sẽ không hoạt động!)

### ⚠️ TTL

Để **Auto** hoặc **3600** giây là được.

## Sau khi thêm xong

### Kiểm tra Records

1. Xem lại trong Cloudflare DNS records:
   - Phải thấy 4 records mới được thêm
   - Tất cả đều **"DNS only"** (không proxy)

### Đợi DNS Propagation

1. **Chờ 5-15 phút** để DNS propagate
2. Kiểm tra tại: https://mxtoolbox.com/SuperTool.aspx
   - Chọn **"CNAME Lookup"**
   - Nhập: `em915.dacs-web.onrender.com`
   - Kiểm tra có trả về `u57081942.wl193.sendgrid.net` không

### Verify trong SendGrid

1. Quay lại SendGrid dashboard
2. SendGrid sẽ tự động kiểm tra DNS records
3. Nếu đúng, sẽ hiển thị **"Verified"** hoặc **"Active"**
4. Nếu chưa verify:
   - Đợi thêm 15-30 phút
   - Kiểm tra lại records đã đúng chưa

## Checklist

Sau khi thêm, đảm bảo:

- [ ] Record 1 (Link Branding): `em915` → `u57081942.wl193.sendgrid.net`
- [ ] Record 2 (DKIM 1): `s1._domainkey` → `s1.domainkey.u57081942.wl193.sendgrid.net`
- [ ] Record 3 (DKIM 2): `s2._domainkey` → `s2.domainkey.u57081942.wl193.sendgrid.net`
- [ ] Record 4 (DMARC): `_dmarc` → (TXT content đầy đủ)
- [ ] Tất cả records đều **"DNS only"** (không proxy)
- [ ] Đã đợi 15-30 phút
- [ ] SendGrid đã verify thành công

## Troubleshooting

### Records không verify

1. Kiểm tra Name đúng chưa (không có domain đầy đủ)
2. Kiểm tra Target/Content đúng chưa (copy chính xác từ SendGrid)
3. Đảm bảo **"DNS only"** (không proxy)
4. Đợi thêm 15-30 phút
5. Kiểm tra tại mxtoolbox.com

### Proxy đang bật (màu cam)

- Phải tắt proxy để records hoạt động
- Click vào record > Toggle **"Proxy"** OFF (chuyển sang DNS only)

