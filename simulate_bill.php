<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->utilityTypeId) || !isset($data->consumption)) {
    echo json_encode(['success' => false, 'message' => 'Missing inputs']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT CalculateBillAmount(?, ?) AS EstimatedCost");
    $stmt->execute([$data->utilityTypeId, $data->consumption]);
    
    $result = $stmt->fetch();
    $cost = $result['EstimatedCost'];

    echo json_encode([
        'success' => true, 
        'cost' => number_format($cost, 2)
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>