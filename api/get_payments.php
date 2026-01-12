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
        SELECT 
            p.PaymentID, 
            p.PaymentDate, 
            p.AmountPaid, 
            p.Method, 
            b.BillID, 
            c.FullName 
        FROM payment p
        JOIN bill b ON p.BillID = b.BillID
        JOIN meter m ON b.MeterID = m.MeterID
        JOIN customer c ON m.CustomerID = c.CustomerID
        ORDER BY p.PaymentDate DESC
    ";
    
    $stmt = $pdo->query($query);
    $payments = $stmt->fetchAll();

    foreach ($payments as &$p) {
        $p['AmountFormatted'] = number_format($p['AmountPaid'], 2);
    }

    echo json_encode(['success' => true, 'data' => $payments]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>