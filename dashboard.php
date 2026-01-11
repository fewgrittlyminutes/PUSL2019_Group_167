<?php
// dashboard.php
session_start();

// 1. Security Gatekeeper
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 2. Connect to Database
require_once 'includes/db_connect.php';

// 3. Fetch Real Stats & Chart Data
try {
    // A. Card Stats
    $total_customers = $pdo->query("SELECT COUNT(*) FROM customer")->fetchColumn();
    
    $current_month = date('m');
    $current_year = date('Y');
    $rev_stmt = $pdo->prepare("SELECT SUM(AmountPaid) FROM payment WHERE MONTH(PaymentDate) = ? AND YEAR(PaymentDate) = ?");
    $rev_stmt->execute([$current_month, $current_year]);
    $monthly_revenue = $rev_stmt->fetchColumn() ?: 0;
    
    $pending_bills = $pdo->query("SELECT COUNT(*) FROM bill WHERE Status = 'Unpaid'")->fetchColumn();
    $total_meters = $pdo->query("SELECT COUNT(*) FROM meter")->fetchColumn();

    // B. Chart Data 1: Revenue Overview (Last 6 Months)
    $chart_rev_query = "
        SELECT DATE_FORMAT(PaymentDate, '%M') as Month, SUM(AmountPaid) as Revenue
        FROM payment
        WHERE PaymentDate >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY YEAR(PaymentDate), MONTH(PaymentDate)
        ORDER BY YEAR(PaymentDate), MONTH(PaymentDate)
    ";
    $rev_chart_data = $pdo->query($chart_rev_query)->fetchAll(PDO::FETCH_ASSOC);

    // C. Chart Data 2: Utility Distribution (Meters per Type)
    $chart_util_query = "
        SELECT u.UtilityName, COUNT(m.MeterID) as Count 
        FROM meter m 
        JOIN utilitytype u ON m.UtilityTypeID = u.UtilityTypeID 
        GROUP BY u.UtilityName
    ";
    $util_chart_data = $pdo->query($chart_util_query)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Fallbacks
    $total_customers = $monthly_revenue = $pending_bills = $total_meters = 0;
    $rev_chart_data = [];
    $util_chart_data = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | UtilityPro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    
    <link rel="icon" type="image/x-icon" href="https://img.icons8.com/color/96/000000/electricity.png">
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
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> 
                                Dashboard
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="customers.php">
                                <i class="bi bi-people me-2"></i> 
                                Customers
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="submit_reading.php">
                                <i class="bi bi-speedometer me-2"></i> 
                                Meter Readings
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="billing.php">
                                <i class="bi bi-receipt me-2"></i> 
                                Billing
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="payments.php">
                                <i class="bi bi-credit-card me-2"></i> 
                                Payments
                            </a>
                        </li>
                        
                        <li class="nav-section">Management</li>
                        <li class="nav-item">
                            <a class="nav-link" href="reports.php">
                                <i class="bi bi-bar-chart-line me-2"></i> 
                                Reports & Analytics
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="tariffs.php">
                                <i class="bi bi-cash-stack me-2"></i> 
                                Tariff Plans
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="meters.php">
                                <i class="bi bi-hdd me-2"></i> 
                                Meter Management
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom animate-fade-in">
                    <div>
                        <h1 class="h2 mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i> Dashboard Overview</h1>
                        <p class="text-muted mb-0">Welcome back! Here's what's happening today.</p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card card-1 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div><h6 class="text-muted">Total Customers</h6><h3 class="mb-0"><?php echo number_format($total_customers); ?></h3></div>
                                    <div class="icon-circle bg-primary-gradient"><i class="bi bi-people"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card card-2 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div><h6 class="text-muted">Monthly Revenue</h6><h3 class="mb-0">£<?php echo number_format($monthly_revenue, 2); ?></h3></div>
                                    <div class="icon-circle bg-success-gradient"><i class="bi bi-cash-stack"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card card-3 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div><h6 class="text-muted">Pending Bills</h6><h3 class="mb-0"><?php echo number_format($pending_bills); ?></h3></div>
                                    <div class="icon-circle bg-warning-gradient"><i class="bi bi-receipt"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card card-4 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div><h6 class="text-muted">Active Meters</h6><h3 class="mb-0"><?php echo number_format($total_meters); ?></h3></div>
                                    <div class="icon-circle bg-info-gradient"><i class="bi bi-speedometer"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i> Revenue Overview</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0"><i class="bi bi-pie-chart me-2 text-success"></i> Utility Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="utilityChart" style="max-height: 300px;"></canvas>
                            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Prepare Data from PHP
        const revenueLabels = <?php echo json_encode(array_column($rev_chart_data, 'Month')); ?>;
        const revenueData = <?php echo json_encode(array_column($rev_chart_data, 'Revenue')); ?>;
        
        const utilityLabels = <?php echo json_encode(array_column($util_chart_data, 'UtilityName')); ?>;
        const utilityData = <?php echo json_encode(array_column($util_chart_data, 'Count')); ?>;

        // 2. Revenue Chart (Bar)
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Revenue (£)',
                    data: revenueData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // 3. Utility Distribution Chart (Doughnut)
        new Chart(document.getElementById('utilityChart'), {
            type: 'doughnut',
            data: {
                labels: utilityLabels,
                datasets: [{
                    data: utilityData,
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.7)',  // Yellow (Electricity)
                        'rgba(54, 162, 235, 0.7)',  // Blue (Water)
                        'rgba(255, 99, 132, 0.7)'   // Red (Gas)
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
    </script>
</body>
</html>