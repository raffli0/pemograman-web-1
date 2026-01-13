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
    <title>Users - Asset SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-2 mb-4">
            <div>
                <h2 class="h4 mb-1">Organization Users</h2>
                <p class="text-muted small mb-0">Manage members who can access your assets.</p>
            </div>
            <div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    + Add Member
                </button>
            </div>
        </div>

        <div class="card-simple p-0 overflow-hidden border-0">
            <table class="table table-uc mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody id="userTableBody"></tbody>
            </table>
        </div>

        <?php include 'footer.php'; ?>
    </main>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content rounded-1">
                <div class="modal-header bg-light py-2">
                    <h5 class="modal-title fs-6 fw-bold">Add New Member</h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" class="form-control form-control-sm" id="userName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" class="form-control form-control-sm" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" class="form-control form-control-sm" id="userPassword" required>
                        </div>
                        <div class="text-end border-top pt-3 mt-2">
                            <button type="submit" class="btn btn-sm btn-primary px-3">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script>
        const bsUserModal = new bootstrap.Modal(document.getElementById('userModal'));

        function loadUsers() {
            fetchAPI('/auth/getUsers').then(res => {
                const tbody = document.getElementById('userTableBody');
                tbody.innerHTML = '';
                res.data.forEach(u => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${u.name}</td>
                            <td>${u.email}</td>
                            <td><span class="badge bg-light text-dark border">${u.role}</span></td>
                            <td class="small text-muted">${u.created_at}</td>
                        </tr>
                    `;
                });
            });
        }

        document.getElementById('userForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                name: document.getElementById('userName').value,
                email: document.getElementById('userEmail').value,
                password: document.getElementById('userPassword').value
            };
            try {
                await fetchAPI('/auth/createUser', 'POST', data);
                bsUserModal.hide();
                loadUsers();
                document.getElementById('userForm').reset();
            } catch (err) { alert(err.message); }
        });

        loadUsers();
    </script>
</body>

</html>