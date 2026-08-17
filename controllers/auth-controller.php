<?php

/**
 * Handles user authentication.
 * 
 * Ref: PHP password_verify() - https://www.php.net/manual/en/function.password-verify.php
 */

class AuthController
{
    private User $userManager;

    public function __construct(User $userManager)
    {
        $this->userManager = $userManager;
    }

    public function login(): void
    {
        $error = '';

        // Process the login request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $error = 'Please enter your username and password.';
            } else {
                // Find the user and verify the password
                $user = $this->userManager->findActiveByUsername($username);

                if ($user && password_verify($password, $user['password'])) {
                    // Store user data in the session
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

        // Load the login view
        require __DIR__ . '/../views/auth/login.php';
    }
}
