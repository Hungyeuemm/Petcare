<?php
session_start();
require_once '../functions/auth.php';

// Gọi hàm xử lý đăng xuất từ auth.php
$result = handleLogout();

// Chuyển về trang đăng nhập
header("Location: ../index.php");
exit();