<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in again.']);
    exit;
}

require_once '../includes/db_connect.php';

if (!isset($_GET['meterId'])) {
    echo json_encode(['success' => false, 'message' => 'System Error: No Meter ID sent.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT CurrentReading 
        FROM meterreading 
        WHERE MeterID = ? 
        ORDER BY ReadingDate DESC 
        LIMIT 1
    ");
    
    $stmt->execute([$_GET['meterId']]);
    $reading = $stmt->fetchColumn();

    $lastReading = ($reading !== false) ? $reading : 0;

    echo json_encode([
        'success' => true, 
        'previousReading' => $lastReading
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database Error: ' . $e->getMessage()
    ]);
}
?>