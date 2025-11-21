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
            $_SESSION['role'] = $role; // Store lowercase role consistently
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
    <title>Sign In - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">

    <style>
        /* ============================================
       AUTH PAGE STYLES
       ============================================ */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-accent-600) 100%);
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.5;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: var(--spacing-lg);
        }

        .auth-card {
            background: var(--color-white);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-2xl);
            padding: var(--spacing-3xl);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }

        .auth-title {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
        }

        .auth-subtitle {
            font-size: var(--font-size-base);
            color: var(--text-secondary);
            margin: 0;
            line-height: var(--line-height-relaxed);
        }

        /* Alert Styles */
        .alert {
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid;
        }

        .alert-error {
            background: var(--color-error-light);
            border-color: var(--color-error);
            color: #991B1B;
        }

        .alert-list {
            margin: 0;
            padding-left: var(--spacing-lg);
        }

        .alert-list li {
            font-size: var(--font-size-sm);
            line-height: var(--line-height-relaxed);
        }

        /* Form Styles */
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .form-field label {
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-semibold);
            color: var(--text-primary);
        }

        .form-field input[type="email"],
        .form-field input[type="password"],
        .form-field input[type="text"] {
            width: 100%;
            padding: var(--spacing-md) var(--spacing-lg);
            font-size: var(--font-size-base);
            color: var(--text-primary);
            background: var(--color-white);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-lg);
            transition: all var(--transition-base);
            font-family: var(--font-family-base);
        }

        .form-field input:focus {
            outline: none;
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-field input::placeholder {
            color: var(--text-tertiary);
        }

        /* Password Field */
        .password-field .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-field input {
            padding-right: 4rem;
        }

        .password-toggle {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            padding: 0 var(--spacing-lg);
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-semibold);
            cursor: pointer;
            transition: color var(--transition-fast);
        }

        .password-toggle:hover {
            color: var(--color-primary-600);
        }

        /* Auth Button */
        .auth-button {
            width: 100%;
            padding: var(--spacing-md) var(--spacing-xl);
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-semibold);
            color: var(--text-inverse);
            background: linear-gradient(135deg, var(--color-primary-600), var(--color-primary-700));
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-base);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .auth-button:hover {
            background: linear-gradient(135deg, var(--color-primary-700), var(--color-primary-800));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .auth-button:active {
            transform: translateY(0);
        }

        /* Auth Links */
        .auth-links {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
            align-items: center;
            margin-top: var(--spacing-md);
            font-size: var(--font-size-sm);
            color: var(--text-secondary);
        }

        .auth-links a {
            color: var(--color-primary-600);
            text-decoration: none;
            font-weight: var(--font-weight-semibold);
            transition: color var(--transition-fast);
        }

        .auth-links a:hover {
            color: var(--color-primary-700);
            text-decoration: underline;
        }

        /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
        @media (max-width: 640px) {
            .auth-wrapper {
                padding: var(--spacing-md);
            }

            .auth-card {
                padding: var(--spacing-2xl) var(--spacing-lg);
            }

            .auth-title {
                font-size: var(--font-size-2xl);
            }
        }
    </style>
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
