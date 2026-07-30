<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <main class="container">
        <h1>Dashboard</h1>

        <p>
            Welcome,
            <?= htmlspecialchars($_SESSION['user_name']) ?>.
        </p>

        <p>
            Role:
            <?= htmlspecialchars($_SESSION['user_role']) ?>
        </p>

        <ul>
            <li><a href="categories.php">Category</a></li>
            <li><a href="products.php">Product</a></li>
            <li><a href="sales.php">Sales</a></li>
            <li><a href="pos.php">POS</a></li>
        </ul>

        <a href="logout.php">Logout</a>
    </main>

</body>

</html>