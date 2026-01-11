<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->fullName)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE customer 
        SET FullName = ?, CustomerType = ?, Email = ?, Phone = ?, Address = ? 
        WHERE CustomerID = ?
    ");
    
    $stmt->execute([
        $data->fullName,
        $data->customerType,
        $data->email,
        $data->phone,
        $data->address,
        $data->id
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>