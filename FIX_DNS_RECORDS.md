# Fix DNS Records - Kết quả không đúng

## Vấn đề hiện tại

Khi check CNAME lookup cho `em915.dacs-web.onrender.com`, kết quả trả về:
```
CNAME em915.dacs-web.onrender.com → gcp-us-west1-1.origin.onrender.com
```

**Kết quả đúng phải là:**
```
CNAME em915.dacs-web.onrender.com → u57081942.wl193.sendgrid.net
```

## Nguyên nhân

1. **Record chưa được thêm vào Cloudflare** (hoặc thêm sai)
2. **Record cũ vẫn còn** (conflict với record mới)
3. **Proxy đang bật** (Cloudflare redirect thay vì trỏ trực tiếp)
4. **DNS chưa propagate** (ít khả năng vì đã thấy kết quả)

## Cách kiểm tra trong Cloudflare

### Bước 1: Kiểm tra Records hiện tại

1. Đăng nhập Cloudflare Dashboard
2. Chọn domain `dacs-web.onrender.com`
3. Vào **DNS** > **Records**
4. Tìm record có **Name** là `em915`
5. Kiểm tra:
   - **Type** có phải `CNAME` không?
   - **Target** có phải `u57081942.wl193.sendgrid.net` không?
   - **Proxy status** có phải **"DNS only"** (màu xám) không?

### Bước 2: Nếu không thấy record

**Record chưa được thêm**, cần thêm lại:

1. Click **"Add record"**
2. **Type**: `CNAME`
3. **Name**: `em915`
4. **Target**: `u57081942.wl193.sendgrid.net`
5. **Proxy status**: **"DNS only"** (không proxy!)
6. **TTL**: `Auto`
7. Click **"Save"**

### Bước 3: Nếu thấy record nhưng Target sai

**Record bị thêm sai**, cần sửa:

1. Click vào record `em915`
2. Sửa **Target** thành: `u57081942.wl193.sendgrid.net`
3. Đảm bảo **Proxy status** là **"DNS only"**
4. Click **"Save"**

### Bước 4: Nếu thấy nhiều records có Name `em915`

**Có record cũ conflict**, cần xóa record cũ:

1. Tìm tất cả records có Name `em915`
2. **Xóa** record có Target là `gcp-us-west1-1.origin.onrender.com` (hoặc record cũ khác)
3. **Giữ lại** record có Target là `u57081942.wl193.sendgrid.net`
4. Nếu chưa có record SendGrid, thêm mới như Bước 2

### Bước 5: Nếu Proxy đang bật (màu cam)

**Proxy đang redirect**, cần tắt:

1. Click vào record `em915`
2. Toggle **"Proxy"** OFF (chuyển sang **"DNS only"** - màu xám)
3. Click **"Save"**

## Kiểm tra lại sau khi sửa

### Sau 5-15 phút, check lại tại:

1. **MXToolbox**: https://mxtoolbox.com/SuperTool.aspx
   - Chọn **"CNAME Lookup"**
   - Nhập: `em915.dacs-web.onrender.com`
   - **Kết quả đúng phải là**: `u57081942.wl193.sendgrid.net`

2. **DNS Checker**: https://dnschecker.org
   - Chọn **"CNAME"**
   - Nhập: `em915.dacs-web.onrender.com`
   - Xem kết quả trên các DNS servers

### Kết quả mong đợi

```
CNAME em915.dacs-web.onrender.com → u57081942.wl193.sendgrid.net
```

**KHÔNG phải:**
```
CNAME em915.dacs-web.onrender.com → gcp-us-west1-1.origin.onrender.com
```

## Checklist Records cần có

Trong Cloudflare DNS, phải có **4 records**:

1. ✅ `em915` (CNAME) → `u57081942.wl193.sendgrid.net` - **DNS only**
2. ✅ `s1._domainkey` (CNAME) → `s1.domainkey.u57081942.wl193.sendgrid.net` - **DNS only**
3. ✅ `s2._domainkey` (CNAME) → `s2.domainkey.u57081942.wl193.sendgrid.net` - **DNS only**
4. ✅ `_dmarc` (TXT) → `v=DMARC1; p=none; sp=none; rua=mailto:re+43f616552aef@inbound.dmarcdigests.com; pct=100`

**Tất cả đều phải "DNS only" (không proxy)!**

## Nếu vẫn không đúng sau khi sửa

1. **Xóa tất cả records cũ** có Name tương ứng
2. **Thêm lại từ đầu** theo đúng hướng dẫn
3. **Đợi 30 phút** để DNS clear cache
4. **Check lại** tại MXToolbox
5. **Verify trong SendGrid** dashboard

## Verify trong SendGrid

1. Quay lại SendGrid dashboard
2. Vào **Settings** > **Sender Authentication**
3. Xem domain status
4. Nếu records đúng, SendGrid sẽ tự động verify (có thể mất 5-30 phút)

