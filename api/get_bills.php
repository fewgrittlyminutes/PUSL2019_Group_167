<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/db_connect.php';

$status = $_GET['status'] ?? 'all';

try {
    if ($status === 'unpaid') {
        $query = "SELECT * FROM unpaidbillssummary ORDER BY BillDate DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $bills = $stmt->fetchAll();
        
        $data = array_map(function($row) {
            return [
                'BillID' => $row['BillID'],
                'FullName' => $row['CustomerName'],
                'UtilityName' => $row['UtilityName'],
                'BillingMonth' => $row['BillingMonth'],
                'TotalAmount' => $row['TotalAmount'],
                'BillDate' => $row['BillDate'],
                'Status' => 'Unpaid'
            ];
        }, $bills);

    } else {
        $query = "
            SELECT b.BillID, c.FullName, ut.UtilityName, b.BillingMonth, b.TotalAmount, b.BillDate, b.Status 
            FROM bill b
            JOIN meter m ON b.MeterID = m.MeterID
            JOIN customer c ON m.CustomerID = c.CustomerID
            JOIN utilitytype ut ON m.UtilityTypeID = ut.UtilityTypeID
            ORDER BY b.BillDate DESC
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll();
    }

    foreach ($data as &$bill) {
        $billDate = new DateTime($bill['BillDate']);
        $dueDate = clone $billDate;
        $dueDate->modify('+30 days');
        
        $bill['DueDate'] = $dueDate->format('Y-m-d');
        $bill['TotalAmountFormatted'] = number_format($bill['TotalAmount'], 2);
        
        if ($bill['UtilityName'] == 'Electricity') $bill['UtilityClass'] = 'warning text-dark';
        elseif ($bill['UtilityName'] == 'Water') $bill['UtilityClass'] = 'info text-white';
        else $bill['UtilityClass'] = 'danger text-white';
    }

    echo json_encode(['success' => true, 'data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>