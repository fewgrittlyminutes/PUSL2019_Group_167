<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'includes/db_connect.php';

try {
    $stmt = $pdo->query("
        SELECT m.MeterID, m.MeterSerial, m.UtilityTypeID, c.FullName 
        FROM meter m 
        JOIN customer c ON m.CustomerID = c.CustomerID
        ORDER BY m.MeterSerial ASC
    ");
    $meters = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Reading | UtilityPro</title>
    
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
                            <a class="nav-link active" href="submit_reading.php">
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
                        <h1 class="h2 mb-1">
                            <i class="bi bi-speedometer text-primary me-2"></i>
                            Submit Meter Reading
                        </h1>
                        <p class="text-muted mb-0">Record usage to automatically generate customer bills.</p>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>
                            New Reading Entry
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="readingForm">
                            <div class="row g-3">
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Select Customer Meter</label>
                                    <select class="form-select" id="meterSelect" required>
                                        <option value="" selected disabled>Choose a meter...</option>
                                        <?php foreach($meters as $meter): ?>
                                            <?php 
                                                $type = ($meter['UtilityTypeID'] == 1) ? 'Electricity' : (($meter['UtilityTypeID'] == 2) ? 'Water' : 'Gas');
                                                $icon = ($meter['UtilityTypeID'] == 1) ? '⚡' : (($meter['UtilityTypeID'] == 2) ? '💧' : '🔥');
                                            ?>
                                            <option value="<?php echo $meter['MeterID']; ?>">
                                                <?php echo $meter['MeterSerial']; ?> - <?php echo htmlspecialchars($meter['FullName']); ?> (<?php echo $icon . ' ' . $type; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Reading Date</label>
                                    <input type="date" class="form-control" id="readingDate" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="col-12 my-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Previous Reading</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control bg-white" id="previousReading" value="-" readonly>
                                                        <span class="input-group-text bg-white text-muted">Units</span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-primary">New Reading</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control border-primary" id="currentReading" step="0.01" placeholder="0.00" required>
                                                        <span class="input-group-text bg-primary text-white">Units</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('readingForm').reset()">
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check-circle me-2"></i>Submit Reading
                                    </button>
                                </div>
                            </div>
                        </form>
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
    
    <script src="js/main.js"></script>
    
    <script>
    document.getElementById('meterSelect').addEventListener('change', async function() {
        const meterId = this.value;
        const prevInput = document.getElementById('previousReading');
        prevInput.value = "Loading...";
        
        try {
            const response = await fetch(`api/get_previous_reading.php?meterId=${meterId}`);
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Server Error (Not JSON)");
            }

            const result = await response.json();
            
            if (result.success) {
                prevInput.value = parseFloat(result.previousReading).toFixed(2);
            } else {
                prevInput.value = "Error";
                alert(result.message);
            }
        } catch (error) {
            console.error(error);
            prevInput.value = "0.00"; 
        }
    });

    document.getElementById('readingForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        const data = {
            meterId: document.getElementById('meterSelect').value,
            readingDate: document.getElementById('readingDate').value,
            currentReading: document.getElementById('currentReading').value
        };

        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('api/add_reading.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            
            if (result.success) {
                alert("✅ Success: " + result.message);
                document.getElementById('readingForm').reset();
                document.getElementById('previousReading').value = "-";
                document.getElementById('readingDate').value = new Date().toISOString().split('T')[0];
            } else {
                alert("❌ Error: " + result.message);
            }
        } catch (error) {
            console.error(error);
            alert("System Error: Could not connect to server.");
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
    </script>
</body>
</html>