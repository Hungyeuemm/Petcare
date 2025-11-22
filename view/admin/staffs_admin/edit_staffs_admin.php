<?php
require_once __DIR__ . '/../../../functions/db_connection.php';
require_once __DIR__ . '/../../../functions/staffs_admin_functions.php';

// Lấy ID bác sĩ/nhân viên
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../staffs_admin.php?error=Thiếu ID nhân viên");
    exit();
}

$idstaffs = (int)$_GET['id'];
$staff = getStaffById($idstaffs);

if (!$staff) {
    header("Location: ../staffs_admin.php?error=Không tìm thấy nhân viên");
    exit();
}

// Lấy danh sách vai trò
$conn = getDbConnection();
$roles_result = mysqli_query($conn, "SELECT idrole, role_name FROM roles");
$roles = [];
if ($roles_result) {
    while ($row = mysqli_fetch_assoc($roles_result)) {
        $roles[] = $row;
    }
}
mysqli_close($conn);
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
    <style>
        body {
            background-color: #f3f6fc;
            font-family: "Poppins", sans-serif;
        }

        /* Layout chính */
        .layout-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #fff;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-radius: 0 16px 16px 0;
        }

        .sidebar .logo {
            font-weight: 700;
            font-size: 1.4rem;
            color: #007bff;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eaeaea;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .sidebar ul li {
            margin: 6px 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: 500;
        }

        .sidebar ul li.active a,
        .sidebar ul li:hover a {
            background-color: #007bff;
            color: white;
        }

        .sidebar-footer {
            text-align: center;
            font-size: 0.85rem;
            padding: 15px;
            color: #aaa;
            border-top: 1px solid #eee;
        }

        /* Content area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #f3f6fc;
            overflow-y: auto;
        }

        /* Header */
        header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 10px 25px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
        }

        .header-left h5 {
            font-weight: 600;
            margin: 0;
        }

        .search-bar input {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding-left: 35px;
        }

        .search-bar {
            position: relative;
        }

        .search-bar i {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .header-right img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff25;
        }

        /* Main content */
        main {
            flex: 1;
            padding: 25px;
        }

        .content-box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .content-box h6 {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .content-box p {
            color: #555;
        }
    </style>
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
                    <li><a href="./services_admin.php"><i class="bi bi-calendar-check"></i> Bảng dịch vụ</a></li>
                    <li><a href="./datlich.php"><i class="bi bi-calendar-check"></i>Lịch hẹn</a></li>
                    <li class="active"><a href="./staffs_admin.php"><i class="bi bi-person-badge"></i> Bác sĩ</a></li>
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
                    //     if (!isset($_GET['id'])) {
                    //         die("❌ Thiếu ID bác sĩ.");
                    //     }

                    //     $id = $_GET['id'];
                    //     $staff = getStaffById($id);
                    //     if (!$staff) {
                    //         die("❌ Không tìm thấy bác sĩ với ID: $id");
                    //     }

                    //     if (isset($_SESSION['error'])) {
                    //         echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    //     ' . htmlspecialchars($_GET['error']) . '
                    //     <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    // </div>';
                    //         unset($_SESSION['error']);
                    //     }
                    ?>
                    <div class="container mt-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-3 text-primary">Chỉnh sửa thông tin nhân viên / bác sĩ</h5>

                                <form method="POST" action="../../../handle/staffs_admin_process.php">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="idstaffs" value="<?= htmlspecialchars($staff['idstaffs']) ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Tên nhân viên / bác sĩ</label>
                                        <input type="text" name="staff_name" class="form-control"
                                            value="<?= htmlspecialchars($staff['staff_name']) ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" name="phone_staff" class="form-control"
                                            value="<?= htmlspecialchars($staff['phone_staff']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Chức vụ</label>
                                        <input type="text" name="position" class="form-control"
                                            value="<?= htmlspecialchars($staff['position']) ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Vai trò</label>
                                        <select name="idrole" class="form-select" required>
                                            <option value="">-- Chọn vai trò --</option>
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?= $r['idrole'] ?>"
                                                    <?= ($staff['idrole'] ?? '') == $r['idrole'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($r['role_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="../staffs_admin.php" class="btn btn-secondary">Hủy</a>
                                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    </div>
                                </form>
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