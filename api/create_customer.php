<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->fullName) || !isset($data->phone)) {
    echo json_encode(['success' => false, 'message' => 'Name and Phone are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO customer (FullName, CustomerType, Email, Phone, Address, RegistrationDate) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $data->fullName,
        $data->customerType,
        $data->email,
        $data->phone,
        $data->address
    ]);

    echo json_encode(['success' => true, 'message' => 'Customer added successfully']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>