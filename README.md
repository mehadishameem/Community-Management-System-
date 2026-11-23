# Yousuf Ali Munshi Bari Foundation Management System (Infinity)

[![PHP Version](https://img.shields.io/badge/PHP-7.4%20%7C%208.x-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange.svg)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap%204.1.3-purple.svg)](https://getbootstrap.com/)
[![Chart.js](https://img.shields.io/badge/Charts-Chart.js-FF6384.svg)](https://www.chartjs.org/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#license)

A comprehensive web-based community management, monthly subscription/contribution tracking, and financial transparency portal developed for **Yousuf Ali Munshi Bari Foundation** (Gunaighar North, Debidwar, Cumilla).

---

## 📌 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
  - [Public Portal](#1-public-portal)
  - [Administrative Dashboard](#2-administrative-dashboard)
- [System Architecture & Tech Stack](#-system-architecture--tech-stack)
- [Directory Structure](#-directory-structure)
- [Database Schema & Setup](#-database-schema--setup)
- [Installation Guide (XAMPP / Localhost)](#-installation-guide-xampp--localhost)
- [Configuration](#-configuration)
- [Usage Workflow](#-usage-workflow)
- [Contact & Support](#-contact--support)
- [License](#-license)

---

## 🌟 Overview

The **Infinity** portal digitizes community records and financial transparency for the **Yousuf Ali Munshi Bari Foundation**. It provides a dual-interface architecture:
1. **Public-facing interface**: Gives community members and donors real-time access to monthly contributions, organizational expenses, vouchers/receipts, donation channels, and interactive analytics.
2. **Administrative interface**: Equips authorized admins with tools to manage member profiles, log and verify monthly contributions, record operational expenses with supporting PDF attachments, and update homepage visuals.

---

## 🚀 Key Features

### 1. Public Portal
- **Interactive Home / Summary (`summary.php`)**:
  - Dynamic image carousel showcasing foundation activities and visual updates.
  - Organizational overview, mission statement, and quick navigational cards.
- **Monthly Contributions (`contribution_current_month.php`)**:
  - Quarterly and month-by-month member contribution breakdown.
  - Filterable by financial year.
  - Color-coded badges indicating payment status (**Paid** / **Due/Pending**).
- **Financial Transparency & Expense Records (`view_expenses.php`)**:
  - Complete list of expenditures with date, category, and cost breakdown.
  - Direct preview and download of official expense vouchers and receipts in PDF format.
  - Balance overview calculating total contributions versus total expenses.
- **Donation Information (`donate.php`)**:
  - Clear guidelines for donating via mobile financial services (bKash) and bank accounts (Sonali Bank PLC).
- **Interactive Analytics (`analytics.php`)**:
  - Powered by **Chart.js** with year-selectable financial visual metrics.
  - Bar and line charts tracking monthly collections, expenses, dues, and paid ratios.
- **About & Contact (`about.php`)**:
  - Foundation location details and verified emergency/committee contacts.

### 2. Administrative Dashboard
- **Secure Authentication (`login.php` & `admin.php`)**:
  - Session-based authentication with role-based access control (Admin-only).
- **Accounts & Payment Matrix (`memberlist_and_payment.php`)**:
  - Matrix view of members across months/quarters.
  - Add or adjust member payments with modal windows.
  - Single-click confirmation of pending payments.
- **Member Directory Management (`members.php` & `adduser.php`)**:
  - Add, edit, search, and delete members.
  - Fields for member name, contact number, category/type, and address.
  - Audit logging (`amendedby` field) for record accountability.
- **Expense Logging (`expense.php`)**:
  - Log expenses with title, description, date, and amount.
  - Direct PDF voucher/receipt upload with automatic file sanitization.
- **Visuals & Banner Manager (`visuals_upload.php`)**:
  - Upload promotional images (`.jpg`, `.jpeg`, `.png`, `.webp`) that dynamically feed into the homepage carousel.

---

## 🛠 System Architecture & Tech Stack

- **Backend**: PHP (Procedural & Object-Oriented MySQLi)
- **Database**: MySQL / MariaDB
- **Frontend**:
  - HTML5 & CSS3
  - [Bootstrap 4.1.3](https://getbootstrap.com/)
  - [Chart.js](https://www.chartjs.org/) (Data visualizations)
  - JavaScript (AJAX dynamic page loader)
- **Supported Web Servers**: Apache (XAMPP / WampServer / LAMP)

---

## 📁 Directory Structure

```plaintext
infinity/
│
├── partials/
│   ├── _dbconnect.php              # Auth database connection (users_cse479 / users)
│   └── _dbconnect2.php             # Core application database connection (community)
│
├── uploads/
│   └── expenses/                   # Stored PDF vouchers & expense receipts
│
├── visuals/                        # Dynamic banner images for homepage carousel
│
├── about.php                       # Contact and foundation information
├── adduser.php                     # Member onboarding form (Admin)
├── admin.php                       # Admin dashboard container and routing
├── analytics.php                   # Chart.js analytics for contributions and costs
├── bkash.png                       # Payment channel branding asset
├── contribution_current_month.php  # Public member contribution grid
├── donate.php                      # Donation instructions and payment channels
├── expense.php                     # Expense recording form with PDF upload
├── index.css                       # Public portal custom styling
├── index.php                       # Main public single-page layout & navigation
├── login.php                       # Admin authentication portal
├── memberlist_and_payment.css      # Admin accounts matrix styles
├── memberlist_and_payment.php      # Admin accounts and payment grid
├── members.php                     # Member management (CRUD & search)
├── sonali.png                      # Bank payment channel branding asset
├── summary.php                     # Homepage summary with dynamic visual slider
├── view_expenses.php               # Expense ledger with PDF view modal
└── visuals_upload.php              # Visual banner uploader for admin
```

---

## 🗄 Database Schema & Setup

The system utilizes MySQL databases for authentication and operations. You can configure them in a single database (e.g., `community`) or split as preconfigured:

### 1. `community` Database Schema

Run the following SQL script in **phpMyAdmin** or MySQL CLI:

```sql
CREATE DATABASE IF NOT EXISTS `community` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `community`;

-- Members Table
CREATE TABLE IF NOT EXISTS `member` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `number` VARCHAR(50) DEFAULT NULL,
  `type` VARCHAR(100) DEFAULT 'General',
  `address` TEXT DEFAULT NULL,
  `amendedby` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payments / Contributions Table
CREATE TABLE IF NOT EXISTS `payment` (
  `id` INT(11) NOT NULL,
  `month` VARCHAR(20) NOT NULL,
  `year` INT(11) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
  `payment_date` DATE DEFAULT NULL,
  `added_by` VARCHAR(100) DEFAULT NULL,
  KEY `member_id_idx` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Expenses Table
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `date` DATE NOT NULL,
  `description` TEXT DEFAULT NULL,
  `total_cost` DECIMAL(10,2) NOT NULL,
  `pdf_path` VARCHAR(255) DEFAULT NULL,
  `uploaded_by` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. Admin Users Table

You can place the `users` table inside the `users_cse479` database (default in `partials/_dbconnect.php`) or inside `community` (if you unify the connection):

```sql
CREATE DATABASE IF NOT EXISTS `users_cse479` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `users_cse479`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `usertype` VARCHAR(50) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin User
INSERT INTO `users` (`username`, `password`, `usertype`) 
VALUES ('admin', 'admin123', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;
```

---

## 💻 Installation Guide (XAMPP / Localhost)

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 7.4 or higher & MySQL)
- Git (optional, for cloning)

### Step-by-Step Installation

1. **Clone or Download Repository**:
   Place the project directory inside your web server root (`htdocs` for XAMPP):
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/mehadishameem/infinity.git
   ```

2. **Start Apache & MySQL**:
   Open the XAMPP Control Panel and start both **Apache** and **MySQL** modules.

3. **Import Databases**:
   - Open your browser and navigate to `http://localhost/phpmyadmin/`.
   - Create databases `community` and `users_cse479`.
   - Run the SQL statements provided in the [Database Schema & Setup](#-database-schema--setup) section.

4. **Verify Folder Permissions**:
   Ensure write permissions exist for upload directories:
   - `uploads/expenses/`
   - `visuals/`

5. **Access the Application**:
   - **Public Portal**: `http://localhost/infinity/`
   - **Admin Login**: `http://localhost/infinity/login.php`

---

## ⚙ Configuration

Database configurations can be modified in the `partials/` folder:

- **`partials/_dbconnect.php`** (User authentication):
  ```php
  $servername = 'localhost';
  $username   = 'root';
  $password   = '';
  $database   = 'users_cse479';
  ```

- **`partials/_dbconnect2.php`** (Core application data):
  ```php
  $servername = 'localhost';
  $username   = 'root';
  $password   = '';
  $database   = 'community';
  ```

> [!TIP]
> If you prefer using a single database, update `$database = "community";` in `_dbconnect.php` and import the `users` table into the `community` database.

---

## 📖 Usage Workflow

1. **Adding Members**:
   - Log into the Admin panel (`/login.php`).
   - Navigate to **MEMBER MANAGEMENT** or **ADD MEMBER**.
   - Fill in the member's name, phone number, category, and address.

2. **Recording Monthly Subscriptions**:
   - Go to **ACCOUNTS** in the Admin panel.
   - Select the financial year and quarter.
   - Click on the contribution cell or edit button to enter payment details (Amount, Date, Status: *Paid* or *Pending*).
   - Use the one-click checkmark to confirm pending amounts once received.

3. **Logging Expenses**:
   - Go to **OTHERS > ADD EXPENSE**.
   - Input expenditure title, amount, date, and description.
   - Attach the receipt voucher in PDF format.
   - Members can verify the expense and view the voucher on the public **আর্থিক সারসংক্ষেপ** page.

4. **Managing Visuals**:
   - Go to **OTHERS > UPLOAD VISUALS**.
   - Upload event or announcement banners; they will automatically rotate on the homepage hero section.

---

## 👨‍💻 Developer & Contact

**Developed by**: Mehadi Hasan Shameem  
**Organization**: Yousuf Ali Munshi Bari Foundation  
**Email**: [srshamim0.23@gmail.com](mailto:srshamim0.23@gmail.com)  
**Profile**: [Facebook Profile](https://www.facebook.com/shamim0.25.inf/)

---


