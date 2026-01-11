<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM monthlyrevenuereport ORDER BY PaymentMonth DESC LIMIT 12");
    $reportData = $stmt->fetchAll();
    
    $totalRev = 0;
    $totalTrans = 0;
    foreach($reportData as $row) { 
        $totalRev += $row['TotalRevenue']; 
        $totalTrans += $row['TotalPayments'];
    }
} catch (PDOException $e) {
    die("Error fetching reports: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics | UtilityPro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">

    <style>
        @media print {
            .navbar, #sidebar, .btn-toolbar, .btn, footer {
                display: none !important;
            }

            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            body, .dashboard-body {
                background-color: white !important;
                color: black !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            .table-responsive {
                overflow: visible !important;
            }

            .bg-primary {
                background-color: #0d6efd !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="dashboard-body">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-gradient fixed-top shadow" style="z-index: 1030;">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="logo-icon-sm me-2"><i class="bi bi-lightning-charge-fill"></i></div>
                <span class="fw-bold">UtilityPro</span>
            </a>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="avatar-sm me-2"><i class="bi bi-person-circle"></i></div>
                        <div class="d-none d-md-block text-start">
                            <div class="small fw-semibold"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin User'); ?></div>
                            <div class="x-small text-muted">System Administrator</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="index.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-5 d-flex flex-column h-100">
                    <ul class="nav flex-column mb-auto">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="customers.php"><i class="bi bi-people me-2"></i> Customers</a></li>
                        <li class="nav-item"><a class="nav-link" href="submit_reading.php"><i class="bi bi-speedometer me-2"></i> Meter Readings</a></li>
                        <li class="nav-item"><a class="nav-link" href="billing.php"><i class="bi bi-receipt me-2"></i> Billing</a></li>
                        <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-credit-card me-2"></i> Payments</a></li>
                        
                        <li class="nav-section">Management</li>
                        <li class="nav-item"><a class="nav-link active" href="reports.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
                        <li class="nav-item"><a class="nav-link" href="tariffs.php"><i class="bi bi-cash-stack me-2"></i> Tariff Plans</a></li>
                        <li class="nav-item"><a class="nav-link" href="meters.php"><i class="bi bi-hdd me-2"></i> Meter Management</a></li>
                    </ul>
                </div>
            </nav>

            <main class="main-content col-md-9 ms-sm-auto col-lg-10 px-md-4">
                
                <div class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom animate-fade-in">
                    <div>
                        <h1 class="h2 mb-1"><i class="bi bi-bar-chart-line text-primary me-2"></i> Financial Reports</h1>
                        <p class="text-muted mb-0">Monthly revenue breakdown by utility type.</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-outline-primary btn-sm me-2" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Report
                        </button>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="card bg-primary text-white border-0 shadow h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 text-uppercase mb-2">Total Revenue (Last 12 Mo)</h6>
                                        <h2 class="display-6 fw-bold mb-0">£<?php echo number_format($totalRev, 2); ?></h2>
                                    </div>
                                    <div class="icon-circle bg-white bg-opacity-25">
                                        <i class="bi bi-currency-pound fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="card bg-white border-0 shadow h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted text-uppercase mb-2">Total Transactions</h6>
                                        <h2 class="display-6 fw-bold mb-0 text-dark"><?php echo number_format($totalTrans); ?></h2>
                                    </div>
                                    <div class="icon-circle bg-success-subtle text-success">
                                        <i class="bi bi-receipt fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-xl overflow-hidden mb-5">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0">Revenue Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Billing Month</th>
                                        <th>Utility Type</th>
                                        <th class="text-center">Transactions</th>
                                        <th class="text-end pe-4">Revenue Generated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($reportData) > 0): ?>
                                        <?php foreach ($reportData as $row): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-muted"><?php echo htmlspecialchars($row['PaymentMonth']); ?></td>
                                                <td>
                                                    <?php 
                                                        $badge = 'bg-secondary';
                                                        if($row['UtilityName'] == 'Electricity') $badge = 'bg-warning text-dark';
                                                        if($row['UtilityName'] == 'Water') $badge = 'bg-info text-white';
                                                        if($row['UtilityName'] == 'Gas') $badge = 'bg-danger text-white';
                                                    ?>
                                                    <span class="badge <?php echo $badge; ?> rounded-pill px-3">
                                                        <?php echo htmlspecialchars($row['UtilityName']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center"><?php echo number_format($row['TotalPayments']); ?></td>
                                                <td class="text-end pe-4 fw-bold text-success">£<?php echo number_format($row['TotalRevenue'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-clipboard-x display-6 d-block mb-3"></i>
                                                No payment data found in the system.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <footer class="pt-4 mt-5 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">© 2025 UtilityPro Management System</p>
                            <small class="text-muted">A comprehensive utility management solution</small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item"><a href="privacy.php" class="text-muted">Privacy Policy</a></li>
                                <li class="list-inline-item"><a href="support.php" class="text-muted">Contact Support</a></li>
                            </ul>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function logout() {
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>