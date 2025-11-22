<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../handle/staffs_admin_process.php';

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
$items_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Lấy tổng số bác sĩ và tính tổng số trang
$total_items = handleCountTotalStaffs();
$total_pages = ceil($total_items / $items_per_page);
$current_page = min($current_page, max(1, $total_pages));

// Tính offset và lấy dữ liệu
$offset = ($current_page - 1) * $items_per_page;
$staffs = handleGetStaffsWithPagination($items_per_page, $offset);
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

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
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
                    <a href="../../handle/logout.php" class="btn btn-blue">Đăng xuất</a>
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

                <div class="content-box">
                    <h6>Quản lý bác sĩ (Tổng: <?= $total_items ?>)</h6>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="./staffs_admin/create_staffs_admin.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Thêm bác sĩ
                        </a>
                          <a href="./staffs_admin/import_staffs.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Thêm nhiều
                        </a>

                        <button type="button" id="deleteSelectedBtn" class="btn btn-danger" onclick="deleteSelected()">
                            <i class="fa-solid fa-trash"></i> Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    <!-- <div class="action-buttons">
                        <a href="./staffs_admin/create_staffs_admin.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Thêm nhiều
                        </a>
                    </div> -->

                    <!-- Form xóa nhiều -->
                    <form id="deleteMultipleForm" method="POST" action="../../handle/staffs_admin_process.php">
                        <input type="hidden" name="action" value="delete_multiple">

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Số điện thoại</th>
                                        <th>Chức vụ</th>
                                        <th>Vai trò</th> <!-- Thêm -->
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($staffs)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($staffs as $staff): ?>
                                            <tr>
                                                <td><input type="checkbox" name="selected_ids[]" value="<?= $staff['idstaffs'] ?>" class="staff-checkbox" onchange="updateDeleteButton()"></td>
                                                <td><?= $staff['idstaffs'] ?></td>
                                                <td><?= htmlspecialchars($staff['staff_name']) ?></td>
                                                <td><?= htmlspecialchars($staff['phone_staff']) ?></td>
                                                <td><?= htmlspecialchars($staff['position']) ?></td>
                                                <td><?= htmlspecialchars($staff['role_name'] ?? 'Không rõ') ?></td> <!-- Vai trò -->
                                                <td>
                                                    <a href='./staffs_admin/edit_staffs_admin.php?id=<?= $staff["idstaffs"] ?>' title="Chỉnh sửa">
                                                        <i class="fa-solid fa-pen-nib" style="color: #B197FC;font-size: 1.2rem;"></i>
                                                    </a>
                                                    <a href='../../handle/staffs_admin_process.php?action=delete&id=<?= $staff["idstaffs"] ?>'
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?')"
                                                        title="Xóa">
                                                        <i class="fa-regular fa-trash-can" style="color: #e13737; font-size: 1.2rem;"></i>
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
                                <!-- Nút Previous -->
                                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <!-- Các số trang -->
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <!-- Nút Next -->
                                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                    <!-- Thông tin phân trang -->
                    <div class="text-center text-muted">
                        Hiển thị <?= count($staffs) ?> / <?= $total_items ?> bác sĩ
                        (Trang <?= $current_page ?> / <?= $total_pages ?>)
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Chọn tất cả checkbox
        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.staff-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateDeleteButton();
        }

        // Cập nhật nút xóa
        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.staff-checkbox:checked');
            const deleteBtn = document.getElementById('deleteSelectedBtn');
            const countSpan = document.getElementById('selectedCount');
            const selectAllCheckbox = document.getElementById('selectAll');

            countSpan.textContent = checkboxes.length;

            if (checkboxes.length > 0) {
                deleteBtn.style.display = 'inline-block';
            } else {
                deleteBtn.style.display = 'none';
            }

            // Cập nhật trạng thái checkbox "Chọn tất cả"
            const allCheckboxes = document.querySelectorAll('.staff-checkbox');
            selectAllCheckbox.checked = allCheckboxes.length > 0 &&
                checkboxes.length === allCheckboxes.length;
        }

        // Xóa các mục đã chọn
        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.staff-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Vui lòng chọn ít nhất một bác sĩ để xóa!');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn xóa ${checkboxes.length} bác sĩ đã chọn?`)) {
                document.getElementById('deleteMultipleForm').submit();
            }
        }
    </script>
</body>

</html>