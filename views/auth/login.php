<?php

/** @var string $error */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">
    <div class="container">
        <h1>Login</h1>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>