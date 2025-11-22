<?php
// /view/admin/datlich_admin/create_datlich_admin.php
session_start();
require_once __DIR__ . '/../../../functions/auth.php';
require_once __DIR__ . '/../../../functions/datlich_admin_functions.php';
if (!isAdmin()) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: ../../index.php");
    exit();
}
$customers = getCustomers();
$pets = getPets();
$staffs = getStaffs();
$services = getServices();
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
    <link rel="stylesheet" href="../../assets/css/view.css">
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
                    <li class="active"><a href="./pet_admin.php"><i class="bi bi-heart"></i> Thú cưng</a></li>
                    <li><a href="./services_admin.php"><i class="bi bi-calendar-check"></i> Bảng dịch vụ</a></li>
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
                    <?php if (isset($_GET['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="../../../handle/datlich_admin_process.php?action=create">
                                        <div class="mb-3">
                                            <label>Khách hàng</label>
                                            <select name="customer_id" class="form-select" required>
                                                <option value="">-- chọn khách --</option>
                                                <?php foreach ($customers as $c): ?>
                                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Thú cưng</label>
                                            <select name="pet_id" class="form-select" required>
                                                <option value="">-- chọn thú cưng --</option>
                                                <?php foreach ($pets as $p): ?>
                                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>



                                        <div class="mb-3">
                                            <label>Dịch vụ</label>
                                            <select id="service_id" name="service_id" class="form-select" required>

                                                <option value="">-- chọn dịch vụ --</option>
                                                <?php foreach ($services as $sv): ?>
                                                    <option value="<?= $sv['idservices'] ?>"><?= htmlspecialchars($sv['services_name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Bác sĩ</label>
                                            <select id="staff_id" name="staff_id" class="form-select" required>

                                                <option value="">-- chọn bác sĩ --</option>
                                                <?php foreach ($staffs as $s): ?>
                                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Giá dịch vụ:</label>
                                            <input type="text" id="service_price" name="service_price" class="form-control" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Tổng tiền:</label>
                                            <input type="text" id="total_price" name="total_price" class="form-control" readonly>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label>Ngày</label>
                                                <input type="date" name="appointment_date" class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label>Giờ</label>
                                                <input type="time" name="appointment_time" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label>Ghi chú</label>
                                            <textarea name="notes" class="form-control"></textarea>
                                        </div>
                                        <script>
                                            document.addEventListener("DOMContentLoaded", function() {

                                                const serviceSelect = document.getElementById("service_id");
                                                const staffSelect = document.getElementById("staff_id");
                                                const servicePriceInput = document.getElementById("service_price");
                                                const totalPriceInput = document.getElementById("total_price");

                                                // Khi thay đổi dịch vụ → lọc bác sĩ + lấy giá
                                                serviceSelect.addEventListener("change", function() {
                                                    const serviceId = this.value;

                                                    if (!serviceId) {
                                                        staffSelect.innerHTML = '<option value="">--Chọn bác sĩ--</option>';
                                                        servicePriceInput.value = "";
                                                        totalPriceInput.value = "";
                                                        return;
                                                    }

                                                    // 1. Lấy danh sách bác sĩ theo dịch vụ
                                                    fetch(`/Baitaplon/handle/datlich_ajax.php?action=staff_by_service&service_id=${serviceId}`)
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            staffSelect.innerHTML = '<option value="">--Chọn bác sĩ--</option>';
                                                            data.forEach(s => {
                                                                staffSelect.innerHTML += `<option value="${s.idstaffs}">${s.staff_name}</option>`;
                                                            });
                                                        });

                                                    // 2. Lấy giá dịch vụ
                                                    fetch(`/Baitaplon/handle/datlich_ajax.php?action=price_by_service&service_id=${serviceId}`)
                                                        .then(res => res.json())
                                                        .then(data => {
                                                            servicePriceInput.value = data.price;
                                                            totalPriceInput.value = data.price; // vì 1 dịch vụ = tổng tiền
                                                        });
                                                });

                                            });
                                        </script>


                                        <button class="btn btn-success">Tạo lịch (Đang chờ duyệt)</button>
                                        <a href="../datlich.php" class="btn btn-secondary">Quay lại</a>
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