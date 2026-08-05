<div class="dashboard-links">
    <div class="container">
        <ul>
            <li class="<?= $currentPage === 'Dashboard' ? 'active' : '' ?>"><a href="dashboard.php">Dashboard</a></li>
            <li class="<?= $currentPage === 'Categories' ? 'active' : '' ?>"><a href="categories.php">Categories</a></li>
            <li class="<?= $currentPage === 'Products' ? 'active' : '' ?>"><a href="products.php">Products</a></li>
            <li class="<?= $currentPage === 'Sales History' ? 'active' : '' ?>"><a href="sales-history.php">Sales History</a></li>
            <li class="<?= $currentPage === 'Reports' ? 'active' : '' ?>"><a href="sales-report.php"> Reports</a></li>
            <li><a href="pos.php">POS</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>