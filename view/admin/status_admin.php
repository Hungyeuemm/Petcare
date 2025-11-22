<?php
// /view/admin/datlich_admin/duyet_datlich_admin.php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/datlich_admin_functions.php';

if (!isAdmin()) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: ../../index.php");
    exit();
}

$appointments = getPendingAppointments(); // gọi function riêng
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
                    <li ><a href="./services_admin.php"><i class="bi bi-calendar-check"></i> Bảng dịch vụ</a></li>
                    <li><a href="./datlich.php"><i class="bi bi-calendar-check"></i>Lịch hẹn</a></li>
                    <li ><a href="./staffs_admin.php"><i class="bi bi-person-badge"></i> Bác sĩ</a></li>
                    <!-- <li><a href="/Baitaplon/view/admin/appointments_admin.php"><i class="bi bi-calendar-check"></i> Lịch hẹn</a></li> -->
                    <li class="active"><a href="./status_admin.php"><i class="bi bi-cash-stack"></i> Duyệt lịch hẹn</a></li>
                    
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
            <main>
                <div class="container">
                    <h3 class="mb-4">Duyệt lịch đặt</h3>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                    <?php elseif (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                    <?php endif; ?>

                    <?php if (empty($appointments)): ?>
                        <div class="alert alert-info">Không có lịch nào đang chờ duyệt.</div>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="table-light">
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Thú cưng</th>
                                    <th>Bác sĩ</th>
                                    <th>Dịch vụ</th>
                                    <th>Ngày</th>
                                    <th>Giờ</th>
                                    <th>Ghi chú</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $r): ?>
                                    <tr>
                                        <td><?= $r['idappointments'] ?></td>
                                        <td><?= htmlspecialchars($r['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($r['pet_name']) ?></td>
                                        <td><?= htmlspecialchars($r['staff_name']) ?></td>
                                        <td><?= htmlspecialchars($r['services_name']) ?></td>
                                        <td><?= htmlspecialchars($r['appointment_date']) ?></td>
                                        <td><?= htmlspecialchars($r['appointment_time']) ?></td>
                                        <td><?= htmlspecialchars($r['notes']) ?></td>
                                        <td>
                                            <form method="post" action="../../handle/status_admin_process.php" class="d-inline">
                                                <input type="hidden" name="idappointments" value="<?= $r['idappointments'] ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-success btn-sm">✅ Duyệt</button>
                                            </form>
                                            <form method="post" action="../../handle/status_admin_process.php" class="d-inline">
                                                <input type="hidden" name="idappointments" value="<?= $r['idappointments'] ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger btn-sm">❌ Từ chối</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>