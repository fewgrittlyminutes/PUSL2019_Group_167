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
    <title>Privacy Policy | UtilityPro</title>
    
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
                            <i class="bi bi-shield-lock text-primary me-2"></i>
                            Privacy Policy
                        </h1>
                        <p class="text-muted mb-0">How we protect and handle your information</p>
                    </div>
                </div>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            UtilityPro Privacy Policy
                        </h5>
                    </div>
                    <div class="card-body">
                        <section class="mb-4">
                            <h4 class="mb-3">1. Introduction</h4>
                            <p>Welcome to UtilityPro Management System ("UtilityPro," "we," "our," or "us"). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our utility management software and services.</p>
                            <p>We are committed to protecting your personal information and your right to privacy. If you have any questions or concerns about this privacy policy, or our practices with regards to your personal information, please contact us at <a href="mailto:privacy@utilitypro.com">privacy@utilitypro.com</a>.</p>
                        </section>
                        
                        <section class="mb-4">
                            <h4 class="mb-3">2. Information We Collect</h4>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Name and contact details</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Email address</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Phone number</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Address and location</li>
                                                <li><i class="bi bi-check-circle text-success me-2"></i>Account credentials</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="bi bi-speedometer me-2"></i>Utility Data</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Meter readings</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Consumption patterns</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Billing information</li>
                                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Payment history</li>
                                                <li><i class="bi bi-check-circle text-success me-2"></i>Usage statistics</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                        <section class="mb-4">
                            <h4 class="mb-3">3. How We Use Your Information</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Purpose</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Billing & Payments</strong></td>
                                            <td>To generate accurate utility bills and process payments</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Service Delivery</strong></td>
                                            <td>To provide utility management services and customer support</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Analytics & Improvements</strong></td>
                                            <td>To analyze usage patterns and improve our services</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Legal Compliance</strong></td>
                                            <td>To comply with legal obligations and regulations</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Communications</strong></td>
                                            <td>To send important updates and service notifications</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        
                        <section class="mb-4">
                            <h4 class="mb-3">4. Data Security</h4>
                            <div class="alert alert-info">
                                <i class="bi bi-shield-check me-2"></i>
                                We implement appropriate technical and organizational security measures designed to protect the security of any personal information we process.
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded h-100">
                                        <i class="bi bi-lock text-primary display-6 mb-3"></i>
                                        <h6>Encryption</h6>
                                        <small class="text-muted">Data encrypted both in transit and at rest</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded h-100">
                                        <i class="bi bi-person-check text-primary display-6 mb-3"></i>
                                        <h6>Access Control</h6>
                                        <small class="text-muted">Role-based access permissions</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="text-center p-3 border rounded h-100">
                                        <i class="bi bi-clock-history text-primary display-6 mb-3"></i>
                                        <h6>Regular Audits</h6>
                                        <small class="text-muted">Security assessments and monitoring</small>
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                        <section class="mb-4">
                            <h4 class="mb-3">5. Your Privacy Rights</h4>
                            <p>Depending on your location, you may have the following rights regarding your personal information:</p>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-eye text-success me-3 mt-1"></i>
                                        <div>
                                            <h6>Right to Access</h6>
                                            <small class="text-muted">Request access to your personal data</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-pencil text-primary me-3 mt-1"></i>
                                        <div>
                                            <h6>Right to Rectification</h6>
                                            <small class="text-muted">Request correction of inaccurate data</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-trash text-danger me-3 mt-1"></i>
                                        <div>
                                            <h6>Right to Deletion</h6>
                                            <small class="text-muted">Request deletion of your personal data</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-download text-warning me-3 mt-1"></i>
                                        <div>
                                            <h6>Right to Portability</h6>
                                            <small class="text-muted">Request transfer of your data</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        
                        <div class="contact-info-section p-4 bg-light rounded mb-4">
                            <h4 class="mb-3"><i class="bi bi-envelope me-2"></i>Contact Us</h4>
                            <p>If you have questions or comments about this policy, you may contact our Data Protection Officer at:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Email:</strong> <a href="mailto:privacy@utilitypro.com">privacy@utilitypro.com</a></li>
                                        <li class="mb-2"><strong>Phone:</strong> +1 (800) 555-UTILITY</li>
                                        <li><strong>Hours:</strong> Mon-Fri, 9:00 AM - 5:00 PM EST</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Address:</strong></li>
                                        <li class="mb-1">UtilityPro Data Protection Office</li>
                                        <li class="mb-1">123 Security Lane</li>
                                        <li>Privacy City, PC 12345</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <footer class="pt-4 mt-4 border-top">
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
    .contact-info-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .section-card {
        transition: all 0.3s ease;
        height: 100%;
        margin-bottom: 20px;
    }
    
    .section-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg) !important;
    }
    </style>
</body>
</html>