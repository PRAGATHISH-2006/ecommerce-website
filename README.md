# 🛍️ VibrantShop - Premium E-Commerce Platform

Welcome to **VibrantShop**, a modern, feature-rich e-commerce solution built with PHP and MySQL. This project delivers a seamless shopping experience for users and a powerful management interface for administrators.

![Banner](https://img.freepik.com/free-vector/abstract-colorful-fluid-background_23-2148901720.jpg?w=1200)

---

## 🚀 Key Features

### 👤 User Side
- **Elegant Home Page**: Featuring a dynamic hero section and featured products.
- **Product Discovery**: Browse products by categories with real-time stock status.
- **Seamless Cart**: Add, remove, and manage items in your shopping cart.
- **Secure Checkout**: Integrated checkout process with order confirmation.
- **Order History**: Track your past purchases and their current status.
- **User Authentication**: Secure registration and login system.

### 🛠️ Admin Side
- **Comprehensive Dashboard**: Real-time overview of your store's performance.
- **Product Management**: Create, edit, and delete products easily.
- **Category Control**: Organize products into logical categories with custom icons.
- **Order Tracking**: Manage customer orders from pending to delivered.
- **User Management**: View and manage all registered users.

---

## 🛠️ Tech Stack

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap 5](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

## 🛠️ Installation & Setup

Follow these steps to get your local development environment running:

### 1. Prerequisites
- **XAMPP** or **WAMP** installed on your machine.
- A modern web browser.

### 2. Database Setup
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `kkk_vibrant`.
3. Import the `database.sql` file located in the project root.

### 3. File Configuration
1. Move the project folder to your `htdocs` (XAMPP) or `www` (WAMP) directory.
2. Open `includes/config.php` and ensure the database credentials match your local setup:
   ```php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'kkk_vibrant');
   ```

### 4. Running the App
Open your browser and navigate to:
`http://localhost/kkk` (or your specific folder name)

---

## 🔑 Default Credentials

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin** | `admin` | `password123` |
| **User** | `john_doe` | `password123` |

---
<div align="center">
  Made with ❤️ for a better shopping experience.
</div>
