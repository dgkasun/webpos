<?php

/** @var array $category */
/** @var string $message */
/** @var string $error */

include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Edit Category</h1>
            </div>
            <p><a href="categories.php">Back to Categories</a></p>
        </div>

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

        <form method="post" action="">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" id="name" name="name" maxlength="100" value="<?= htmlspecialchars($category['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" maxlength="255" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
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


<?php include __DIR__ . '/../../includes/footer.php'; ?>