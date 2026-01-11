<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->serial) || !isset($data->customerId) || !isset($data->typeId)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $check = $pdo->prepare("SELECT Count(*) FROM meter WHERE MeterSerial = ?");
    $check->execute([$data->serial]);
    if ($check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Error: Meter Serial already exists!']);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO meter (MeterSerial, UtilityTypeID, CustomerID) 
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $data->serial,
        $data->typeId,
        $data->customerId
    ]);

    echo json_encode(['success' => true, 'message' => 'Meter assigned successfully!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>