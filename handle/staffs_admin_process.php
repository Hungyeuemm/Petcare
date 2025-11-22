 <?php
/*require_once __DIR__ . '/../functions/staffs_admin_functions.php';

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateStaff();
        break;
    case 'edit':
        handleEditStaff();
        break;
    case 'delete':
        handleDeleteStaff();
        break;
    case 'delete_multiple':
        handleDeleteMultipleStaffs();
        break;
}

function handleGetAllStaffs() {
    return getAllstaffs();
}

function handleGetStaffsWithPagination($limit, $offset) {
    return getStaffsWithPagination($limit, $offset);
}

function handleCountTotalStaffs() {
    return countTotalStaffs();
}

// chinh sua bac si
function handleEditStaff() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/staffs_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['idstaffs']) || !isset($_POST['staff_name']) || !isset($_POST['phone_staff']) || !isset($_POST['position'])) {
        header("Location: ../view/admin/staffs_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $idstaffs = $_POST['idstaffs'];
    $staff_name = $_POST['staff_name'];
    $phone_staff = $_POST['phone_staff'];
    $position = $_POST['position'];

    $result = updateStaff($idstaffs, $staff_name, $phone_staff, $position);
    if ($result) {
        header("Location: ../view/admin/staffs_admin.php?success=Cập nhật thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Cập nhật thất bại");
    }
    exit();
}

// them bac si
function handleCreateStaff() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/staffs_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['staff_name']) || !isset($_POST['phone_staff']) || !isset($_POST['position'])) {
        header("Location: ../view/admin/staffs_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $staff_name = $_POST['staff_name'];
    $phone_staff = $_POST['phone_staff'];
    $position = $_POST['position'];

    $result = createStaff($staff_name, $phone_staff, $position);
    if ($result) {
        header("Location: ../view/admin/staffs_admin.php?success=Thêm bác sĩ thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Thêm bác sĩ thất bại");
    }
    exit();
}

// xoa bac si
function handleDeleteStaff() {
    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/staffs_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $id = $_GET['id'];
    $result = deleteStaff($id);
    if ($result) {
        header("Location: ../view/admin/staffs_admin.php?success=Xóa bác sĩ thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Xóa bác sĩ thất bại");
    }
    exit();
}

// xoa nhieu bac si
function handleDeleteMultipleStaffs() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/staffs_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['selected_ids']) || empty($_POST['selected_ids'])) {
        header("Location: ../view/admin/staffs_admin.php?error=Chưa chọn bác sĩ nào để xóa");
        exit();
    }
    
    $selected_ids = $_POST['selected_ids'];
    $success_count = deleteMultipleStaffs($selected_ids);
    
    if ($success_count > 0) {
        header("Location: ../view/admin/staffs_admin.php?success=Đã xóa {$success_count} bác sĩ thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Xóa bác sĩ thất bại");
    }
    exit();
}*/
?> 
<?php
require_once __DIR__ . '/../functions/staffs_admin_functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        handleCreateStaff();
        break;
    case 'edit':
        handleEditStaff();
        break;
    case 'delete':
        handleDeleteStaff();
        break;
    case 'delete_multiple':
        handleDeleteMultipleStaffs();
        break;
}

function handleGetAllStaffs() {
    return getAllstaffs();
}

function handleGetStaffsWithPagination($limit, $offset) {
    return getStaffsWithPagination($limit, $offset);
}

function handleCountTotalStaffs() {
    return countTotalStaffs();
}

// Tạo nhân viên
function handleCreateStaff() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/staffs_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    $staff_name = $_POST['staff_name'] ?? '';
    $phone_staff = $_POST['phone_staff'] ?? '';
    $position = $_POST['position'] ?? '';
    $idrole = $_POST['idrole'] ?? '';

    if (empty($staff_name) || empty($idrole)) {
        header("Location: ../view/admin/staffs_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $result = createStaff($staff_name, $phone_staff, $position, $idrole);
    if ($result) {
        header("Location: ../view/admin/staffs_admin.php?success=Thêm nhân viên thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Thêm nhân viên thất bại");
    }
    exit();
}

// Cập nhật nhân viên
function handleEditStaff() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/staffs_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    $id = $_POST['idstaffs'] ?? '';
    $staff_name = $_POST['staff_name'] ?? '';
    $phone_staff = $_POST['phone_staff'] ?? '';
    $position = $_POST['position'] ?? '';
    $idrole = $_POST['idrole'] ?? '';

    if (empty($id) || empty($staff_name) || empty($idrole)) {
        header("Location: ../view/admin/staffs_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $result = updateStaff($id, $staff_name, $phone_staff, $position, $idrole);
    if ($result) {
        header("Location: ../view/admin/staffs_admin.php?success=Cập nhật thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Cập nhật thất bại");
    }
    exit();
}

// Xóa nhân viên
function handleDeleteStaff() {
    if (!isset($_GET['id'])) {
        header("Location: ../view/admin/staffs_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = $_GET['id'];
    $result = deleteStaff($id);
    if ($result) {
        header("Location: ../view/admin/staffs_admin.php?success=Xóa nhân viên thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Xóa nhân viên thất bại");
    }
    exit();
}

// Xóa nhiều nhân viên
function handleDeleteMultipleStaffs() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/staffs_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    $selected_ids = $_POST['selected_ids'] ?? [];
    if (empty($selected_ids)) {
        header("Location: ../view/admin/staffs_admin.php?error=Chưa chọn nhân viên nào để xóa");
        exit();
    }

    $success_count = deleteMultipleStaffs($selected_ids);
    if ($success_count > 0) {
        header("Location: ../view/admin/staffs_admin.php?success=Đã xóa {$success_count} nhân viên thành công");
    } else {
        header("Location: ../view/admin/staffs_admin.php?error=Xóa nhân viên thất bại");
    }
    exit();
}
?>
