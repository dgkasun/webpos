<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/category.php';

$categoryManager = new Category($conn);

$categoryId = $_GET['id'] ?? 0;

if ($categoryId <= 0) {
    header('Location: categories.php');
    exit;
}

$message = '';
$error = '';

$category = $categoryManager->find($categoryId);

/*$categoryQuery = $conn->prepare(
    'SELECT id, name, description, is_active
     FROM categories
     WHERE id = :id'
);

$categoryQuery->execute([
    'id' => $categoryId,
]);

$category = $categoryQuery->fetch(PDO::FETCH_ASSOC);*/

if (!$category) {
    exit('Category not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        try {
            /*$updateCategoryQuery = $conn->prepare(
                'UPDATE categories
                 SET
                    name = :name,
                    description = :description,
                    is_active = :is_active
                 WHERE id = :id'
            );

            $updateCategoryQuery->execute([
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'is_active' => $isActive,
                'id' => $categoryId,
            ]);*/

            $categoryManager->update(
                $categoryId,
                $name,
                $description,
                $isActive
            );

            $message = 'Category updated successfully.';

            /*$categoryQuery->execute([
                'id' => $categoryId,
            ]);

            $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);*/
            $category = $categoryManager->find($categoryId);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'This category already exists.';
            } else {
                $error = 'Unable to update the category.';
            }
        }
    }
}

$pageTitle = 'Edit Category';
$currentPage = 'Categories';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Edit Category</h1>
            </div>
            <p><a href="categories.php">Back to Categories</a></p>
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
                <input type="text" id="name" name="name" maxlength="100" value="<?= $category['name'] ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" maxlength="255" rows="3"><?= $category['description'] ?? '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="is_active">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?= $category['is_active'] == 1 ? 'checked' : '' ?>> Active
                </label>
            </div>

            <button type="submit">Update Category</button>
        </form>
    </div>
</main>


<?php include 'includes/footer.php'; ?>