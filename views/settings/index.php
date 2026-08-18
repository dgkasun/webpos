<?php

/** @var array $settings */
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

            <div class="card">
                <form method="post">

                    <div class="form-group">
                        <label for="shop_name">Shop Name</label>
                        <input type="text" id="shop_name" name="shop_name" maxlength="150" value="<?= htmlspecialchars($settings['shop_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" maxlength="255" rows="3"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" maxlength="30" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="print_footer">Footer</label>
                        <textarea id="print_footer" name="print_footer" maxlength="255" rows="3"><?= htmlspecialchars($settings['print_footer'] ?? '') ?></textarea>
                    </div>

                    <button type="submit">Save Settings</button>

                </form>
            </div>
        </div>
    </div>

</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>