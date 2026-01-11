<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id)) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM meter WHERE MeterID = ?");
    $stmt->execute([$data->id]);

    echo json_encode(['success' => true, 'message' => 'Meter deleted successfully']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete: This meter has associated bills/readings.']);
}
?>