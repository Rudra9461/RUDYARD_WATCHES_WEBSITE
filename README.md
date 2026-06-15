# ⌚ Rudyard Watches — Men's Watch E-Commerce Website

![HTML](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=flat&logo=bootstrap&logoColor=white)

## 🌐 Live Demo
**[👉 View Live Site](http://rudyardwatches.freedev.app)**

---

## 📌 About the Project

A fully responsive men's watch e-commerce website built as a 1st year internship project. Features a premium dark UI with gold accents, interactive elements, and a full PHP+MySQL backend for user registration.

---

## ✨ Features

- 🎨 Premium dark navy + gold UI design
- 📱 Fully responsive (mobile, tablet, desktop)
- 🎴 Interactive flip cards for collection showcase
- 🔍 Live search/filter on price table
- 📝 User registration form with full validation
- 🔒 Secure password hashing (PHP `password_hash`)
- 🛡️ SQL injection prevention (prepared statements)
- 💾 MySQL database integration
- 🌐 Deployed live on InfinityFree hosting

---

## 🛠️ Tech Stack

| Frontend | Backend | Database | Hosting |
|---|---|---|---|
| HTML5, CSS3 | PHP 8.x | MySQL | InfinityFree |
| Bootstrap 5.3 | | | |
| JavaScript (jQuery) | | | |

---

## 📂 Project Structure

RUDYARD_WATCHES_WEBSITE/

│

├── index.html          # Main homepage

├── form.html           # User registration form

├── submit.php          # Form handler (PHP backend)

│

├── config/

│   └── db.example.php  # DB config template (credentials excluded)

│

└── assest/

├── css/style.css   # Custom styles

├── js/style.js     # Form validation + UI interactions

└── images/         # Watch product images


---

## 🚀 Run Locally

1. Clone the repo:
```bash
git clone https://github.com/Rudra9461/RUDYARD_WATCHES_WEBSITE.git
```

2. Set up XAMPP (Apache + MySQL running)

3. Copy project to `htdocs`:
```bash
C:\xampp\htdocs\RUDYARD_WATCHES\
```

4. Create database in phpMyAdmin:
```sql
CREATE DATABASE rudyard_watches;
USE rudyard_watches;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fname VARCHAR(50) NOT NULL,
  lname VARCHAR(50) NOT NULL,
  contact VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  address TEXT NOT NULL,
  pin_code VARCHAR(10) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

5. Copy `config/db.example.php` → `config/db.php` and add your credentials

6. Visit: `http://localhost/RUDYARD_WATCHES/`

---

## 👨‍💻 Developer

**Rudra Pratap Singh**
- GitHub: [@Rudra9461](https://github.com/Rudra9461)
- Email: pratapsinghr782@gmail.com

---

## 📸 Screenshots
![Homepage](assest/images/screenshot.png)
---