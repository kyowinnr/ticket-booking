# 本機第一次啟動

## 1. 取得專案

在 XAMPP 網站目錄執行：

```bash
cd C:\xampp\htdocs
git clone https://github.com/kyowinnr/ticket-booking.git
cd ticket-booking
```

如果專案已經 clone 過，改用：

```bash
cd C:\xampp\htdocs\ticket-booking
git pull
```

## 2. 安裝 Laravel 套件

確認 PHP 8.2 以上與 Composer 已安裝：

```bash
composer install
```

## 3. 建立環境檔

Windows CMD：

```bat
copy .env.example .env
```

PowerShell：

```powershell
Copy-Item .env.example .env
```

## 4. 建立資料庫

在 phpMyAdmin 建立：

- 資料庫名稱：ticket_booking
- 編碼：utf8mb4
- Collation：utf8mb4_unicode_ci

目前預設使用：

- Host：127.0.0.1
- Port：3306
- User：root
- Password：空白

如果你的 XAMPP MySQL 設定不同，請修改 .env。

## 5. 產生 APP_KEY

```bash
php artisan key:generate
```

## 6. 建立資料表與測試資料

```bash
php artisan migrate --seed
```

這會建立布袋港 ↔ 澎湖的測試航線、測試船、全票／兒童票，以及未來 7 天的測試航次。

## 7. 第一次啟動：先不用設定 Apache

先用 Laravel 內建伺服器確認系統本身可以運作：

```bash
php artisan serve
```

瀏覽器開：

http://127.0.0.1:8000

看到「布袋港－澎湖船票訂位」首頁，就代表 Laravel 基礎環境已經正常。

## 8. 第一次試用

首頁：

1. 選擇日期
2. 選擇「布袋港 → 澎湖」
3. 查詢航次
4. 選擇測試航次
5. 選擇票種與人數
6. 填寫旅客資料
7. 送出訂位

管理後台：

http://127.0.0.1:8000/admin

目前後台包含：

- 航線管理
- 船舶管理
- 票種／票價
- 航次管理
- 櫃台快速操作
- 訂單管理

## 注意

目前尚未加入：

- 線上金流
- 會員登入
- 權限管理
- 正式船班資料
- 電子船票／QR Code
- 正式通知簡訊或 Email

先完成第一次實際操作，再依實際使用流程調整介面與業務規則。
