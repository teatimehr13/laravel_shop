### 作品集簡介

本作品為簡易電商系統，採 **Laravel 10 + Inertia.js + Vue 3** 開發，前後台獨立界面，整合金流、驗證、圖片管理等功能。

---

#### 前台功能（Tailwind CSS + RWD）：

* **分類導覽與產品列表**：支援主分類／子分類，提供自訂排序。
* **產品詳情頁**：圖像預覽、顏色選項切換等互動設計。
* **購物流程**：

  * 加入購物車、登入後結帳。
  * 串接 **綠界金流（測試環境）**。
  * **補繳功能**：未完成付款可於訂單頁限時補繳。
* **會員系統**：Email 註冊登入，支援社群 OAuth 第三方登入。
* **手機驗證**：串接 **Twilio** 傳送驗證碼。
* **退貨與取消訂單申請**（簡易展示流程）。

---

#### 後台功能（Element Plus）：

* **門市管理**：CRUD + 懶加載功能。
* **產品管理**：

  * 支援分頁、顏色選項與附圖設定。
  * **圖片排序拖曳**、多張圖片整合上傳與管理。
* **分類管理**：

  * 主／子分類結構，支援排序與分頁。
* **訂單管理**：

  * 查詢、詳情查看、條件篩選與分頁顯示。
---
### 資料庫結構設計

本專案使用 MySQL 作為資料庫，所有資料表皆透過 Laravel migration 定義，並可視覺化為下圖 ER 圖：
https://dbdiagram.io/d/6889dd7ecca18e685c6a9cf9

<br>
下列為系統中各模型之間的關聯設計與角色說明：

#### 1. **User 使用者**

* `hasOne(Cart)`：一個用戶對應一個購物車
* `hasMany(Order)`：一個用戶可能有多筆訂單

#### 2. **Cart 購物車**

* `belongsTo(User)`
* `hasMany(CartItem)`

#### 3. **CartItem 購物車項目**

* `belongsTo(Cart)`
* `belongsTo(ProductOption)`

#### 4. **Category 主分類**

* `hasMany(Subcategory)`

#### 5. **Subcategory 子分類**

* `belongsTo(Category)`
* `hasMany(Product)`

#### 6. **Product 商品**

* `belongsTo(Subcategory)`
* `hasMany(ProductOption)`
* `hasMany(ProductImage)`

#### 7. **ProductOption 商品選項（如顏色／尺寸）**

* `belongsTo(Product)`
* `hasMany(CartItem)`
* `hasMany(OrderItem)`
* `belongsToMany(ProductImage)`（透過 `product_option_images` 中介表）

#### 8. **ProductImage 商品圖片**

* `belongsTo(Product)`
* `belongsToMany(ProductOption)`（透過 `product_option_images` 中介表）

#### 9. **Order 訂單**

* `belongsTo(User)`
* `hasMany(OrderItem)`
* `hasMany(Return)`

#### 10. **OrderItem 訂單項目**

* `belongsTo(Order)`
* `belongsTo(ProductOption)`

#### 11. **Return 退貨記錄**

* `belongsTo(Order)`
* `hasMany(ReturnItem)`

#### 12. **ReturnItem 退貨項目**

* `belongsTo(Return)`
* `belongsTo(OrderItem)`

#### 13. **Store 門市**

* 目前為獨立資料表，暫無關聯設計

