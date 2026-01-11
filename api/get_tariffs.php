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
        SELECT t.TariffID, u.UtilityName, t.SlabStart, t.SlabEnd, t.RatePerUnit
        FROM tariffplan t
        JOIN utilitytype u ON t.UtilityTypeID = u.UtilityTypeID
        ORDER BY u.UtilityName, t.SlabStart
    ";
    
    $stmt = $pdo->query($query);
    $tariffs = $stmt->fetchAll();

    echo json_encode(['success' => true, 'data' => $tariffs]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>