<?php
session_start();
require_once '../functions/db_connection.php';
require_once '../functions/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    $result = processLogin($username, $password);
    
    if ($result['success']) {
        // Nếu là admin thì chuyển đến trang admin
        if ($_SESSION['user']['role'] === 'admin') {
            header("Location: /Baitaplon/view/admin/admin.php");
        } else {
            header("Location: /Baitaplon/view/user_home.php");
        }
        exit();
    } else {
        $_SESSION['error'] = $result['message'];
        header("Location: /Baitaplon/index.php");
        exit();
    }
} else {
    header("Location: /Baitaplon/index.php");
    exit();
}
    
    if ($user) {
        error_log("Login successful for user: " . $username);
        // Lưu thông tin người dùng vào session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        $_SESSION['success'] = "Đăng nhập thành công!";

        // Phân luồng theo vai trò
        if ($user['role'] === 'admin') {
            header("Location: /Baitaplon/view/admin/admin.php");
        } else {
            header("Location: ../view/user_home.php");
        }
        exit();
    } else {
        $_SESSION['login_error'] = true;
        $_SESSION['error_message'] = 'Sai tài khoản hoặc mật khẩu!';
        header("Location: ../index.php");
        exit();
    }

?>
