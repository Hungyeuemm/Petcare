<?php
// session_start();
require_once __DIR__ . '/../functions/pet_admin_functions.php';
require_once __DIR__ . '/../functions/customer_admin_functions.php';
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreatePet();
        break;
    case 'edit':
        handleEditPet();
        break;
    case 'delete':
        handleDeletePet();
        break;
        //         // default:
        //         //     header("Location: ../views/student.php?error=Hành động không hợp lệ");
        //         //     exit();
}
function handleGetAllPets()
{
    return getAllPets();
}
// them thu cung
function handleCreatePet()
{
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;
    // echo "Hàm handleCreatePet đã chạy<br>";
    // var_dump($_POST);
    // exit;
    //  var_dump($_POST);
    // exit;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("location: ../view/admin/pet_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['petname']) || !isset($_POST['species']) || !isset($_POST['breed']) || !isset($_POST['petscol']) || !isset($_POST['gender']) || !isset($_POST['birth_date']) || !isset($_POST['weight']) || !isset($_POST['customer_id']) || !isset($_POST['note'])) {
        header("location: ../view/admin/pet_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $petname = $_POST['petname'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $petscol = $_POST['petscol'] ?? '';
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $weight = $_POST['weight'];
    $customer_id = $_POST['customer_id'];

    $note = $_POST['note'];
    $result = createPet($petname, $species, $petscol, $breed, $gender, $birth_date, $weight, $customer_id, $note);
    if ($result) {
        header("location: ../view/admin/pet_admin.php?success=Thêm thú cưng thành công");
    } else {
        header("location: ../view/admin/pet_admin.php?error=Thêm thú cưng thất bại");
    }
    exit();
}
// chinh sua pet
function handleEditPet()
{
    // Xử lý chỉnh sửa thú cưng ở đây
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../view/admin/pet_admin.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['id']) || !isset($_POST['petname']) || !isset($_POST['species']) || !isset($_POST['breed']) || !isset($_POST['petscol']) || !isset($_POST['gender']) || !isset($_POST['birth_date']) || !isset($_POST['weight']) || !isset($_POST['customer_id']) || !isset($_POST['note'])) {
        header("Location: ../view/admin/pet_admin.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $id = $_POST['id'];
    $petname = $_POST['petname'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $petscol = $_POST['petscol'];
    $gender
        = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $weight = $_POST['weight'];
    $customer_id = $_POST['customer_id'];
    $note = $_POST['note'];
    $result = updatePet($id, $petname, $species, $petscol, $breed, $gender, $birth_date, $weight, $customer_id, $note);
    if ($result) {
        header("Location: ../view/admin/pet_admin.php?success=Cập nhật thành công");
    } else {
        header("Location: ../view/admin/pet_admin.php?error=Cập nhật thất bại");
    }
    exit();
}
// xoa pet
function handleDeletePet()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../view/admin/pet_admin.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../view/admin/pet_admin.php?error=Không tìm thấy ID thú cưng");
        exit();
    }
    $id = $_GET['id'];
    $result = deletePet($id);
    if ($result) {
        header("Location: ../view/admin/pet_admin.php?success=Xóa thú cưng thành công");
    } else {
        header("Location: ../view/admin/pet_admin.php?error=Xóa thú cưng thất bại");
    }
    exit();
}
