<?php
session_start();
require_once __DIR__ . '/../../../functions/auth.php';
require_once __DIR__ . '/../../../functions/services_admin_functions.php';
require_once __DIR__ . '/../../../functions/db_connection.php';
// Kiểm tra đăng nhập và quyền admin
if (!isAdmin()) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: ../../index.php");
    exit();
}
$roles = getAllRoles();
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare Admin Dashboard</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="../../assets/css/view.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
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
                    <i class="bi bi-bell fs-5"></i>
                    <img src="https://i.ibb.co/yX1mYkS/avatar.png" alt="Admin Avatar">
                </div>
            </header>

            <!-- Nội dung -->
            <main>
                <div class="content-box">
                    <h6>Chào mừng trở lại!</h6>
                    <script>
                        // Sau 3 giây sẽ tự động ẩn alert
                        setTimeout(() => {
                            let alertNode = document.querySelector('.alert');
                            if (alertNode) {
                                let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                                bsAlert.close();
                            }
                        }, 3000);
                    </script>
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ' . htmlspecialchars($_GET['error']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
                        unset($_SESSION['error']);
                    }
                    ?>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <form action="../../../handle/services_admin_process.php?action=create" method="POST">
                                        <div class="form-group mb-3">
                                            <label for="services_name" class="form-label">Tên dịch vụ:</label>
                                            <input type="text" name="services_name" id="services_name" class="form-control" required>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="price_services" class="form-label">Giá dịch vụ (VNĐ):</label>
                                            <input type="number" step="0.01" name="price_services" id="price_services" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Vai trò có thể thực hiện</label><br>
                                            <?php foreach ($roles as $role): ?>
                                                <label class="me-3">
                                                    <input type="checkbox" name="role_ids[]" value="<?= $role['idrole'] ?>"> <?= htmlspecialchars($role['role_name']) ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>

                                        <button type="submit" class="btn btn-success">💾 Lưu dịch vụ</button>
                                        <a href="../services_admin.php" class="btn btn-secondary">⬅ Quay lại</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>