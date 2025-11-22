<?php
// session_start();
require_once __DIR__ . '/../functions/customer_admin_functions.php';
$action = '';require_once __DIR__ . '/../functions/pet_admin_functions.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateCustomer();
        break;
    case 'edit':
        handleEditCustomer();
        break;
    case 'delete':
        handleDeleteCustomer();
        break;
        // default:
        //     header("Location: ../views/student.php?error=Hành động không hợp lệ");
        //     exit();
}
function handleGetAllCustomers()
{
    return getAllCustomers();
}
// function handleGetCustomerById($id) {
//     return getCustomerById($id);
// }
// chinh sua khach hang
function handleEditCustomer()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/customer_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['idcustomers']) || !isset($_POST['name_customer']) || !isset($_POST['phone_customer']) || !isset($_POST['email_customer']) || !isset($_POST['address'])) {
        header("Location: ../view/admin/customer_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = $_POST['idcustomers'];
    $user_id = $_POST['user_id'];
    $name_customer = $_POST['name_customer'];
    $phone_customer = $_POST['phone_customer'];
    $email_customer = $_POST['email_customer'];
    $address = $_POST['address'];

    $result = updateCustomer($id, $user_id, $name_customer, $phone_customer, $email_customer, $address);
    if ($result) {
        header("Location: ../view/admin/customer_admin.php?success=Cập nhật thành công");
    } else {
        header("Location: ../view/admin/customer_admin.php?error=Cập nhật thất bại");
    }
    exit();
}
// xoa khach hang
function handleDeleteCustomer()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../view/admin/customer_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../view/admin/customer_admin.php?error=Không tìm thấy ID khách hàng");
        exit();
    }
    $id = $_GET['id'];
    $result = deleteCustomer($id);
    if ($result) {
        header("Location: ../view/admin/customer_admin.php?success=Xóa khách hàng thành công");
    } else {
        header("Location: ../view/admin/customer_admin.php?error=Xóa khách hàng thất bại");
    }
    exit();
}
// them khach hang
// thêm khách hàng mới
function handleCreateCustomer()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/customer_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    // Kiểm tra dữ liệu đầu vào
    if (!isset($_POST['user_id'], $_POST['name_customer'], $_POST['phone_customer'], $_POST['email_customer'], $_POST['address'])) {
        header("Location: ../view/admin/customer_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $user_id = $_POST['user_id'];
    $name_customer = $_POST['name_customer'];
    $phone_customer = $_POST['phone_customer'];
    $email_customer = $_POST['email_customer'];
    $address = $_POST['address'];

    $result = createCustomer($user_id, $name_customer, $phone_customer, $email_customer, $address);

    if ($result === true) {
        header("Location: ../view/admin/customer_admin.php?success=Thêm khách hàng thành công");
    } else {
        header("Location: ../view/admin/customer_admin.php?error=" . urlencode($result));
    }
    exit();
}

