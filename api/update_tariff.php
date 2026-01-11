<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->rate)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE tariffplan 
        SET RatePerUnit = ?, SlabStart = ?, SlabEnd = ?
        WHERE TariffID = ?
    ");

    $stmt->execute([
        $data->rate,
        $data->start,
        $data->end,
        $data->id
    ]);

    echo json_encode(['success' => true, 'message' => 'Tariff updated successfully!']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>