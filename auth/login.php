<?php
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$redirectRoutes = [
    'admin' => '../index.php',
    'examiner' => '../index.php',
    'manager' => '../index.php',
    'staff' => '../index.php',
];

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Please enter your password.';
    }

    if (!$errors) {
        $authenticated = false;
        $role = 'employee';
        $department = null;
        $userId = null;
        $normalizedEmail = $email;

        $stmt = $conn->prepare('SELECT id, email, password_hash, role, department_id, first_name, last_name FROM users WHERE email = ? LIMIT 1');

        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password_hash'])) {
                $authenticated = true;
                $role = strtolower($user['role']);
                $department = $user['department_id'] ?? null;
                $userId = (int) $user['id'];
                $normalizedEmail = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
            } else {
                // Legacy authentication removed - all users must be in users table
                $authenticated = false;
            }
        } else {
            $errors[] = 'Unable to process your request right now. Please try again later.';
        }

        if ($authenticated) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $normalizedEmail;
            $_SESSION['role'] = ucfirst($role);
            $_SESSION['role_key'] = $role;

            if ($department !== null) {
                $_SESSION['department'] = $department;
            }

            $target = $redirectRoutes[$role] ?? $redirectRoutes['staff'];
            header('Location: ' . $target);

            exit;
        }

        if (!$authenticated && empty($errors)) {
            $errors[] = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/auth.css">
</head>
<body>
<div class="auth-wrapper" role="main">
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to manage examinations, evaluations, and employee progress.</p>
        </header>

        <?php if ($errors): ?>
            <div class="alert alert-error" role="alert">
                <ul class="alert-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="auth-form" data-form-type="login">
            <div class="form-field">
                <label for="email">Work Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required autocomplete="email" autofocus>
            </div>

            <div class="form-field password-field">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required minlength="8" autocomplete="current-password" data-password-field>
                    <button type="button" class="password-toggle" data-toggle-password aria-label="Toggle password visibility">
                        <span class="toggle-text">Show</span>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-button">Sign In</button>

            <div class="auth-links">
                <a href="forgetPassword.php">Forgot password?</a>
                <span>Need an account? <a href="register.php">Register</a></span>
            </div>
        </form>
    </div>
</div>

<script src="../scripts/auth.js" defer></script>
</body>
</html>

