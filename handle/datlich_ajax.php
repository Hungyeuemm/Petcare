<?php
require_once __DIR__ . '/../functions/db_connection.php';
$conn = getDbConnection();
header("Content-Type: application/json; charset=utf-8");

$action = $_GET['action'] ?? '';

if ($action === 'services_by_staff') {
    $staffId = intval($_GET['staff_id']);

    $q1 = "SELECT idrole FROM staffs WHERE idstaffs = ?";
    $stmt = $conn->prepare($q1);
    $stmt->bind_param("i", $staffId);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    $stmt->close();

    if (!$staff) {
        echo json_encode([]);
        exit;
    }

    $roleId = $staff['idrole'];

    $q2 = "
        SELECT s.idservices, s.services_name
        FROM services s
        JOIN service_role sr ON s.idservices = sr.idservices
        WHERE sr.idroles = ?
    ";
    $stmt = $conn->prepare($q2);
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();

    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    $stmt->close();

    echo json_encode($services);
    exit;
}

if ($action === 'staff_by_service') {
    $serviceId = intval($_GET['service_id']);

    $q1 = "SELECT idroles FROM service_role WHERE idservices = ?";
    $stmt = $conn->prepare($q1);
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $role = $result->fetch_assoc();
    $stmt->close();

    if (!$role) {
        echo json_encode([]);
        exit;
    }

    $roleId = $role['idroles'];

    $q2 = "SELECT idstaffs, staff_name FROM staffs WHERE idrole = ?";
    $stmt = $conn->prepare($q2);
    $stmt->bind_param("i", $roleId);
    $stmt->execute();
    $result = $stmt->get_result();

    $staffs = [];
    while ($row = $result->fetch_assoc()) {
        $staffs[] = $row;
    }
    $stmt->close();

    echo json_encode($staffs);
    exit;
}
if ($action == "price_by_service") {
    $id = intval($_GET['service_id']);

    $query = "SELECT price_services FROM services WHERE idservices = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["price" => $row["price_services"]]);
    } else {
        echo json_encode(["price" => 0]);
    }
    exit;
}


echo json_encode(['error' => 'Invalid action']);
