<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

try {
    $query = "
        SELECT m.MeterID, m.MeterSerial, c.FullName, u.UtilityName, u.UtilityTypeID
        FROM meter m
        JOIN customer c ON m.CustomerID = c.CustomerID
        JOIN utilitytype u ON m.UtilityTypeID = u.UtilityTypeID
        ORDER BY m.MeterID DESC
    ";
    
    $stmt = $pdo->query($query);
    $meters = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $meters]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>