<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
require_once __DIR__ . '/../../app/core/RoleMiddleware.php';
$user = AuthMiddleware::authenticate();
RoleMiddleware::authorize(['org_admin']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Returns - Asset Responsibility System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="border-bottom pb-2 mb-4">
            <h2 class="h4 mb-1">Returns Processing</h2>
            <p class="text-muted small mb-0">Recover distributed items.</p>
        </div>

        <div class="card-simple p-0 overflow-hidden border-0">
            <table class="table table-uc mb-0">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Item Details</th>
                        <th>Return Due</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="returnsTableBody"></tbody>
            </table>
        </div>

        <?php include 'footer.php'; ?>
    </main>

    <!-- Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-1">
                <div class="modal-header bg-light py-2">
                    <h5 class="modal-title fs-6 fw-bold">Process Item Return</h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="returnForm">
                        <input type="hidden" id="returnRequestId">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Condition Check</label>
                            <textarea class="form-control form-control-sm" id="conditionNote" required
                                placeholder="Note any damages or 'OK'"></textarea>
                        </div>
                        <div class="text-end border-top pt-3 mt-2">
                            <button type="submit" class="btn btn-sm btn-primary px-3">Return</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/return.js"></script>
    <script>
        loadReturns();
    </script>
</body>

</html>