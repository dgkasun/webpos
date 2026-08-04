<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/user.php';

$userManager = new User($conn);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $user = $userManager->findActiveByUsername($username);
        /*$userQuery = $conn->prepare(
            'SELECT id, name, username, password, role
            FROM users
            WHERE username = :username
            AND is_active = 1
            LIMIT 1'
        );

        $userQuery->execute([
            'username' => $username,
        ]);

        $user = $userQuery->fetch(PDO::FETCH_ASSOC);*/

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header('Location: dashboard.php');
            exit;
        }

        $error = 'Invalid username or password.';
    }
}

$pageTitle = 'Login';
?>

<?php include 'includes/header.php'; ?>

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

<?php include 'includes/footer.php'; ?>