# Giải thích DNS Records trong Cloudflare

## 3 Records có sẵn (KHÔNG phải lỗi)

### Record 1: Wildcard `*`
```
Type: CNAME
Name: *
Target: gcp-us-west1-1.origin.onrender.com
Proxy: Proxied
```
**Mục đích**: Catch-all subdomain, trỏ tất cả subdomain chưa được định nghĩa về Render  
**Cần giữ**: ✅ YES - Cần cho website hoạt động

### Record 2: Root domain
```
Type: CNAME
Name: dacs-web.onrender.com (hoặc @)
Target: gcp-us-west1-1.origin.onrender.com
Proxy: Proxied
```
**Mục đích**: Trỏ root domain về Render  
**Cần giữ**: ✅ YES - Cần cho website hoạt động

### Record 3: www subdomain
```
Type: CNAME
Name: www
Target: gcp-us-west1-1.origin.onrender.com
Proxy: Proxied
```
**Mục đích**: Trỏ www.dacs-web.onrender.com về Render  
**Cần giữ**: ✅ YES - Cần cho website hoạt động

## Vấn đề với Wildcard `*`

**Wildcard `*` sẽ match TẤT CẢ subdomain**, bao gồm cả `em915.dacs-web.onrender.com`.

**Nhưng**: Nếu bạn thêm record cụ thể `em915`, nó sẽ có **priority cao hơn** wildcard.

## Giải pháp: Thêm Record cụ thể

### Cần thêm record này:

```
Type: CNAME
Name: em915
Target: u57081942.wl193.sendgrid.net
Proxy: DNS only (KHÔNG proxy!)
TTL: Auto
```

**Lưu ý**: Record cụ thể `em915` sẽ override wildcard `*` cho subdomain này.

## Cách thêm vào Cloudflare

1. Vào **DNS** > **Records**
2. Click **"Add record"**
3. Điền:
   - **Type**: `CNAME`
   - **Name**: `em915` (chỉ phần này, không có domain)
   - **Target**: `u57081942.wl193.sendgrid.net`
   - **Proxy status**: **"DNS only"** (màu xám, KHÔNG proxy!)
   - **TTL**: `Auto`
4. Click **"Save"**

## Kiểm tra sau khi thêm

### Trong Cloudflare DNS Records:

Bây giờ bạn sẽ có **4 CNAME records**:

1. ✅ `*` → `gcp-us-west1-1.origin.onrender.com` (Proxied) - Giữ nguyên
2. ✅ `dacs-web.onrender.com` → `gcp-us-west1-1.origin.onrender.com` (Proxied) - Giữ nguyên
3. ✅ `www` → `gcp-us-west1-1.origin.onrender.com` (Proxied) - Giữ nguyên
4. ✅ **`em915` → `u57081942.wl193.sendgrid.net` (DNS only)** - Record mới

### Thêm các records SendGrid khác:

5. ✅ `s1._domainkey` → `s1.domainkey.u57081942.wl193.sendgrid.net` (DNS only)
6. ✅ `s2._domainkey` → `s2.domainkey.u57081942.wl193.sendgrid.net` (DNS only)
7. ✅ `_dmarc` (TXT) → `v=DMARC1; p=none; sp=none; rua=mailto:re+43f616552aef@inbound.dmarcdigests.com; pct=100`

## Priority Rules

**DNS resolution priority**:
1. Record cụ thể (ví dụ: `em915`) - **Priority cao nhất**
2. Record wildcard (`*`) - Priority thấp hơn

Vì vậy, khi lookup `em915.dacs-web.onrender.com`:
- ✅ Sẽ match với record `em915` (nếu đã thêm)
- ❌ KHÔNG match với wildcard `*`

## Checklist

- [ ] Đã giữ nguyên 3 records cũ (không xóa!)
- [ ] Đã thêm record `em915` → `u57081942.wl193.sendgrid.net` (DNS only)
- [ ] Đã thêm record `s1._domainkey` → `s1.domainkey.u57081942.wl193.sendgrid.net` (DNS only)
- [ ] Đã thêm record `s2._domainkey` → `s2.domainkey.u57081942.wl193.sendgrid.net` (DNS only)
- [ ] Đã thêm record `_dmarc` (TXT) với content đầy đủ
- [ ] Tất cả records SendGrid đều **"DNS only"** (không proxy)

## Sau khi thêm xong

1. **Đợi 5-15 phút** để DNS propagate
2. **Check lại** tại MXToolbox:
   - CNAME lookup: `em915.dacs-web.onrender.com`
   - Kết quả phải là: `u57081942.wl193.sendgrid.net`
3. **Verify trong SendGrid** dashboard

## Lưu ý quan trọng

- ❌ **KHÔNG xóa** 3 records cũ - chúng cần cho website hoạt động
- ✅ **Chỉ cần thêm** records SendGrid mới
- ✅ Records SendGrid phải **"DNS only"** (không proxy)
- ✅ Records cụ thể sẽ override wildcard `*`

