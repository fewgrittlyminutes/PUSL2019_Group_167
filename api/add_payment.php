<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->billId) || !isset($data->amount) || !isset($data->method)) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO payment (BillID, PaymentDate, AmountPaid, Method) 
        VALUES (?, CURDATE(), ?, ?)
    ");

    $stmt->execute([
        $data->billId,
        $data->amount,
        $data->method
    ]);

    echo json_encode(['success' => true, 'message' => 'Payment recorded successfully!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>