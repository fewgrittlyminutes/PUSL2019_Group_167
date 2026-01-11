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
    <title>Payments | UtilityPro</title>
    
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
                            <a class="nav-link" href="billing.php">
                                <i class="bi bi-receipt me-2"></i>
                                Billing
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link active" href="payments.php">
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
                            <i class="bi bi-credit-card text-primary me-2"></i>
                            Payments
                        </h1>
                        <p class="text-muted mb-0">Record customer transactions and view payment history.</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary shadow-sm" onclick="openPaymentModal()">
                            <i class="bi bi-plus-lg me-2"></i>Record New Payment
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-xl overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Transaction History
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Payment ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Bill Reference</th>
                                        <th>Method</th>
                                        <th class="text-end pe-4">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody">
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

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        <div class="mb-3">
                            <label class="form-label">Select Unpaid Invoice <span class="text-danger">*</span></label>
                            <select class="form-select" id="billSelect" required onchange="updateAmount()">
                                <option value="" selected disabled>Loading invoices...</option>
                            </select>
                            <div class="form-text">List populated from 'unpaidbillssummary' View</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" class="form-control" id="payAmount" step="0.01" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" id="payMethod">
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Online">Online Portal</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="submitPayment()">
                        <i class="bi bi-check-lg me-2"></i>Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="js/main.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        loadPayments();
    });

    async function loadPayments() {
        const tbody = document.getElementById('paymentsTableBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><span class="spinner-border text-primary"></span></td></tr>';

        try {
            const response = await fetch('api/get_payments.php');
            const result = await response.json();

            if (result.success) {
                tbody.innerHTML = '';
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No payment records found.</td></tr>';
                    return;
                }
                result.data.forEach(p => {
                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 text-muted">#${p.PaymentID}</td>
                            <td>${p.PaymentDate}</td>
                            <td class="fw-bold">${p.FullName}</td>
                            <td><span class="badge bg-light text-dark border">Bill #${p.BillID}</span></td>
                            <td>${p.Method}</td>
                            <td class="text-end pe-4 fw-bold text-success">£${p.AmountFormatted}</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error: ${result.message}</td></tr>`;
            }
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">System Error</td></tr>`;
        }
    }

    async function openPaymentModal() {
        const select = document.getElementById('billSelect');
        select.innerHTML = '<option disabled selected>Loading...</option>';
        new bootstrap.Modal(document.getElementById('paymentModal')).show();

        try {
            const response = await fetch('api/get_bills.php?status=unpaid');
            const result = await response.json();

            select.innerHTML = '<option value="" selected disabled>Choose an invoice...</option>';
            
            if (result.success && result.data.length > 0) {
                result.data.forEach(bill => {
                    const option = document.createElement('option');
                    option.value = bill.BillID;
                    option.dataset.amount = bill.TotalAmount;
                    option.text = `#${bill.BillID} - ${bill.FullName} (£${bill.TotalAmountFormatted})`;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option disabled>No unpaid bills found</option>';
            }
        } catch (error) {
            select.innerHTML = '<option disabled>Error loading bills</option>';
        }
    }

    function updateAmount() {
        const select = document.getElementById('billSelect');
        const amountInput = document.getElementById('payAmount');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.dataset.amount) {
            amountInput.value = selectedOption.dataset.amount;
        }
    }

    async function submitPayment() {
        const billId = document.getElementById('billSelect').value;
        const amount = document.getElementById('payAmount').value;
        const method = document.getElementById('payMethod').value;

        if (!billId || !amount) {
            alert("Please select a bill and enter amount.");
            return;
        }

        try {
            const response = await fetch('api/add_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ billId, amount, method })
            });
            const result = await response.json();

            if (result.success) {
                alert("✅ " + result.message);
                location.reload();
            } else {
                alert("Error: " + result.message);
            }
        } catch (error) {
            alert("System Error");
        }
    }
    </script>
</body>
</html>