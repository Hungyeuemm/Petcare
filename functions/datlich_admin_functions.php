<?php
require_once __DIR__ . '/db_connection.php';

/* ----- LẤY DANH SÁCH ĐẶT LỊCH ----- */
function getAllDatLich()
{
    $conn = getDbConnection();
    $sql = "
        SELECT 
            a.*, 
            c.name_customer AS customer_name, 
            p.petname AS pet_name, 
            s.staff_name, 
            sv.services_name
        FROM appointments a
        LEFT JOIN customers c ON a.customer_id = c.idcustomers
        LEFT JOIN pets p ON a.pet_id = p.id
        LEFT JOIN staffs s ON a.staff_id = s.idstaffs
        LEFT JOIN services sv ON a.service_id = sv.idservices
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";
    $res = $conn->query($sql);
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $res->free();
    }
    return $rows;
}

/* ----- LẤY CHI TIẾT 1 LỊCH ----- */
function getDatLichById($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE idappointments = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return $row;
}

/* ----- DANH SÁCH HỖ TRỢ FORM ----- */
function getCustomers()
{
    $conn = getDbConnection();
    $res = $conn->query("SELECT idcustomers AS id, name_customer AS name FROM customers ORDER BY name_customer");
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $res->free();
    }
    return $rows;
}

function getPets()
{
    $conn = getDbConnection();
    $res = $conn->query("SELECT id, petname AS name, customer_id FROM pets ORDER BY petname");
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $res->free();
    }
    return $rows;
}

function getStaffs()
{
    $conn = getDbConnection();
    $res = $conn->query("SELECT idstaffs AS id, staff_name AS name FROM staffs ORDER BY staff_name");
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $res->free();
    }
    return $rows;
}

function getServices()
{
    $conn = getDbConnection();
    $res = $conn->query("SELECT idservices, services_name FROM services ORDER BY services_name");
    $rows = [];
    if ($res) {
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $res->free();
    }
    return $rows;
}

/* ----- KIỂM TRA TRÙNG LỊCH ----- */
function checkConflict($staff_id, $date, $time, $excludeId = null)
{
    $conn = getDbConnection();
    if ($excludeId) {
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS cnt 
            FROM appointments 
            WHERE staff_id = ? 
              AND appointment_date = ? 
              AND appointment_time = ? 
              AND idappointments != ?
        ");
        $stmt->bind_param("issi", $staff_id, $date, $time, $excludeId);
    } else {
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS cnt 
            FROM appointments 
            WHERE staff_id = ? 
              AND appointment_date = ? 
              AND appointment_time = ?
        ");
        $stmt->bind_param("iss", $staff_id, $date, $time);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return ($row && $row['cnt'] > 0);
}

/* ----- CRUD ----- */
function createDatLich($customer_id, $pet_id, $staff_id, $service_id, $date, $time, $notes)
{
    $conn = getDbConnection();
    $status = 'pending';
    $stmt = $conn->prepare("
        INSERT INTO appointments 
            (customer_id, pet_id, staff_id, service_id, appointment_date, appointment_time, notes, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiissss", $customer_id, $pet_id, $staff_id, $service_id, $date, $time, $notes, $status);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function updateDatLich($id, $customer_id, $pet_id, $staff_id, $service_id, $date, $time, $notes, $status)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        UPDATE appointments 
        SET customer_id=?, pet_id=?, staff_id=?, service_id=?, appointment_date=?, appointment_time=?, notes=?, status=? 
        WHERE idappointments=?
    ");
    $stmt->bind_param("iiiissssi", $customer_id, $pet_id, $staff_id, $service_id, $date, $time, $notes, $status, $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function updateDatLichStatus($id, $status)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE idappointments=?");
    $stmt->bind_param("si", $status, $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function deleteDatLich($id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM appointments WHERE idappointments=?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

function getPendingAppointments()
{
    $conn = getDbConnection();
    $sql = "
        SELECT a.*, 
               c.name_customer AS customer_name,
               p.petname AS pet_name,
               s.staff_name AS staff_name,
               sv.services_name AS services_name
        FROM appointments a
        LEFT JOIN customers c ON a.customer_id = c.idcustomers
        LEFT JOIN pets p ON a.pet_id = p.id
        LEFT JOIN staffs s ON a.staff_id = s.idstaffs
        LEFT JOIN services sv ON a.service_id = sv.idservices
        WHERE a.status = 'pending'
        ORDER BY a.appointment_date, a.appointment_time
    ";
    $result = $conn->query($sql);
    $rows = [];
    if ($result) {
        while ($r = $result->fetch_assoc()) $rows[] = $r;
        $result->free();
    }
    return $rows;
}
function getPetsByCustomer($customer_id)
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, petname AS name FROM pets WHERE customer_id = ? ORDER BY petname");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();
    return $rows;
}
/* --- LẤY DỊCH VỤ CỦA 1 BÁC SĨ --- */
/* --- LẤY DỊCH VỤ CỦA 1 BÁC SĨ --- */
function getServicesByStaff($staff_id) {
    $conn = getDbConnection();
    $sql = "
        SELECT DISTINCT s.idservices, s.services_name
        FROM staffs st
        INNER JOIN roles r ON st.idrole = r.idrole
        INNER JOIN service_role sr ON sr.idroles = r.idrole
        INNER JOIN services s ON sr.idservices = s.idservices
        WHERE st.idstaffs = ?
        ORDER BY s.services_name
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();
    return $rows;
}


/* --- KIỂM TRA BÁC SĨ CÓ LÀM DỊCH VỤ NÀY KHÔNG --- */
function staffCanDoService($staff_id, $service_id) {
    $conn = getDbConnection();
    $sql = "
        SELECT COUNT(*) AS cnt
        FROM staffs st
        INNER JOIN service_role sr ON sr.idroles = st.idrole
        WHERE st.idstaffs = ? AND sr.idservices = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $staff_id, $service_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return ($row && $row['cnt'] > 0);
}
