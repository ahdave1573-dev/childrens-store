<div align="center">

# 🧸 Children's Store

### *Everything Your Little One Needs — All in One Place!*

---

![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat-square&logo=bootstrap&logoColor=white)

![Status](https://img.shields.io/badge/Status-Active%20%F0%9F%9F%A2-brightgreen?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-yellow?style=flat-square)
![Made With Love](https://img.shields.io/badge/Made%20with-%E2%9D%A4%EF%B8%8F-red?style=flat-square)

</div>

---

## 📖 About The Project

**Children's Store** is a dedicated online shopping platform built specially for kids' products. Parents can easily browse and purchase toys, clothing, books, school supplies, and much more — all from one convenient, easy-to-use platform. The store is designed with a colorful, fun, and child-friendly interface.

> 💡 *Because every child deserves the best!*

---

## 🚀 Features

<table>
<tr>
<td width="50%">

### 👨‍👩‍👧 Customer Side
- ✅ User Registration & Login
- ✅ Browse Kids Products by Category
- ✅ Search & Filter Products
- ✅ Product Detail Page
- ✅ Add / Remove from Cart
- ✅ Place Orders
- ✅ View Order History
- ✅ User Profile Management
- ✅ Mobile Responsive Design

</td>
<td width="50%">

### 🔧 Admin Panel
- ✅ Admin Dashboard
- ✅ Add / Edit / Delete Products
- ✅ Manage Categories
- ✅ View & Process Orders
- ✅ Manage Customers
- ✅ Upload Product Images
- ✅ Stock Management

</td>
</tr>
</table>

---

## 🗂️ Product Categories

| Category | Description |
|---|---|
| 🧸 Toys & Games | Educational & fun toys for all ages |
| 👗 Kids Clothing | Trendy & comfortable outfits |
| 📚 Books | Story books, activity books & more |
| 🎒 School Supplies | Bags, stationery & accessories |
| 🛏️ Baby Products | Essentials for newborns & toddlers |
| 🎨 Art & Craft | Creative kits & drawing supplies |

---

## 🛠️ Built With

| Layer              | Technology                      |
|--------------------|---------------------------------|
| 🎨 Frontend        | HTML5, CSS3, JavaScript         |
| ⚙️ Backend         | PHP                             |
| 🗄️ Database        | MySQL                           |
| 🎨 Styling         | Bootstrap + Custom CSS          |
| 🔧 Version Control | Git & GitHub                    |

---

## 📁 Project Structure

```
childrens-store/
│
├── 📄 index.php                 ← Home Page
├── 📄 login.php                 ← User Login
├── 📄 register.php              ← Registration
├── 📄 logout.php                ← Logout
├── 📄 products.php              ← All Products
├── 📄 product-detail.php        ← Product View
├── 📄 search.php                ← Search Results
├── 📄 cart.php                  ← Shopping Cart
├── 📄 checkout.php              ← Checkout
├── 📄 order-success.php         ← Order Confirmation
├── 📄 profile.php               ← User Profile
├── 📄 my-orders.php             ← Order History
│
├── 📂 admin/
│   ├── index.php                ← Dashboard
│   ├── products.php             ← Manage Products
│   ├── add-product.php          ← Add Product
│   ├── edit-product.php         ← Edit Product
│   ├── categories.php           ← Categories
│   ├── orders.php               ← Manage Orders
│   └── users.php                ← Manage Users
│
├── 📂 includes/
│   ├── db.php                   ← DB Connection
│   ├── header.php               ← Common Header
│   ├── footer.php               ← Common Footer
│   └── functions.php            ← Helper Functions
│
├── 📂 assets/
│   ├── css/                     ← Stylesheets
│   ├── js/                      ← JavaScript Files
│   └── images/                  ← Static Images
│
├── 📂 uploads/                  ← Product Images
├── 📂 database/
│   └── childrens_store.sql      ← Database File
└── 📄 README.md
```

---

## ⚙️ Local Setup

### Prerequisites
- XAMPP / WAMP / LAMP
- PHP >= 7.4
- MySQL >= 5.7
- Git

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/ahdave1573-dev/childrens-store.git
```

**2. Move to server folder**
```
XAMPP (Windows) → C:/xampp/htdocs/childrens-store
XAMPP (Mac)     → /Applications/XAMPP/htdocs/childrens-store
Linux           → /var/www/html/childrens-store
```

**3. Import Database**
- Open `http://localhost/phpmyadmin`
- Create database: `childrens_store`
- Import: `database/childrens_store.sql`

**4. Configure DB Connection** → `includes/db.php`
```php
<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "childrens_store";
$conn = mysqli_connect($host, $username, $password, $database);
?>
```

**5. Run the Project**
```
http://localhost/childrens-store/
```

---

## 🔐 Admin Access

```
URL      →  http://localhost/childrens-store/admin/
Email    →  admin@childrensstore.com
Password →  admin123
```
> ⚠️ Change credentials after first login!

---

## 🔮 Future Plans

- [ ] 💳 Payment Gateway (Razorpay / UPI / PayPal)
- [ ] ⭐ Product Reviews & Ratings
- [ ] ❤️ Wishlist / Favourites
- [ ] 🎟️ Discount Coupon System
- [ ] 📧 Order Email Notifications
- [ ] 📱 Android App Version
- [ ] 🌐 Multi-language Support
- [ ] 🎂 Age-based Product Filtering

---

## 🤝 Contributing

```bash
git checkout -b feature/FeatureName
git commit -m "Add: FeatureName"
git push origin feature/FeatureName
# Then open a Pull Request
```

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

<div align="center">

## 👨‍💻 Developer

| | |
|:---:|:---|
| **Name** | Anshul Dave |
| **Email** | [ahdave1573@gmail.com](mailto:ahdave1573@gmail.com) |
| **GitHub** | [@ahdave1573-dev](https://github.com/ahdave1573-dev) |

---

*Made with ❤️ by **Anshul Dave***

</div>
