<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
require_once '../../functions/services_admin_functions.php';

// Kiểm tra đăng nhập và quyền admin
if (!isAdmin()) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: ../../index.php");
    exit();
}
if (isset($_GET['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
        . htmlspecialchars($_GET['success']) .
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    handleDeleteService();
}
// Xử lý phân trang
$limit = 5; // số bản ghi/trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalServices = countAllServices();
$totalPages = ceil($totalServices / $limit);

$services = getServicesPaginated($limit, $offset);
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare Admin Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/view.css">
</head>

<body>
    <div class="layout-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div>
                <div class="logo">🐾 PetCare</div>
                <ul>
                    <li><a href="./admin.php"><i class="bi bi-house-door"></i> Trang chủ</a></li>
                    <li><a href="./account_admin.php"><i class="bi bi-gear"></i> Tài khoản</a></li>
                    <li><a href="./customer_admin.php"><i class="bi bi-people"></i> Khách hàng</a></li>
                    <li><a href="./pet_admin.php"><i class="bi bi-heart"></i> Thú cưng</a></li>
                    <li class="active"><a href="./services_admin.php"><i class="bi bi-calendar-check"></i> Bảng dịch vụ</a></li>
                    <li><a href="./datlich.php"><i class="bi bi-calendar-check"></i>Lịch hẹn</a></li>
                    <li><a href="./staffs_admin.php"><i class="bi bi-person-badge"></i> Bác sĩ</a></li>
                    <!-- <li><a href="/Baitaplon/view/admin/appointments_admin.php"><i class="bi bi-calendar-check"></i> Lịch hẹn</a></li> -->
                    <li><a href="./status_admin.php"><i class="bi bi-cash-stack"></i> Duyệt lịch hẹn</a></li>

                </ul>
            </div>
            <div class="sidebar-footer">
                © 2025 PetCare
            </div>
        </aside>

        <!-- Main -->
        <div class="main-content">
            <!-- Header -->
            <header>
                <div class="header-left">
                    <h5>PetCare Admin Dashboard</h5>
                </div>
                <div class="header-right">
                    <div class="search-bar">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm...">
                    </div>
                    <a href="../../handle/logout.php" class="btn btn-blue">Đăng xuất</a>
                    <i class="bi bi-bell fs-5"></i>
                    <img src="https://i.ibb.co/yX1mYkS/avatar.png" alt="Admin Avatar">
                </div>
            </header>

            <!-- Nội dung -->
            <main class="p-4">
                <div class="content-box">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Danh sách dịch vụ</h5>
                        <div>
                            <a href="./service_admin/create_services_admin.php" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Thêm dịch vụ
                            </a>
                            <button id="delete-selected" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Xóa chọn
                            </button>
                        </div>
                    </div>

                    <form id="delete-form" method="POST" action="../../handle/services_admin_process.php">
                        <input type="hidden" name="action" value="delete-multiple">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>ID</th>
                                    <th>Tên dịch vụ</th>
                                    <th>Giá</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $s): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected[]" value="<?= $s['idservices'] ?>"></td>
                                        <td><?= $s['idservices'] ?></td>
                                        <td><?= htmlspecialchars($s['services_name']) ?></td>
                                        <td><?= number_format($s['price_services'], 0, ',', '.') ?> đ</td>
                                        <td>
                                            <a href="./service_admin/edit_services_admin.php?id=<?= $s['idservices'] ?>" class="text-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="../../handle/services_admin_process.php?action=delete&id=<?= $s['idservices'] ?>" class="text-danger" onclick="return confirm('Xóa dịch vụ này?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>

                    <!-- Phân trang -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src = "../assets/js/view.js"></script>
</body>

</html>