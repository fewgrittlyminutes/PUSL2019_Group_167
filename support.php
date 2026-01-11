<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Support | UtilityPro</title>
    
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
                            <i class="bi bi-headset text-primary me-2"></i>
                            Contact Support
                        </h1>
                        <p class="text-muted mb-0">Get help with UtilityPro Management System</p>
                    </div>
                    <div class="btn-toolbar">
                        <span class="badge bg-success">Support Available 24/7</span>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="icon-circle bg-primary-gradient mx-auto mb-4">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <h5 class="card-title">Phone Support</h5>
                                <p class="card-text text-muted">Speak directly with our support team</p>
                                <div class="mt-3">
                                    <h3 class="text-primary">1-800-555-UTIL</h3>
                                    <small class="text-muted">(1-800-555-8845)</small>
                                </div>
                                <div class="mt-3">
                                    <small><strong>Hours:</strong> 24/7 Emergency Support</small>
                                    <br>
                                    <small><strong>Average Wait:</strong> &lt; 2 minutes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="icon-circle bg-success-gradient mx-auto mb-4">
                                    <i class="bi bi-envelope"></i>
                                </div>
                                <h5 class="card-title">Email Support</h5>
                                <p class="card-text text-muted">Send us an email for non-urgent issues</p>
                                <div class="mt-3">
                                    <h6 class="text-success">support@utilitypro.com</h6>
                                    <p class="small text-muted mt-2">For technical support and billing inquiries</p>
                                </div>
                                <div class="mt-3">
                                    <small><strong>Response Time:</strong> Within 4 hours</small>
                                    <br>
                                    <small><strong>Priority:</strong> Medium urgency</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i> Our Offices</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6>Headquarters</h6>
                                        <address class="small">
                                            123 Utility Street<br>
                                            Energy City, EC 12345<br>
                                            United States<br>
                                            <i class="bi bi-telephone"></i> +1 (800) 555-UTIL<br>
                                            <i class="bi bi-envelope"></i> hq@utilitypro.com
                                        </address>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>European Office</h6>
                                        <address class="small">
                                            456 Power Avenue<br>
                                            London, UK EC1A 1BB<br>
                                            United Kingdom<br>
                                            <i class="bi bi-telephone"></i> +44 20 7946 0958<br>
                                            <i class="bi bi-envelope"></i> europe@utilitypro.com
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-clock me-2"></i> Support Hours</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <td><strong>Emergency Support</strong></td>
                                                <td>24/7</td>
                                                <td><span class="badge bg-danger">Phone Only</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Technical Support</strong></td>
                                                <td>Mon-Fri: 8:00 AM - 8:00 PM EST</td>
                                                <td><span class="badge bg-primary">All Channels</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Billing Support</strong></td>
                                                <td>Mon-Fri: 9:00 AM - 6:00 PM EST</td>
                                                <td><span class="badge bg-success">Email & Phone</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Weekend Support</strong></td>
                                                <td>Sat-Sun: 10:00 AM - 4:00 PM EST</td>
                                                <td><span class="badge bg-warning">Emergency Only</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
    .support-card {
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .support-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg) !important;
    }
    
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .bg-primary-gradient { 
        background: linear-gradient(135deg, var(--primary-blue), var(--accent-blue)); 
    }
    .bg-success-gradient { 
        background: linear-gradient(135deg, var(--success), #34d399); 
    }
    </style>
</body>
</html>