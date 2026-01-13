<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Asset Responsibility System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h3 class="mb-2" style="color: #2c3e50;">Asset Responsibility System</h3>
                    <p class="text-muted small">Borrowing Management Platform</p>
                </div>

                <div class="card-simple">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" required placeholder="name@company.com">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required placeholder="****">
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">Sign In</button>
                        </div>
                    </form>
                    <div id="error-msg" class="text-danger small text-center"></div>
                </div>

                <div class="text-center mt-3 small">
                    <p class="text-muted">Register a new Organization? <a href="register_org.php" class="fw-bold">Click
                            Here</a></p>
                </div>

                <div class="auth-wrapper">
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Auth JS now contains the logic -->
    <script src="../assets/js/auth.js"></script>
</body>

</html>