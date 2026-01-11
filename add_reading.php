<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->meterId) || !isset($data->readingDate) || !isset($data->currentReading)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO meterreading (MeterID, ReadingDate, CurrentReading) VALUES (?, ?, ?)");
    $stmt->execute([$data->meterId, $data->readingDate, $data->currentReading]);

    $billingMonth = date('Y-m', strtotime($data->readingDate));
    
    $endDate = $data->readingDate;
    $startDate = date('Y-m-d', strtotime("$endDate -1 month"));
    
    $proc = $pdo->prepare("CALL GenerateBillForMeter(?, ?, ?, ?, ?)");
    $proc->execute([
        $data->meterId, 
        $billingMonth, 
        $startDate, 
        $endDate, 
        date('Y-m-d')
    ]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Reading recorded and Bill generated automatically!']);

} catch (PDOException $e) {
    $pdo->rollBack();
    
    if (strpos($e->getMessage(), 'New meter reading must be greater') !== false) {
        echo json_encode(['success' => false, 'message' => 'Error: New reading cannot be lower than previous reading!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
    }
}
?>