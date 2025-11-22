<h2 align="center">
    <a href="https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin">
    🎓 Faculty of Information Technology (DaiNam University)
    </a>
</h2>
<h2 align="center">
    Quản Lý Chăm Sóc Thú Cưng
</h2>
<div align="center">
    <p align="center">
        <img src="doc/aiotlab_logo.png" alt="AIoTLab Logo" width="170"/>
        <img src="doc/fitdnu_logo.png" alt="AIoTLab Logo" width="180"/>
        <img src="doc/dnu_logo.png" alt="DaiNam University Logo" width="200"/>
    </p>

[![AIoTLab](https://img.shields.io/badge/AIoTLab-green?style=for-the-badge)](https://www.facebook.com/DNUAIoTLab)
[![Faculty of Information Technology](https://img.shields.io/badge/Faculty%20of%20Information%20Technology-blue?style=for-the-badge)](https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin)
[![DaiNam University](https://img.shields.io/badge/DaiNam%20University-orange?style=for-the-badge)](https://dainam.edu.vn)

</div>
# 🐾 PetCare – Hệ thống Quản lý Phòng khám Thú cưng

## 📖 1. Giới thiệu
PetCare là hệ thống quản lý phòng khám thú cưng giúp theo dõi khách hàng, thú cưng, lịch hẹn, dịch vụ, thanh toán và hồ sơ khám chữa bệnh.  
Hệ thống mang lại giải pháp hiện đại – nhanh chóng – chính xác.

## 🔧 2. Công nghệ sử dụng
<div align="center">

### Hệ điều hành
![macOS](https://img.shields.io/badge/macOS-000000?style=for-the-badge&logo=macos)
![Windows](https://img.shields.io/badge/Windows-0078D6?style=for-the-badge&logo=windows)
![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu)

### Ngôn ngữ & Framework
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5)
![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3)
![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap)

### Web Server & Database
![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp)

</div>

---

## 🚀 3. Chức năng chính
- Quản lý khách hàng  
- Quản lý thú cưng  
- Quản lý lịch hẹn  
- Quản lý dịch vụ  
- Quản lý hồ sơ khám  
- Quản lý hóa đơn & thanh toán  
- Quản lý tài khoản & phân quyền  
- Thống kê doanh thu – dịch vụ – lịch hẹn  

---

## 📸 4. Giao diện
<img width="1919" height="960" alt="image" src="https://github.com/user-attachments/assets/4ccb9293-0733-4e34-8941-3bb1345cf2ec" />

<img width="1915" height="970" alt="image" src="https://github.com/user-attachments/assets/45f4fcd8-c5ba-4187-8bd5-da917c487e8e" />

<img width="1913" height="967" alt="image" src="https://github.com/user-attachments/assets/0b427c41-9697-4b99-971c-c02488e0411e" />

## ⚙️ 5. Cài đặt

### 5.1. Tải project
```bash
cd C:\xampp\htdocs
git clone https://github.com/Hungyeuemm/Petcare.git
http://localhost/authentication_login.php
## 5.2. Tạo Database

```sql
CREATE DATABASE IF NOT EXISTS petcare
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
5.3. Cấu hình kết nối

config.php

<?php
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "petcare";
    $port = 3306;

    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);

    if (!$conn) {
        die("Kết nối database thất bại: " . mysqli_connect_error());
    }

    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}
?>

5.4. Chạy hệ thống

Khởi động Apache và MySQL trong XAMPP, sau đó truy cập:

http://localhost/index.php
