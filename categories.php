<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        try {
            $catQuery = $conn->prepare(
                'INSERT INTO categories (name, description)
                 VALUES (:name, :description)'
            );

            $catQuery->execute([
                'name' => $name,
                'description' => $description !== '' ? $description : null,
            ]);

            $message = 'Category added successfully.';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'This category already exists.';
            } else {
                $error = 'Unable to add the category.';
            }
        }
    }
}

$catQuery = $conn->query(
    'SELECT id, name, description, is_active
     FROM categories
     ORDER BY name ASC'
);

$categories = $catQuery->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Categories';
$currentPage = 'Categories';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Categories</h1>
            </div>
        </div>

        <?php if ($message !== ''): ?>
            <p class="success">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" maxlength="255" rows="3"></textarea>
            </div>
            <button type="submit">Add Category</button>
        </form>

        <hr>

        <h2>Existing Categories</h2>

        <?php if (empty($categories)): ?>
            <p>No categories have been added.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Category</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <!--<td><?= $category['id'] ?></td>-->
                                <td><?= $category['name'] ?></td>
                                <td><?= $category['description'] ?? '' ?></td>
                                <td><?= $category['is_active'] ? 'Active' : 'Inactive' ?></td>
                                <td><a href="edit-category.php?id=<?= (int) $category['id'] ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>