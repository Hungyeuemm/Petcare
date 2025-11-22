<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/appointments_admin_functions.php';

// Kiểm tra đăng nhập và quyền admin
if (!isAdmin()) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: ../../index.php");
    exit();
}

// Hiển thị thông báo
if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}

if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}

$user = $_SESSION['user'];

// Cấu hình phân trang
$items_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lọc theo trạng thái
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Lấy thống kê theo trạng thái
$status_counts = handleCountAppointmentsByStatus();

// Lấy tổng số lịch hẹn và tính tổng số trang
$total_items = handleCountTotalAppointments($status_filter);
$total_pages = ceil($total_items / $items_per_page);
$current_page = min($current_page, max(1, $total_pages));

// Tính offset và lấy dữ liệu
$offset = ($current_page - 1) * $items_per_page;
$appointments = handleGetAppointmentsWithPagination($items_per_page, $offset, $status_filter);

// Hàm hiển thị badge theo trạng thái
function getStatusBadge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
        case 'confirmed':
            return '<span class="badge bg-primary">Đã duyệt</span>';
        case 'completed':
            return '<span class="badge bg-success">Hoàn thành</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">Đã hủy</span>';
        default:
            return '<span class="badge bg-secondary">Không xác định</span>';
    }
}

// Hàm format ngày giờ
function formatDateTime($date, $time) {
    $dateObj = new DateTime($date);
    return $dateObj->format('d/m/Y') . ' - ' . $time;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch hẹn - PetCare Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <style>
        body {
            background-color: #f3f6fc;
            font-family: "Poppins", sans-serif;
        }
        .layout-wrapper {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
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
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #f3f6fc;
            overflow-y: auto;
        }
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
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
        }
        .stat-card.pending { border-left-color: #ffc107; }
        .stat-card.confirmed { border-left-color: #0d6efd; }
        .stat-card.completed { border-left-color: #198754; }
        .stat-card.cancelled { border-left-color: #dc3545; }
        .stat-card h3 {
            font-size: 2rem;
            margin: 0;
            font-weight: 700;
        }
        .stat-card p {
            margin: 5px 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-tabs a {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            background: #f8f9fa;
            transition: 0.3s;
        }
        .filter-tabs a.active {
            background: #007bff;
            color: white;
        }
        .filter-tabs a:hover {
            background: #0056b3;
            color: white;
        }
        .pagination {
            margin-top: 20px;
            justify-content: center;
        }
        .page-link {
            color: #007bff;
        }
        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        #deleteSelectedBtn {
            display: none;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .action-icons a {
            margin: 0 5px;
            text-decoration: none;
        }
        .action-icons i {
            font-size: 1.2rem;
            cursor: pointer;
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
                    <li class="active"><a href="./customer_admin.php"><i class="bi bi-people"></i> Khách hàng</a></li>
                    <li><a href="./pet_admin.php"><i class="bi bi-heart"></i> Thú cưng</a></li>
                    <li><a href="./services_admin.php"><i class="bi bi-calendar-check"></i> Bảng dịch vụ</a></li>
                    <li><a href="./appointments_admin.php"><i class="bi bi-calendar-check"></i> Lịch hẹn</a></li>
                    <li><a href="./staffs_admin.php"><i class="bi bi-person-badge"></i> Bác sĩ</a></li>
                    <li><a href="#"><i class="bi bi-cash-stack"></i> Thanh toán</a></li>
                    <li><a href="#"><i class="bi bi-graph-up"></i> Báo cáo</a></li>
                    <li><a href="#"><i class="bi bi-gear"></i> Cài đặt</a></li>
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
                    <h5>Quản lý lịch hẹn</h5>
                </div>
                <div class="header-right">
                    <i class="bi bi-bell fs-5"></i>
                    <img src="https://i.ibb.co/yX1mYkS/avatar.png" alt="Admin Avatar">
                </div>
            </header>

            <!-- Nội dung -->
            <main>
                <!-- Hiển thị thông báo -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $success_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error_message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Thống kê -->
                <div class="stats-cards">
                    <div class="stat-card pending">
                        <h3><?= $status_counts['pending'] ?></h3>
                        <p>Chờ duyệt</p>
                    </div>
                    <div class="stat-card confirmed">
                        <h3><?= $status_counts['confirmed'] ?></h3>
                        <p>Đã duyệt</p>
                    </div>
                    <div class="stat-card completed">
                        <h3><?= $status_counts['completed'] ?></h3>
                        <p>Hoàn thành</p>
                    </div>
                    <div class="stat-card cancelled">
                        <h3><?= $status_counts['cancelled'] ?></h3>
                        <p>Đã hủy</p>
                    </div>
                </div>

                <div class="content-box">
                    <h6>Danh sách lịch hẹn (Tổng: <?= $total_items ?>)</h6>
                    
                    <!-- Filter Tabs -->
                    <div class="filter-tabs">
                        <a href="?status=all" class="<?= $status_filter === 'all' ? 'active' : '' ?>">
                            Tất cả (<?= array_sum($status_counts) ?>)
                        </a>
                        <a href="?status=pending" class="<?= $status_filter === 'pending' ? 'active' : '' ?>">
                            Chờ duyệt (<?= $status_counts['pending'] ?>)
                        </a>
                        <a href="?status=confirmed" class="<?= $status_filter === 'confirmed' ? 'active' : '' ?>">
                            Đã duyệt (<?= $status_counts['confirmed'] ?>)
                        </a>
                        <a href="?status=completed" class="<?= $status_filter === 'completed' ? 'active' : '' ?>">
                            Hoàn thành (<?= $status_counts['completed'] ?>)
                        </a>
                        <a href="?status=cancelled" class="<?= $status_filter === 'cancelled' ? 'active' : '' ?>">
                            Đã hủy (<?= $status_counts['cancelled'] ?>)
                        </a>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="./appointments_admin/create_appointments_admin.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Tạo lịch hẹn mới
                        </a>
                        
                        <button type="button" id="deleteSelectedBtn" class="btn btn-danger" onclick="deleteSelected()">
                            <i class="fa-solid fa-trash"></i> Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                    </div>

                    <!-- Form xóa nhiều -->
                    <form id="deleteMultipleForm" method="POST" action="../../handle/appointments_admin_process.php">
                        <input type="hidden" name="action" value="delete_multiple">
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)">
                                        </th>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Thú cưng</th>
                                        <th>Bác sĩ</th>
                                        <th>Dịch vụ</th>
                                        <th>Ngày giờ</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($appointments)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($appointments as $appointment): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_ids[]" 
                                                           value="<?= $appointment["idappointments"] ?>" 
                                                           class="appointment-checkbox" 
                                                           onchange="updateDeleteButton()">
                                                </td>
                                                <td><?= $appointment["idappointments"] ?></td>
                                                <td>
                                                    <?= htmlspecialchars($appointment["customer_name"]) ?>
                                                    <?php if ($appointment["customer_phone"]): ?>
                                                        <br><small class="text-muted"><?= htmlspecialchars($appointment["customer_phone"]) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($appointment["pet_name"]) ?></td>
                                                <td><?= htmlspecialchars($appointment["staff_name"]) ?></td>
                                                <td><?= htmlspecialchars($appointment["name_service"]) ?></td>
                                                <td><?= formatDateTime($appointment["appointment_date"], $appointment["appointment_time"]) ?></td>
                                                <td><?= getStatusBadge($appointment["status"]) ?></td>
                                                <td class="action-icons">
                                                    <?php if ($appointment["status"] === 'pending'): ?>
                                                        <a href='../../handle/appointments_admin_process.php?action=approve&id=<?= $appointment["idappointments"] ?>'
                                                           onclick="return confirm('Xác nhận duyệt lịch hẹn này?')"
                                                           title="Duyệt">
                                                            <i class="fa-solid fa-check" style="color: #198754;"></i>
                                                        </a>
                                                        <a href='../../handle/appointments_admin_process.php?action=reject&id=<?= $appointment["idappointments"] ?>'
                                                           onclick="return confirm('Xác nhận từ chối lịch hẹn này?')"
                                                           title="Từ chối">
                                                            <i class="fa-solid fa-xmark" style="color: #dc3545;"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($appointment["status"] === 'confirmed'): ?>
                                                        <a href='../../handle/appointments_admin_process.php?action=complete&id=<?= $appointment["idappointments"] ?>'
                                                           onclick="return confirm('Đánh dấu hoàn thành?')"
                                                           title="Hoàn thành">
                                                            <i class="fa-solid fa-circle-check" style="color: #198754;"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href='./appointments_admin/edit_appointments_admin.php?id=<?= $appointment["idappointments"] ?>' 
                                                       title="Chỉnh sửa">
                                                        <i class="fa-solid fa-pen-nib" style="color: #B197FC;"></i>
                                                    </a>
                                                    
                                                    <a href='../../handle/appointments_admin_process.php?action=delete&id=<?= $appointment["idappointments"] ?>'
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn này?')"
                                                       title="Xóa">
                                                        <i class="fa-regular fa-trash-can" style="color: #e13737;"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Phân trang -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $current_page - 1 ?>&status=<?= $status_filter ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&status=<?= $status_filter ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $current_page + 1 ?>&status=<?= $status_filter ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                    <div class="text-center text-muted">
                        Hiển thị <?= count($appointments) ?> / <?= $total_items ?> lịch hẹn 
                        (Trang <?= $current_page ?> / <?= max(1, $total_pages) ?>)
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.appointment-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.appointment-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            const countSpan = document.getElementById('selectedCount');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            countSpan.textContent = checkboxes.length;
            
            if (checkboxes.length > 0) {
                deleteBtn.style.display = 'inline-block';
            } else {
                deleteBtn.style.display = 'none';
            }

            const allCheckboxes = document.querySelectorAll('.appointment-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 && 
                                       checkboxes.length === allCheckboxes.length;
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.appointment-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một lịch hẹn để xóa!');
                return;
            }
            
            if (confirm(`Bạn có chắc chắn muốn xóa ${checkboxes.length} lịch hẹn đã chọn?`)) {
                document.getElementById('deleteMultipleForm').submit();
            }   
        }
    </script>
</body>
</html>