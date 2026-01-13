<?php
// Shared sidebar logic
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar">
    <div class="sidebar-title">Asset Manager</div>

    <span class="sidebar-label">Main Menu</span>
    <a href="dashboard.php" class="sidebar-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
        Dashboard
    </a>

    <?php if ($user['role'] !== 'super_admin'): ?>
        <a href="assets.php" class="sidebar-link <?php echo $currentPage == 'assets.php' ? 'active' : ''; ?>">
            Inventory Assets
        </a>
        <a href="borrow.php" class="sidebar-link <?php echo $currentPage == 'borrow.php' ? 'active' : ''; ?>">
            Borrowing Requests
        </a>
    <?php endif; ?>

    <?php if ($user['role'] == 'org_admin'): ?>
        <span class="sidebar-label mt-3">Administration</span>
        <a href="users.php" class="sidebar-link <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
            Members & Access
        </a>
        <a href="returns.php" class="sidebar-link <?php echo $currentPage == 'returns.php' ? 'active' : ''; ?>">
            Return Processing
        </a>
    <?php endif; ?>

    <div style="margin-top: auto; padding: 2rem 1.5rem;">
        <a href="#" onclick="logout()" class="text-secondary small text-decoration-none"
            style="display: flex; align-items: center; gap: 8px;">
            <span>&larr; Sign Out</span>
        </a>
    </div>
</nav>