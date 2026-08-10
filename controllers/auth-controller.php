<?php

/**
 * Handles user authentication.
 */
class AuthController
{
    private User $userManager;

    // Receive the User model.
    public function __construct(User $userManager)
    {
        $this->userManager = $userManager;
    }

    public function login(): void
    {
        $error = '';

        // Process the login request.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $error = 'Please enter your username and password.';
            } else {
                $user = $this->userManager->findActiveByUsername($username);

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

        // Load the login view.
        require __DIR__ . '/../views/auth/login.php';
    }
}
