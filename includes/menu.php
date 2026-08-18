<?php

/**
 * Displays the shared admin navigation menu.
 */

/** @var string $currentPage **/

// Set an empty value if the current page is not defined.
$currentPage = $currentPage ?? '';
?>

<div class="dashboard-links">
    <div class="container">
        <h2><img src="assets/images/cart-shopping-solid-full.svg" /> WebPOS</h2>
        <ul>
            <li class="<?= $currentPage === 'Dashboard' ? 'active' : '' ?>"><a href="dashboard.php">Dashboard</a></li>
            <li class="<?= $currentPage === 'Categories' ? 'active' : '' ?>"><a href="categories.php">Categories</a></li>
            <li class="<?= $currentPage === 'Products' ? 'active' : '' ?>"><a href="products.php">Products</a></li>
            <li class="<?= $currentPage === 'Sales History' ? 'active' : '' ?>"><a href="sales-history.php">Sales History</a></li>
            <li class="<?= $currentPage === 'Reports' ? 'active' : '' ?>"><a href="sales-report.php">Reports</a></li>
            <li><a href="pos.php">POS</a></li>
            <li class="<?= $currentPage === 'Settings' ? 'active' : '' ?>"><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</div>