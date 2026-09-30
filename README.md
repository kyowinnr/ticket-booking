# 布袋港－澎湖船票訂位系統

Repository: ticket-booking

## 系統目標

建立布袋港 ↔ 澎湖船票線上訂位系統，不包含線上金流。

### 第一階段

- 航線管理
- 船舶管理
- 航次管理
- 票種與票價管理
- 前台船班查詢
- 線上訂位
- 訂單與旅客資料
- 後台訂單管理
- 防止超賣
- 櫃台查詢與訂單確認
- 基礎報表

## 建議技術

- Laravel
- PHP
- MySQL / MariaDB
- Bootstrap

## 核心資料表

- users
- routes
- ships
- trips
- ticket_types
- orders
- order_items
- order_passengers

## 開發原則

每個階段獨立 commit，避免一次修改過多功能。
