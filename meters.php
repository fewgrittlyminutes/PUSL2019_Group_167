<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'includes/db_connect.php';

try {
    $custStmt = $pdo->query("SELECT CustomerID, FullName FROM customer ORDER BY FullName");
    $customers = $custStmt->fetchAll();
} catch (PDOException $e) {
    $customers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meter Management | UtilityPro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
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
                            <a class="nav-link active" href="meters.php">
                                <i class="bi bi-hdd me-2"></i> 
                                Meter Management
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="main-content col-md-9 ms-sm-auto col-lg-10 px-md-4">
                
                <div class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom animate-fade-in">
                    <div>
                        <h1 class="h2 mb-1"><i class="bi bi-hdd text-primary me-2"></i> Meter Management</h1>
                        <p class="text-muted mb-0">Assign meters to customers and view hardware inventory.</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addMeterModal">
                            <i class="bi bi-plus-lg me-2"></i>Assign New Meter
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-xl mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search Serial or Customer..." onkeyup="filterTable()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-xl overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Meter Inventory</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Meter ID</th>
                                        <th>Serial Number</th>
                                        <th>Utility Type</th>
                                        <th>Assigned Customer</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="metersTableBody">
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

    <div class="modal fade" id="addMeterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign New Meter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addMeterForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Serial Number</label>
                            <input type="text" class="form-control" id="meterSerial" placeholder="e.g. ELEC-998877" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Utility Type</label>
                            <select class="form-select" id="utilityType" required>
                                <option value="1">Electricity</option>
                                <option value="2">Water</option>
                                <option value="3">Gas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Assign to Customer</label>
                            <select class="form-select" id="customerSelect" required>
                                <option value="" selected disabled>Select a customer...</option>
                                <?php foreach($customers as $c): ?>
                                    <option value="<?php echo $c['CustomerID']; ?>"><?php echo htmlspecialchars($c['FullName']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitMeter()">Assign Meter</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        loadMeters();
    });

    // 1. Load Meters
    async function loadMeters() {
        const tbody = document.getElementById('metersTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border text-primary"></span></td></tr>';

        try {
            const response = await fetch('api/get_meters.php');
            const result = await response.json();

            if (result.success) {
                tbody.innerHTML = '';
                if (result.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No meters found.</td></tr>';
                    return;
                }
                result.data.forEach(m => {
                    let badgeClass = 'bg-secondary';
                    let icon = 'hdd';
                    if(m.UtilityName === 'Electricity') { badgeClass = 'bg-warning text-dark'; icon = 'lightning-charge'; }
                    if(m.UtilityName === 'Water') { badgeClass = 'bg-info text-white'; icon = 'droplet'; }
                    if(m.UtilityName === 'Gas') { badgeClass = 'bg-danger text-white'; icon = 'fire'; }

                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 text-muted">#${m.MeterID}</td>
                            <td class="fw-bold font-monospace">${m.MeterSerial}</td>
                            <td>
                                <span class="badge ${badgeClass} rounded-pill px-3">
                                    <i class="bi bi-${icon} me-1"></i>${m.UtilityName}
                                </span>
                            </td>
                            <td>${m.FullName}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteMeter(${m.MeterID})">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error: ${result.message}</td></tr>`;
            }
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">System Error</td></tr>`;
        }
    }

    // 2. Add Meter
    async function submitMeter() {
        const serial = document.getElementById('meterSerial').value;
        const typeId = document.getElementById('utilityType').value;
        const customerId = document.getElementById('customerSelect').value;

        if(!serial || !customerId) {
            alert("Please fill all fields");
            return;
        }

        try {
            const response = await fetch('api/add_meter.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ serial, typeId, customerId })
            });
            const result = await response.json();

            if (result.success) {
                alert("✅ Success!");
                bootstrap.Modal.getInstance(document.getElementById('addMeterModal')).hide();
                document.getElementById('addMeterForm').reset();
                loadMeters();
            } else {
                alert("Error: " + result.message);
            }
        } catch (error) {
            alert("System Error");
        }
    }

    // 3. Delete Meter
    async function deleteMeter(id) {
        if(!confirm("Are you sure? This will fail if bills exist for this meter.")) return;

        try {
            const response = await fetch('api/delete_meter.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const result = await response.json();

            if (result.success) {
                loadMeters();
            } else {
                alert("Error: " + result.message);
            }
        } catch (error) {
            alert("System Error");
        }
    }

    // 4. Simple Search Filter
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const tbody = document.getElementById('metersTableBody');
        const rows = tbody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const serialCell = rows[i].getElementsByTagName("td")[1];
            const nameCell = rows[i].getElementsByTagName("td")[3];
            if (serialCell && nameCell) {
                const serialText = serialCell.textContent || serialCell.innerText;
                const nameText = nameCell.textContent || nameCell.innerText;
                if (serialText.toLowerCase().indexOf(filter) > -1 || nameText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }       
        }
    }
    </script>
</body>
</html>