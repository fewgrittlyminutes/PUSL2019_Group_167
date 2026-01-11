<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM customer ORDER BY CustomerID DESC");
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management | UtilityPro</title>
    
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
                            <a class="nav-link active" href="customers.php">
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

            <main class="main-content col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="page-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom animate-fade-in">
                    <div>
                        <h1 class="h2 mb-1"><i class="bi bi-people text-primary me-2"></i> Customer Management</h1>
                        <p class="text-muted mb-0">Manage user accounts, addresses, and utility subscriptions.</p>
                    </div>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="bi bi-person-plus me-2"></i>Add New Customer
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-xl overflow-hidden">
                    <div class="card-header bg-white py-3">
                        <div class="row align-items-center">
                            <div class="col"><h5 class="mb-0">Customer Directory (<?php echo count($customers); ?>)</h5></div>
                            <div class="col-auto">
                                <input type="text" class="form-control form-control-sm bg-light" id="customerSearch" placeholder="Search...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">ID</th>
                                        <th>Customer Name</th>
                                        <th>Contact Info</th>
                                        <th>Address</th>
                                        <th>Type</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($customers) > 0): ?>
                                        <?php foreach ($customers as $customer): ?>
                                            <?php 
                                                $initials = strtoupper(substr($customer['FullName'], 0, 2));
                                                $badgeClass = ($customer['CustomerType'] == 'Business') ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success';
                                            ?>
                                            <tr>
                                                <td class="ps-4 text-muted">#<?php echo $customer['CustomerID']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px; font-weight:bold;">
                                                            <?php echo $initials; ?>
                                                        </div>
                                                        <span class="fw-bold"><?php echo htmlspecialchars($customer['FullName']); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="small"><?php echo htmlspecialchars($customer['Email']); ?></div>
                                                    <div class="text-muted x-small"><?php echo htmlspecialchars($customer['Phone']); ?></div>
                                                </td>
                                                <td class="small"><?php echo htmlspecialchars($customer['Address']); ?></td>
                                                <td><span class="badge <?php echo $badgeClass; ?> rounded-pill px-3"><?php echo htmlspecialchars($customer['CustomerType']); ?></span></td>
                                                <td class="text-end pe-4">
                                                    <button class="btn btn-sm btn-light border me-1" 
                                                            onclick="openEditModal(this)"
                                                            data-id="<?php echo $customer['CustomerID']; ?>"
                                                            data-name="<?php echo htmlspecialchars($customer['FullName']); ?>"
                                                            data-email="<?php echo htmlspecialchars($customer['Email']); ?>"
                                                            data-phone="<?php echo htmlspecialchars($customer['Phone']); ?>"
                                                            data-address="<?php echo htmlspecialchars($customer['Address']); ?>"
                                                            data-type="<?php echo $customer['CustomerType']; ?>"
                                                            title="Edit">
                                                        <i class="bi bi-pencil text-primary"></i>
                                                    </button>
                                                    
                                                    <button class="btn btn-sm btn-light border" 
                                                            onclick="deleteCustomer(<?php echo $customer['CustomerID']; ?>)" 
                                                            title="Delete">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center py-4">No customers found.</td></tr>
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

    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Full Name</label>
                                <input type="text" class="form-control" id="newFullName" required>
                            </div>
                            <div class="col-md-6">
                                <label>Type</label>
                                <select class="form-select" id="newCustomerType">
                                    <option value="Household">Household</option>
                                    <option value="Business">Business</option>
                                    <option value="Government">Government</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" id="newEmail">
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" id="newPhone" required>
                            </div>
                            <div class="col-12">
                                <label>Address</label>
                                <input type="text" class="form-control" id="newAddress" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="saveCustomer('create')">Save Customer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerForm">
                        <input type="hidden" id="editId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Full Name</label>
                                <input type="text" class="form-control" id="editFullName" required>
                            </div>
                            <div class="col-md-6">
                                <label>Type</label>
                                <select class="form-select" id="editCustomerType">
                                    <option value="Household">Household</option>
                                    <option value="Business">Business</option>
                                    <option value="Government">Government</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" id="editEmail">
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" id="editPhone" required>
                            </div>
                            <div class="col-12">
                                <label>Address</label>
                                <input type="text" class="form-control" id="editAddress" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="saveCustomer('update')">Update Customer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('customerSearch').addEventListener('keyup', function() {
            let searchValue = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchValue) ? '' : 'none';
            });
        });

        function openEditModal(btn) {
            document.getElementById('editId').value = btn.dataset.id;
            document.getElementById('editFullName').value = btn.dataset.name;
            document.getElementById('editEmail').value = btn.dataset.email;
            document.getElementById('editPhone').value = btn.dataset.phone;
            document.getElementById('editAddress').value = btn.dataset.address;
            document.getElementById('editCustomerType').value = btn.dataset.type;

            new bootstrap.Modal(document.getElementById('editCustomerModal')).show();
        }

        async function saveCustomer(mode) {
            const isEdit = (mode === 'update');
            const prefix = isEdit ? 'edit' : 'new';
            const apiEndpoint = isEdit ? 'api/update_customer.php' : 'api/create_customer.php';

            const data = {
                id: isEdit ? document.getElementById('editId').value : null,
                fullName: document.getElementById(prefix + 'FullName').value,
                customerType: document.getElementById(prefix + 'CustomerType').value,
                email: document.getElementById(prefix + 'Email').value,
                phone: document.getElementById(prefix + 'Phone').value,
                address: document.getElementById(prefix + 'Address').value
            };

            try {
                const response = await fetch(apiEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    alert(isEdit ? "Updated successfully!" : "Created successfully!");
                    location.reload();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                console.error(error);
                alert("Request failed.");
            }
        }

        async function deleteCustomer(id) {
            if (!confirm("Are you sure? This cannot be undone.")) return;

            try {
                const response = await fetch('api/delete_customer.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const result = await response.json();

                if (result.success) {
                    alert("Customer deleted.");
                    location.reload();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                alert("Request failed.");
            }
        }
    </script>
</body>
</html>