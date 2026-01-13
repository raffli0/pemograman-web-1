<?php
require_once __DIR__ . '/../../app/core/AuthMiddleware.php';
$user = AuthMiddleware::authenticate();
$isAdmin = ($user['role'] === 'org_admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assets - Asset Responsibility System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-2 mb-4">
            <div>
                <h2 class="h4 mb-1">Assets Inventory</h2>
                <p class="text-muted small mb-0">System Asset Responsibility</p>
            </div>
            <?php if ($isAdmin): ?>
                <div>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#assetModal"
                        onclick="clearAssetForm()">
                        + Add Item
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-simple p-0 overflow-hidden border-0">
            <table class="table table-uc mb-0 table-striped">
                <thead>
                    <tr>
                        <th style="width: 20%;">Item Name</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 15%;">Condition</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody id="assetTableBody"></tbody>
            </table>
        </div>

        <?php include 'footer.php'; ?>
    </main>

    <!-- Modals -->
    <div class="modal fade" id="assetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-1">
                <div class="modal-header bg-light py-2">
                    <h5 class="modal-title fs-6 fw-bold" id="modalTitle">Manage Asset</h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assetForm">
                        <input type="hidden" id="assetId">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Item Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Description</label>
                            <textarea class="form-control form-control-sm" id="description" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Quantity</label>
                                <input type="number" class="form-control form-control-sm" id="quantity" required
                                    min="0">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Condition</label>
                                <input type="text" class="form-control form-control-sm" id="condition" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select class="form-select form-select-sm" id="status">
                                <option value="active">Active (Available)</option>
                                <option value="maintenance">Under Maintenance</option>
                                <option value="lost">Lost / Retired</option>
                            </select>
                        </div>
                        <div class="text-end border-top pt-3 mt-2">
                            <button type="submit" class="btn btn-sm btn-primary px-3">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrow Modal (Include normally) -->
    <div class="modal fade" id="borrowModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-1">
                <div class="modal-header bg-light py-2">
                    <h5 class="modal-title fs-6 fw-bold">Borrow Item</h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="borrowForm">
                        <input type="hidden" id="borrowAssetId">
                        <div class="mb-3 p-2 bg-light border rounded text-center">
                            <span class="d-block text-muted small">Item</span>
                            <strong id="borrowAssetName"></strong>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Start Date</label>
                                <input type="date" class="form-control form-control-sm" id="startDate" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Return Date</label>
                                <input type="date" class="form-control form-control-sm" id="endDate" required>
                            </div>
                        </div>
                        <div class="text-end border-top pt-3 mt-2">
                            <button type="submit" class="btn btn-sm btn-primary px-3">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/asset.js"></script>
    <script src="../assets/js/borrow.js"></script>

    <script>
        const isAdmin = <?php echo json_encode($isAdmin); ?>;
        loadAssets(isAdmin);
    </script>
</body>

</html>