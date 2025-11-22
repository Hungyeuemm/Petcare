<?php
session_start();
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/invoice_functions.php';

if (!isAdmin()) {
    $_SESSION['error'] = "Không có quyền";
    header("Location: ../../index.php");
    exit();
}

$rows = getAllInvoices(true);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Danh sách hóa đơn</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <div class="d-flex justify-content-between mb-3">
    <h4>Hóa đơn (chưa thanh toán)</h4>
    <div>
      <a href="./invoice_admin/payment_history.php" class="btn btn-outline-secondary">Lịch sử thanh toán</a>
      <a href="./datlich.php" class="btn btn-light">Quay lại Lịch hẹn</a>
    </div>
  </div>

  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Khách</th><th>Tổng</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead>
    <tbody>
    <?php if (empty($rows)): ?>
      <tr><td colspan="5" class="text-center">Chưa có hóa đơn</td></tr>
    <?php else: foreach ($rows as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['name_customer'] ?? '-') ?></td>
        <td><?= number_format($r['total_amount'] ?? 0,0,',','.') ?> đ</td>
        <td><?= htmlspecialchars($r['created_at'] ?? '-') ?></td>
        <td>
          <a class="btn btn-sm btn-primary" href="./invoice_detail.php?id=<?= $r['id'] ?>">Xem</a>
          <a class="btn btn-sm btn-success" href="./invoice_admin/invoice_pay.php?id=<?= $r['id'] ?>">Thanh toán</a>
          <a class="btn btn-sm btn-danger" href="../../handle/delete_invoice.php?id=<?= $r['id'] ?>" onclick="return confirm('Xóa hóa đơn?')">Xóa</a>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
