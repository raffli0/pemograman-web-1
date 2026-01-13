<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Organization - Asset Responsibility System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-simple p-4 bg-white shadow-sm">
                    <div class="text-center mb-4">
                        <h4 class="mb-1 text-primary">Asset Responsibility System</h4>
                        <p class="text-muted small">Multi-Organization Platform.</p>
                    </div>

                    <form id="registerOrgForm">
                        <h6 class="text-uppercase text-muted small fw-bold mb-3">Organization Details</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Organization Name</label>
                            <input type="text" class="form-control" id="orgName" required placeholder="e.g. Robot Club">
                        </div>

                        <h6 class="text-uppercase text-muted small fw-bold mb-3 mt-4">Administrator Account</h6>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" class="form-control" id="adminName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Create Organization</button>
                        </div>
                    </form>
                    <div id="error-msg" class="text-danger mt-3 text-center small"></div>

                    <div class="text-center mt-3 small">
                        <a href="login.php" class="text-decoration-none">Already have an account? Login</a>
                    </div>
                </div>

                <div class="auth-wrapper">
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/organization.js"></script>
</body>

</html>