<?php
require_once __DIR__ . '/../functions/services_admin_functions.php';

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateService();
        break;
    case 'edit':
        handleEditService();
        break;
    case 'delete':
        handleDeleteService();
        break;
    case 'delete-multiple':
        handleDeleteMultipleServices();
        break;
    default:
        header("Location: ../view/admin/services_admin.php?error=Hành động không hợp lệ");
        exit();
}
function handleGetAllServices()
{
    return getAllServices();
}
// chinh sua dich vu
function handleEditService()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/services_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    $idservices = $_POST['idservices'] ?? 0;
    $services_name = $_POST['services_name'] ?? '';
    $price_services = $_POST['price_services'] ?? 0;
    $roleIds = $_POST['role_ids'] ?? [];

    if (!$idservices) {
        header("Location: ../view/admin/services_admin.php?error=Thiếu ID dịch vụ");
        exit();
    }

    $ok1 = updateService($idservices, $services_name, $price_services);
    $ok2 = updateServiceRoles($idservices, $roleIds);

    $msg = ($ok1 && $ok2) ? "Cập nhật thành công" : "Cập nhật thất bại";
    header("Location: ../view/admin/services_admin.php?success=$msg");
    exit();
}
// tạo dịch vụ
function handleCreateService()
{
    // Xử lý tạo dịch vụ ở đây
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/services_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    $services_name = $_POST['services_name'] ?? '';
    $price_services = $_POST['price_services'] ?? 0;
    $roleIds = $_POST['role_ids'] ?? [];

    if (empty($services_name) || empty($price_services)) {
        header("Location: ../view/admin/services_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $result = createServiceWithRoles($services_name, $price_services, $roleIds);
    $msg = $result ? "Thêm dịch vụ thành công" : "Thêm dịch vụ thất bại";
    header("Location: ../view/admin/services_admin.php?success=$msg");
    exit();
}
// xoa dich vu
function handleDeleteService()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../view/admin/services_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/services_admin.php?error=Thiếu ID dịch vụ");
        exit();
    }

    $idservices = $_GET['id'];
    $result = deleteService($idservices);

    if ($result) {
        header("Location: ../view/admin/services_admin.php?success=Xóa dịch vụ thành công");
    } else {
        header("Location: ../view/admin/services_admin.php?error=Xóa dịch vụ thất bại");
    }
    exit();
}
function handleDeleteMultipleServices()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['selected'])) {
        header("Location: ../view/admin/services_admin.php?error=Không có dịch vụ nào được chọn");
        exit();
    }
    $ids = $_POST['selected'];
    $result = deleteMultipleServices($ids);
    $msg = $result ? "Đã xóa " . count($ids) . " dịch vụ." : "Xóa thất bại.";
    header("Location: ../view/admin/services_admin.php?success=$msg");
    exit();
}

// Hàm thực tế trong function
function deleteMultipleServices($ids)
{
    $conn = getDbConnection();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $sql = "DELETE FROM services WHERE idservices IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$ids);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}
