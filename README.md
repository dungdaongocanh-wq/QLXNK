# QLXNK - Hệ thống Quản lý Xuất Nhập Khẩu

Phần mềm quản lý tờ khai **Tạm Nhập - Tái Xuất** dành cho doanh nghiệp cho thuê thiết bị máy móc theo quy trình hải quan Việt Nam.

---

## Mô tả Dự án

QLXNK giúp doanh nghiệp theo dõi toàn bộ vòng đời của thiết bị máy móc nhập khẩu tạm thời:

```
Tạm nhập → Lưu kho → Tạm xuất (cho thuê) → Tái nhập (khách trả) → Xuất trả nước ngoài
```

### Tính năng chính

| Module | Chức năng |
|--------|-----------|
| **Tờ khai Tạm nhập** | Import Excel hải quan, theo dõi hạn tái xuất |
| **Tờ khai Tạm xuất** | Quản lý cho thuê thiết bị, cảnh báo hạn tái nhập |
| **Tái nhập** | Ghi nhận khách trả máy về, cập nhật tồn kho |
| **Xuất trả** | Tờ khai xuất trả thiết bị về nước ngoài |
| **Serial Number** | Tra cứu và lịch sử di chuyển từng máy |
| **Báo cáo** | Xuất nhập tồn, doanh thu cho thuê |
| **Cảnh báo** | Email + thông báo trên màn hình trước 30/7 ngày |

---

## Yêu cầu Hệ thống

- **PHP** >= 8.2
- **MySQL** 5.7+ (XAMPP)
- **Composer** >= 2.0
- **Node.js** >= 18 (cho asset build)

---

## Hướng dẫn Cài đặt trên XAMPP

### 1. Clone project vào thư mục XAMPP

```bash
# Copy project vào htdocs (hoặc www)
C:\xampp\htdocs\qlxnk\
```

### 2. Cài đặt PHP Dependencies

```bash
cd C:\xampp\htdocs\qlxnk
composer install
```

### 3. Tạo file `.env`

```bash
cp .env.example .env
php artisan key:generate
```

Chỉnh sửa `.env`:
```env
APP_NAME="QLXNK"
APP_URL=http://localhost/qlxnk/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qlxnk
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Tạo Database

Mở phpMyAdmin hoặc dùng MySQL CLI:
```sql
CREATE DATABASE qlxnk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Chạy Migration và Seed

```bash
php artisan migrate
php artisan db:seed
```

Tài khoản mặc định sau khi seed:
| Email | Password | Role |
|-------|----------|------|
| admin@qlxnk.local | password | admin |
| manager@qlxnk.local | password | manager |
| nhanvien1@qlxnk.local | password | staff |
| kho@qlxnk.local | password | staff |

### 6. Cấu hình Storage

```bash
php artisan storage:link
```

### 7. Cài đặt Frontend Assets (Tùy chọn)

```bash
npm install
npm run build
```

---

## Cấu trúc Database

### Sơ đồ quan hệ

```
departments
    └── users

import_declarations (Tờ khai tạm nhập)
    └── import_declaration_items (Chi tiết mặt hàng)
            └── equipment_serials (Serial number từng máy)
                    └── export_serial_items
                    └── reimport_serial_items
                    └── reexport_items

customers (Khách hàng thuê)
    └── export_declarations (Tờ khai tạm xuất)
            ├── export_declaration_items
            ├── export_serial_items
            └── reimport_records (Phiếu tái nhập)
                    └── reimport_serial_items

reexport_declarations (Tờ khai xuất trả)
    └── reexport_items

notifications
alert_configs
```

### Mô tả các bảng chính

| Bảng | Mô tả |
|------|-------|
| `departments` | Phòng ban |
| `users` | Người dùng (admin/manager/staff) |
| `import_declarations` | Tờ khai tạm nhập - Header |
| `import_declaration_items` | Chi tiết mặt hàng trong tờ khai tạm nhập |
| `equipment_serials` | Serial number từng thiết bị |
| `customers` | Khách hàng thuê thiết bị |
| `export_declarations` | Tờ khai tạm xuất (cho thuê) |
| `export_declaration_items` | Chi tiết mặt hàng trong tờ khai tạm xuất |
| `export_serial_items` | Serial theo từng tờ khai tạm xuất |
| `reimport_records` | Phiếu tái nhập (khách trả hàng) |
| `reimport_serial_items` | Serial trong phiếu tái nhập |
| `reexport_declarations` | Tờ khai xuất trả nước ngoài |
| `reexport_items` | Chi tiết hàng xuất trả |
| `notifications` | Thông báo trong hệ thống |
| `alert_configs` | Cấu hình cảnh báo tự động |

---

## Import File Excel Hải quan

### Mapping dữ liệu - Trang 1 (Header tờ khai)

| Trường | Row | Col | Ví dụ |
|--------|-----|-----|-------|
| Số tờ khai | 4 | E | `10302752870/A11` |
| Mã loại hình | 6 | P | `G12` |
| Mã phân loại kiểm tra | 6 | I | `3D` |
| Tên CQ Hải quan tiếp nhận | 7 | L | `DHHXNKNBHN` |
| Ngày đăng ký | 8 | G | `08/05/2018 15:06:10` |
| Thời hạn tái nhập/tái xuất | 8 | AE | `08/11/2026` |
| Mã người nhập khẩu | 10 | H | `0106425122` |
| Tên người nhập khẩu | 11 | H | `Công ty TNHH Is Korea Rental Vina` |
| Tên người xuất khẩu | 23 | H | `KOREA RENTAL CORP` |
| Nước xuất xứ | 27 | H | `KR` |
| Số vận đơn | 31 | D | `780816009185` |
| Số lượng kiện | 36 | K | `1 PK` |
| Tổng trọng lượng | 37 | K | `21 KGM` |
| Số hóa đơn | 41 | J | `KI18S050402` |
| Đơn vị tiền tệ | 45 | J | `USD` |
| Tổng trị giá | 45 | P | `580` |

### Mapping dữ liệu - Trang 3 & 4 (Chi tiết mặt hàng)

Mỗi mặt hàng bắt đầu bằng tag `<01>`, `<02>` trong cột B/C. 
Offset từ row bắt đầu:

| Trường | Offset | Col | Ví dụ |
|--------|--------|-----|-------|
| HS Code | +1 | G | `90318090` |
| Mô tả hàng hóa | +2 | G | `Bộ đo suy hao RF, model:300-WA-FFN-40, seri:...` |
| Số lượng | +5 | V | `4` |
| Đơn vị | +5 | AE | `PCE` |
| Đơn giá | +7 | V | `20` |
| Trị giá hóa đơn | +7 | I | `80` |
| Nước xuất xứ | +12 | X | `MY` |

### Parse tự động từ mô tả

Từ mô tả: `"Bộ đo suy hao RF, model:300-WA-FFN-40, seri:0205225/0205224/0205327/0205328"`

- **equipment_name**: `Bộ đo suy hao RF` (phần trước "model:")
- **model**: `300-WA-FFN-40` (regex `model[:\s]*([\w\-\.]+)`)
- **serials**: `['0205225', '0205224', '0205327', '0205328']` (tách theo `/`)

---

## API Routes

| Method | URL | Controller | Mô tả |
|--------|-----|------------|-------|
| GET | `/` | DashboardController@index | Dashboard |
| GET | `/import-declarations` | ImportDeclarationController@index | DS tờ khai tạm nhập |
| POST | `/import-declarations/upload-excel` | ImportDeclarationController@uploadExcel | Import Excel |
| GET | `/export-declarations` | ExportDeclarationController@index | DS tờ khai tạm xuất |
| POST | `/reimport` | ReimportController@store | Ghi nhận tái nhập |
| GET | `/reexport-declarations` | ReexportDeclarationController@index | DS tờ khai xuất trả |
| GET | `/serials/search` | SerialController@search | Tìm kiếm serial |
| GET | `/serials/{serial}/history` | SerialController@history | Lịch sử serial |
| GET | `/reports/inventory` | ReportController@inventory | Báo cáo tồn kho |
| GET | `/reports/rental-revenue` | ReportController@rentalRevenue | Báo cáo doanh thu |

---

## Lộ trình Phát triển

| Giai đoạn | Nội dung | Trạng thái |
|-----------|----------|------------|
| **Phase 1** | Database + Models + Migration + Service | ✅ Hoàn thành |
| **Phase 2** | Giao diện tờ khai tạm nhập + Import Excel | 🔄 Tiếp theo |
| **Phase 3** | Giao diện tờ khai tạm xuất + Tái nhập | ⏳ Kế hoạch |
| **Phase 4** | Cảnh báo Email + Hệ thống Notification | ⏳ Kế hoạch |
| **Phase 5** | Hợp đồng PDF + Báo cáo chi tiết | ⏳ Kế hoạch |
| **Phase 6** | Test + Deploy lên hosting | ⏳ Kế hoạch |

---

## Công nghệ Sử dụng

- **Framework**: Laravel 11
- **Database**: MySQL (XAMPP)
- **Excel Parsing**: PhpSpreadsheet ^2.0
- **Authentication**: Laravel Built-in Auth
- **PHP**: >= 8.2