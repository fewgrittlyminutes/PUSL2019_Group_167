<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$minAmount = $_GET['min_amount'] ?? 100;

try {
    $stmt = $pdo->prepare("CALL ListDefaulters(?)");
    $stmt->execute([$minAmount]);
    $defaulters = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $defaulters]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>