<?php
require_once __DIR__ . '/../functions/account_admin_functions.php';
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}
switch ($action) {
    case 'create':
        handleCreateAccount();
        break;
    case 'edit':
        handleEditAccount();
        break;
    case 'delete':
        handleDeleteAccount();
        break;
        // default:
        //     header("Location: ../views/student.php?error=Hành động không hợp lệ");
        //     exit();
}
function handleGetAllAccounts()
{
    return getAllAccounts();
}
function handleEditAccount()
{
    // Xử lý chỉnh sửa tài khoản ở đây
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/account_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['id']) || !isset($_POST['username']) || !isset($_POST['password'])) {
        header("Location: ../view/admin/account_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $result = updateAccount($id, $username, $password);
    if ($result) {
        header("Location: ../view/admin/account_admin.php?success=Cập nhật thành công");
    } else {
        header("Location: ../view/admin/account_admin.php?error=Cập nhật thất bại");
    }
    exit();
}
// them tai khoan
function handleCreateAccount()
{
    // Xử lý tạo tài khoản ở đây
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/account_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        header("Location: ../view/admin/account_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        header("Location: ../view/admin/account_admin.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = addAccount($username, $password);
    if ($result) {
        header("Location: ../view/admin/account_admin.php?success=Tạo tài khoản thành công");
    } else {
        header("Location: ../view/admin/account_admin.php?error=Tạo tài khoản thất bại");
    }
    exit();
}
// xoa tai khoan
function handleDeleteAccount()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../view/admin/account_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../view/admin/account_admin.php?error=Không tìm thấy ID tài khoản");
        exit();
    }
    $id = $_GET['id'];
    // Gọi hàm xóa tài khoản ở đây (hàm chưa được định nghĩa trong functions/account_admin_functions.php)
    $result = deleteAccount($id);
    if ($result) {
        header("Location: ../view/admin/account_admin.php?success=Xóa tài khoản thành công");
    } else {
        header("Location: ../view/admin/account_admin.php?error=Xóa tài khoản thất bại");
    }
    exit();
}
