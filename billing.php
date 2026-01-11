<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing | UtilityPro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    
    <link rel="icon" type="image/x-icon" href="https://img.icons8.com/color/96/000000/electricity.png">
</head>
<body class="dashboard-body">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-gradient fixed-top shadow" style="z-index: 1030;">
        <div class="container-fluid">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <div class="logo-icon-sm me-2">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <span class="fw-bold">UtilityPro</span>
            </a>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="avatar-sm me-2">
                            <i class="bi bi-person-circle"></i>
                        </div>
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
                            <a class="nav-link" href="dashboard.php">
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
                            <a class="nav-link active" href="billing.php">
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
                        <h1 class="h2 mb-1">
                            <i class="bi bi-receipt text-primary me-2"></i>
                            Billing Management
                        </h1>
                        <p class="text-muted mb-0">View invoices, track payments, and identify defaulters.</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-danger shadow-sm me-2" onclick="showDefaulters()">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Check Defaulters
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto"><label class="fw-semibold">Filter Status:</label></div>
                            <div class="col-auto">
                                <select class="form-select" id="statusFilter" onchange="loadBills()">
                                    <option value="all">All Bills</option>
                                    <option value="unpaid" selected>Unpaid</option>
                                </select>
                            </div>
                            <div class="col text-end">
                                <small class="text-muted" id="statusIndicator"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Invoice List</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Bill ID</th>
                                        <th>Customer</th>
                                        <th>Utility</th>
                                        <th>Period</th>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="billsTableBody">
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

    <div class="modal fade" id="defaultersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-octagon me-2"></i> High Risk Defaulters</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Customers with outstanding balance > £100</p>
                    <table class="table table-striped">
                        <thead><tr><th>Customer</th><th>Phone</th><th>Address</th><th class="text-end">Total Owed</th></tr></thead>
                        <tbody id="defaultersBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        loadBills();
    });

    async function loadBills() {
        const status = document.getElementById('statusFilter').value;
        const indicator = document.getElementById('statusIndicator');
        const tbody = document.getElementById('billsTableBody');
        
        indicator.textContent = "Loading...";
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><span class="spinner-border text-primary"></span></td></tr>';

        try {
            const response = await fetch(`api/get_bills.php?status=${status}`);
            const result = await response.json();

            if (result.success) {
                indicator.textContent = ""; 
                
                tbody.innerHTML = '';
                
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No bills found.</td></tr>';
                    return;
                }

                result.data.forEach(bill => {
                    let badgeClass = 'bg-secondary';
                    if (bill.Status === 'Paid') badgeClass = 'bg-success';
                    else if (bill.Status === 'Unpaid') badgeClass = 'bg-warning text-dark';
                    else if (bill.Status === 'Overdue') badgeClass = 'bg-danger';

                    let icon = 'lightning-charge';
                    if (bill.UtilityName === 'Water') icon = 'droplet';
                    if (bill.UtilityName === 'Gas') icon = 'fire';

                    const row = `
                        <tr>
                            <td class="ps-4 fw-bold text-muted">#${bill.BillID}</td>
                            <td>${bill.FullName}</td>
                            <td><i class="bi bi-${icon} me-1"></i>${bill.UtilityName}</td>
                            <td>${bill.BillingMonth}</td>
                            <td>${bill.DueDate}</td>
                            <td class="fw-bold">£${bill.TotalAmountFormatted}</td>
                            <td><span class="badge ${badgeClass} rounded-pill px-3">${bill.Status}</span></td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                alert("Error: " + result.message);
                indicator.textContent = "";
            }
        } catch (error) {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Failed to load data.</td></tr>';
            indicator.textContent = "";
        }
    }

    async function showDefaulters() {
        const tbody = document.getElementById('defaultersBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">Calculating...</td></tr>';
        new bootstrap.Modal(document.getElementById('defaultersModal')).show();

        try {
            const response = await fetch('api/get_defaulters.php?min_amount=100');
            const result = await response.json();

            if (result.success) {
                tbody.innerHTML = '';
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No major defaulters found.</td></tr>';
                    return;
                }
                result.data.forEach(d => {
                    tbody.innerHTML += `
                        <tr>
                            <td class="fw-bold">${d.FullName}</td>
                            <td>${d.Phone}</td>
                            <td class="small">${d.Address}</td>
                            <td class="text-end text-danger fw-bold">£${parseFloat(d.OutstandingBalance).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-danger">Error calling stored procedure.</td></tr>';
        }
    }
    </script>
</body>
</html>