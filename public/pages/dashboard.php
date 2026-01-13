<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Asset Responsibility System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <header class="mb-5">
            <h2 class="h3 mb-2">Overview</h2>
            <p class="text-muted">
                <?php echo $user['role'] == 'super_admin' ? 'Platform Governance & Statistics' : 'Organization Assets & Activity'; ?>
            </p>
        </header>

        <!-- Welcome Context -->
        <div class="card-simple mb-5 bg-white border-0 shadow-sm"
            style="background: linear-gradient(to right, #ffffff, #f8fafc);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 fw-bold text-dark">Welcome back, <?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="mb-0 text-secondary small">
                        Logged in as <span
                            class="fw-semibold text-primary"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span>
                    </p>
                </div>
                <div class="text-end text-muted small">
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>
        </div>

        <?php if ($user['role'] === 'super_admin'): ?>
            <!-- ... Super Admin View (Unchanged Logic, Updated Style) ... -->
            <div class="card-simple">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h5 class="h6 mb-0 text-uppercase fw-bold text-secondary tracking-wide">Registered Organizations</h5>
                </div>
                <table class="table table-uc">
                    <thead>
                        <tr>
                            <th>Organization Name</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="orgTableBody"></tbody>
                </table>
            </div>
            <script src="../assets/js/auth.js"></script>
            <script src="../assets/js/organization.js"></script>
            <script>loadOrganizations();</script>

        <?php else: ?>
            <!-- Org Admin / Member View -->
            <div class="row g-4 mb-5">
                <!-- Stats Cards -->
                <div class="col-md-4">
                    <div class="card-simple h-100 border-bottom border-primary border-1">
                        <span class="text-uppercase text-xs fw-bold text-secondary mb-2 d-block">Total Inventory</span>
                        <h2 class="display-6 fw-bold mb-1 text-dark" id="statAssets">-</h2>
                        <span class="small text-muted">Registered assets</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-simple h-100 border-bottom border-warning border-1">
                        <span class="text-uppercase text-xs fw-bold text-secondary mb-2 d-block">Active Loans</span>
                        <h2 class="display-6 fw-bold mb-1 text-dark" id="statBorrows">-</h2>
                        <a href="borrow.php" class="small text-decoration-none fw-medium">View active loans &rarr;</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-simple h-100 border-bottom border-danger border-1">
                        <span class="text-uppercase text-xs fw-bold text-secondary mb-2 d-block">Pending Review</span>
                        <h2 class="display-6 fw-bold mb-1 text-dark" id="statPending">-</h2>
                        <?php if ($user['role'] == 'org_admin'): ?>
                            <a href="borrow.php" class="small text-decoration-none fw-medium text-danger">Action required
                                &rarr;</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <?php if ($user['role'] === 'org_admin'): ?>
                        <div class="card-simple">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="h6 mb-0 fw-bold text-dark">Recent Activity Feed</h5>
                                <span class="badge bg-secondary text-dark bg-opacity-10">Last 5 Actions</span>
                            </div>

                            <div id="activityTimeline" class="ps-2">
                                <div class="text-muted small py-3">Loading feed...</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card-simple bg-light border-0">
                        <h6 class="fw-bold mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="assets.php"
                                class="btn btn-white border shadow-sm text-start py-2 px-3 fw-medium text-dark small">
                                Browse Inventory
                            </a>
                            <a href="borrow.php"
                                class="btn btn-white border shadow-sm text-start py-2 px-3 fw-medium text-dark small">
                                Check My Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <script src="../assets/js/auth.js"></script>
            <script>
                async function loadStats() {
                    try {
                        const res = await fetchAPI('/dashboard/getStats');
                        if (res.status === 'success') {
                            document.getElementById('statAssets').textContent = res.data.total_assets;
                            document.getElementById('statBorrows').textContent = res.data.active_borrows;
                            document.getElementById('statPending').textContent = res.data.pending_requests;

                            // Render Timeline
                            const timelineContainer = document.getElementById('activityTimeline');
                            if (timelineContainer && res.data.logs) {
                                timelineContainer.innerHTML = '';
                                if (res.data.logs.length === 0) {
                                    timelineContainer.innerHTML = '<p class="text-muted small">No recent activity logged.</p>';
                                } else {
                                    res.data.logs.forEach(log => {
                                        // Auto-color dots based on action
                                        let activeClass = '';
                                        if (log.action.includes('APPROVE') || log.action.includes('CREATE')) activeClass = 'active';

                                        timelineContainer.innerHTML += `
                                            <div class="timeline-item ${activeClass}">
                                                <div class="timeline-dot"></div>
                                                <div class="mb-1">
                                                    <span class="fw-bold text-dark small">${log.action.replace('_', ' ')}</span>
                                                    <span class="text-muted ms-2" style="font-size: 0.75rem;">${log.created_at}</span>
                                                </div>
                                                <p class="mb-0 text-muted small">${log.details}</p>
                                            </div>
                                        `;
                                    });
                                }
                            }
                        }
                    } catch (e) { console.error(e); }
                }
                loadStats();
            </script>
        <?php endif; ?>

        <?php include 'footer.php'; ?>
    </main>
</body>

</html>