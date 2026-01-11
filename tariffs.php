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
    <title>Tariff Plans | UtilityPro</title>
    
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
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="customers.php"><i class="bi bi-people me-2"></i> Customers</a></li>
                        <li class="nav-item"><a class="nav-link" href="submit_reading.php"><i class="bi bi-speedometer me-2"></i> Meter Readings</a></li>
                        <li class="nav-item"><a class="nav-link" href="billing.php"><i class="bi bi-receipt me-2"></i> Billing</a></li>
                        <li class="nav-item"><a class="nav-link" href="payments.php"><i class="bi bi-credit-card me-2"></i> Payments</a></li>
                        <li class="nav-section">Management</li>
                        <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-bar-chart-line me-2"></i> Reports & Analytics</a></li>
                        <li class="nav-item"><a class="nav-link active" href="tariffs.php"><i class="bi bi-cash-stack me-2"></i> Tariff Plans</a></li>
                        <li class="nav-item"><a class="nav-link" href="meters.php"><i class="bi bi-hdd me-2"></i> Meter Management</a></li>
                    </ul>
                </div>
            </nav>

            <main class="main-content col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom animate-fade-in">
                    <div>
                        <h1 class="h2 mb-1"><i class="bi bi-cash-stack text-primary me-2"></i> Tariff Plans</h1>
                        <p class="text-muted mb-0">Manage utility rates and pricing slabs.</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 rounded-xl overflow-hidden mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Current Rate Structures</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Utility Type</th>
                                                <th>Start Range</th>
                                                <th>End Range</th>
                                                <th class="text-end">Rate Per Unit</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tariffTableBody">
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 rounded-xl bg-primary-gradient text-white">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="bi bi-calculator me-2"></i> Bill Estimator</h5>
                                <form id="calculatorForm">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Utility Type</label>
                                        <select class="form-select border-0" id="calcUtility" required>
                                            <option value="1">Electricity</option>
                                            <option value="2">Water</option>
                                            <option value="3">Gas</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Consumption (Units)</label>
                                        <input type="number" class="form-control border-0" id="calcConsumption" placeholder="e.g. 150" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-light text-primary fw-bold">Calculate Cost</button>
                                    </div>
                                </form>

                                <div class="mt-4 pt-3 border-top border-white border-opacity-25" id="calcResult" style="display:none;">
                                    <p class="small mb-0">Estimated Bill:</p>
                                    <h2 class="fw-bold" id="resultAmount">£0.00</h2>
                                </div>
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

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tariff Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editId">
                        <div class="mb-3">
                            <label class="form-label">Slab Start</label>
                            <input type="number" class="form-control" id="editStart" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slab End</label>
                            <input type="number" class="form-control" id="editEnd" required>
                            <div class="form-text">Use 999999 for infinity</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Rate Per Unit (£)</label>
                            <input type="number" step="0.01" class="form-control border-primary" id="editRate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveTariff()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        loadTariffs();
    });

    async function loadTariffs() {
        const tbody = document.getElementById('tariffTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><span class="spinner-border text-primary"></span></td></tr>';

        try {
            const response = await fetch('api/get_tariffs.php');
            
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server Error: Check api/get_tariffs.php");
            }

            const result = await response.json();

            if (result.success) {
                tbody.innerHTML = '';
                result.data.forEach(t => {
                    let icon = 'lightning-charge';
                    let color = 'warning';
                    if(t.UtilityName === 'Water') { icon = 'droplet'; color = 'info'; }
                    if(t.UtilityName === 'Gas') { icon = 'fire'; color = 'danger'; }

                    const rangeEnd = t.SlabEnd > 900000 ? '∞' : t.SlabEnd;
                    
                    const safeObj = JSON.stringify(t).replace(/"/g, '&quot;');

                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-${color}-subtle text-${color} border border-${color}-subtle rounded-pill px-3">
                                    <i class="bi bi-${icon} me-1"></i>${t.UtilityName}
                                </span>
                            </td>
                            <td>${t.SlabStart} units</td>
                            <td>${rangeEnd} units</td>
                            <td class="text-end fw-bold">£${parseFloat(t.RatePerUnit).toFixed(2)}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="openEdit(${safeObj})">
                                    <i class="bi bi-pencil-fill"></i> Edit
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Error: ${result.message}</td></tr>`;
            }
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">System Error: ${error.message}</td></tr>`;
        }
    }

    function openEdit(tariff) {
        document.getElementById('editId').value = tariff.TariffID;
        document.getElementById('editStart').value = tariff.SlabStart;
        document.getElementById('editEnd').value = tariff.SlabEnd;
        document.getElementById('editRate').value = tariff.RatePerUnit;
        
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    async function saveTariff() {
        const id = document.getElementById('editId').value;
        const start = document.getElementById('editStart').value;
        const end = document.getElementById('editEnd').value;
        const rate = document.getElementById('editRate').value;

        try {
            const response = await fetch('api/update_tariff.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, start, end, rate })
            });
            const result = await response.json();

            if (result.success) {
                alert("✅ Saved!");
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                loadTariffs();
            } else {
                alert("Error: " + result.message);
            }
        } catch (error) {
            alert("System Error");
        }
    }

    document.getElementById('calculatorForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Calculating...';
        btn.disabled = true;

        const data = {
            utilityTypeId: document.getElementById('calcUtility').value,
            consumption: document.getElementById('calcConsumption').value
        };

        try {
            const response = await fetch('api/simulate_bill.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            document.getElementById('calcResult').style.display = 'block';
            if (result.success) {
                document.getElementById('resultAmount').innerText = '£' + result.cost;
            } else {
                document.getElementById('resultAmount').innerText = 'Error';
            }
        } catch (error) {
            alert("System Error");
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
    </script>
</body>
</html>