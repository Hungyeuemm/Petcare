<?php
session_start();
require_once __DIR__ . '/../../../functions/auth.php';
require_once __DIR__ . '/../../../functions/payment_history.php';

if (!isAdmin()) die("Không có quyền!");

$rows = getPaymentHistoryAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Lịch sử thanh toán</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h4>Lịch sử thanh toán</h4>
  <table class="table table-bordered mt-3">
    <thead><tr><th>ID</th><th>Invoice</th><th>Khách</th><th>Số tiền</th><th>Phương thức</th><th>Ngày</th></tr></thead>
    <tbody>
    <?php if (empty($rows)): ?>
      <tr><td colspan="6" class="text-center">Chưa có giao dịch</td></tr>
    <?php else: foreach ($rows as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td>#<?= $r['invoice_id'] ?></td>
        <td><?= htmlspecialchars($r['name_customer'] ?? '-') ?></td>
        <td><?= number_format($r['amount'],0,',','.') ?> đ</td>
        <td><?= htmlspecialchars($r['method']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
