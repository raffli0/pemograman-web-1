<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
$isAdmin = ($user['role'] === 'org_admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Requests - Asset Responsibility System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="border-bottom pb-2 mb-4">
            <h2 class="h4 mb-1">Borrowing Requests</h2>
            <p class="text-muted small mb-0">Track borrowing status.</p>
        </div>

        <div class="card-simple p-0 overflow-hidden border-0">
            <table class="table table-uc mb-0">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Item</th>
                        <th>Period</th>
                        <th>Status</th>
                        <?php if ($isAdmin): ?>
                            <th class="text-end">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="borrowTableBody"></tbody>
            </table>
        </div>

        <?php include 'footer.php'; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/borrow.js"></script>
    <script>
        const isAdmin = <?php echo json_encode($isAdmin); ?>;
        loadRequests(isAdmin);
    </script>
</body>

</html>