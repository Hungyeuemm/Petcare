<?php
require_once __DIR__ . '/db_connection.php';
// lay danh sach pet
function getAllPets()
{
    $conn = getDbConnection();
    $sql = "SELECT id, customer_id, petname, species, petscol, breed, gender, birth_date, weight, note FROM pets";
    $result = mysqli_query($conn, $sql);

    $pets = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pets[] = $row;
        }
    }
    mysqli_close($conn);
    return $pets;
}
// them pet moi
function createPet($petname, $species, $petscol, $breed, $gender, $birth_date, $weight, $customer_id, $note)
{
 
    $conn = getDbConnection();
    
    // Kiểm tra trùng tên pet
    $checkSql = "SELECT id FROM pets WHERE petname = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "s", $petname);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        return false; // đã tồn tại tên thú cưng
    }
    mysqli_stmt_close($checkStmt);

    // ✅ Thêm thú cưng mới
    $sql = "INSERT INTO pets (petname, species, petscol, breed, gender, birth_date, weight, customer_id, note)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        echo "❌ Lỗi prepare: " . mysqli_error($conn);
        exit;
    }

    // ✅ Ràng buộc đúng thứ tự trước khi execute
    mysqli_stmt_bind_param(
        $stmt,
        "ssssssdis",
        $petname,
        $species,
        $petscol,
        $breed,
        $gender,
        $birth_date,
        $weight,
        $customer_id,
        $note
    );

    // ✅ Execute thật sự
    $success = mysqli_stmt_execute($stmt);

    if (!$success) {
        echo "❌ Lỗi khi thêm thú cưng: " . mysqli_stmt_error($stmt);
        exit;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return true;
}
// lấy id  pet
function getPetById($id)
{
    $conn = getDbConnection();
    $sql = " SELECT id, customer_id, petname, species, petscol, breed, gender, birth_date, weight, note FROM pets WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $pets = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $pets;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}
function updatePet($id, $petname, $species, $petscol, $breed, $gender, $birth_date, $weight, $customer_id, $note)
{
    $conn = getDbConnection();
    $sql = "UPDATE pets SET petname = ?, species = ?, petscol = ?, breed = ?, gender = ?, birth_date = ?, weight = ?, customer_id = ?, note = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssdisi", $petname, $species, $petscol, $breed, $gender, $birth_date, $weight, $customer_id, $note, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}
// xoa pet
function deletePet($id)
{
    $conn = getDbConnection();
    $sql = "DELETE FROM pets WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}
