<?php

/** @var array $categories */
/** @var string $message */
/** @var string $error */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>
    <div class="container">
        <?php include __DIR__ . '/../../includes/page-header.php'; ?>

        <div class="content">
            <?php if ($message !== ''): ?>
                <p class="success">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="error">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <!-- Add category form -->
            <div class="card mb30">
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
            </div>

            <div class="card">
                <h2>Existing Categories</h2>
                <?php if (empty($categories)): ?>
                    <p>No categories have been added.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($category['name']) ?></td>
                                        <td><?= htmlspecialchars($category['description'] ?? '') ?></td>
                                        <td><?= $category['is_active'] ? 'Active' : 'Inactive' ?></td>
                                        <td><a href="edit-category.php?id=<?= (int) $category['id'] ?>">Edit</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>