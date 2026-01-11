<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UtilityPro | Login</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://img.icons8.com/color/96/000000/electricity.png">
    
    <style>
        body.login-page {
            min-height: 100vh;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .login-bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        .container-fluid {
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            margin: 20px 0;
        }
        
        @media (max-height: 700px) {
            .login-card {
                margin-top: 40px;
                margin-bottom: 40px;
            }
            
            .card-body {
                padding: 2rem !important;
            }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-bg-animation"></div>
    
    <div class="container-fluid d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-4">
                <div class="card login-card shadow-lg animate-fade-in">
                    <div class="card-header bg-primary-gradient text-center py-4">
                        <div class="logo-container mb-3">
                            <div class="logo-icon">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                            <h1 class="h3 mb-0 text-white">UtilityPro</h1>
                            <p class="text-light mb-0 opacity-75">Management System</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <form id="loginForm" class="needs-validation" novalidate>
                            <div class="form-floating mb-4">
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       placeholder="name@example.com"
                                       required>
                                <label for="username">
                                    <i class="bi bi-person me-1"></i> Email
                                </label>
                                <div class="invalid-feedback">
                                    Please enter your email.
                                </div>
                            </div>
                            
                            <div class="form-floating mb-4">
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       placeholder="Password"
                                       required>
                                <label for="password">
                                    <i class="bi bi-key me-1"></i> Password
                                </label>
                                <div class="invalid-feedback">
                                    Please enter your password.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Sign In
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                            </button>
                            
                            <div class="mt-4 p-3 bg-light rounded">
                                <p class="small mb-2 text-muted">Demo Credentials:</p>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="bg-white p-2 rounded border">
                                            <small class="text-dark fw-medium">Email:</small>
                                            <small class="text-muted d-block">admin@utilitypro.com</small>
                                            <small class="text-dark fw-medium">Password:</small>
                                            <small class="text-muted d-block">password123</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="js/auth.js"></script>

    <script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    event.preventDefault()
                    const btn = form.querySelector('button[type="submit"]')
                    const spinner = btn.querySelector('.spinner-border')
                    
                    btn.disabled = true
                    spinner.classList.remove('d-none')
                    
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const remember = false;
                    
                    const mockEvent = {
                        preventDefault: function() {},
                        target: form
                    };
                    
                    if (window.AuthManager) {
                        AuthManager.handleLogin(mockEvent);
                    } else {
                        setTimeout(() => {
                            window.location.href = 'dashboard.html'
                        }, 1500)
                    }
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
    
    document.addEventListener('DOMContentLoaded', function() {
        const bgAnimation = document.querySelector('.login-bg-animation');
        if (bgAnimation) {
            bgAnimation.style.background = 
                'radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),' +
                'radial-gradient(circle at 80% 20%, rgba(13, 110, 253, 0.15) 0%, transparent 50%)';
        }
    });
    </script>
</body>
</html>