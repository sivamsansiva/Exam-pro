<?php
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$email = '';
$department = '';

$departmentOptions = [];
$departmentQuery = $conn->query('SELECT id, name FROM department ORDER BY name');

if ($departmentQuery) {
    while ($row = $departmentQuery->fetch_assoc()) {
        $departmentOptions[] = $row;
    }
}

$validDepartments = array_column($departmentOptions, 'id');

if ($departmentQuery instanceof mysqli_result) {
    $departmentQuery->free();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid work email address.';
    }

    if ($department === '' || !in_array($department, $validDepartments, true)) {
        $errors[] = 'Please choose a valid department.';
    }

    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    if ($password === '' || !preg_match($passwordPattern, $password)) {
        $errors[] = 'Password must be at least 8 characters and include upper, lower, and numeric characters.';
    }

    if ($confirmPassword === '' || $password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');

        if ($checkStmt) {
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $errors[] = 'An account already exists with that email address.';
            } else {
                $role = 'staff';
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $conn->prepare('INSERT INTO users (email, password_hash, role, department_id, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');

                if ($insertStmt) {
                    $insertStmt->bind_param('sssi', $email, $passwordHash, $role, $department);

                    if ($insertStmt->execute()) {
                        $newUserId = $insertStmt->insert_id ?: $conn->insert_id;
                        $insertStmt->close();
                        $checkStmt->close();

                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $role; // Store lowercase role consistently
                        $_SESSION['role_key'] = $role;
                        $_SESSION['department'] = $department;

                        // Redirect to profile completion
                        header('Location: ../users/profile.php?complete=1');
                        exit;
                    }

                    $errors[] = 'We could not complete your registration. Please try again.';
                    $insertStmt->close();
                } else {
                    $errors[] = 'Unable to process your registration right now. Please try again later.';
                }
            }

            $checkStmt->close();
        } else {
            $errors[] = 'Unable to process your registration right now. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">

    <style>
        /* Using shared auth styles from login.php */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--color-accent-600) 0%, var(--color-primary-600) 100%);
            position: relative;
            overflow: hidden;
            padding: var(--spacing-xl) 0;
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
            max-width: 500px;
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
        .form-field select {
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

        .form-field select {
            cursor: pointer;
            appearance: none;
            background-image: url('data:image/svg+xml,<svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L11 1" stroke="%2364748B" stroke-width="2" stroke-linecap="round"/></svg>');
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 3rem;
        }

        .form-field input:focus,
        .form-field select:focus {
            outline: none;
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

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

        .field-hint {
            font-size: var(--font-size-xs);
            color: var(--text-tertiary);
            margin-top: calc(var(--spacing-xs) * -1);
        }

        .auth-button {
            width: 100%;
            padding: var(--spacing-md) var(--spacing-xl);
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-semibold);
            color: var(--text-inverse);
            background: linear-gradient(135deg, var(--color-accent-600), var(--color-accent-700));
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all var(--transition-base);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.3);
        }

        .auth-button:hover {
            background: linear-gradient(135deg, var(--color-accent-700), var(--color-accent-800));
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(168, 85, 247, 0.4);
        }

        .auth-button:active {
            transform: translateY(0);
        }

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
            color: var(--color-accent-600);
            text-decoration: none;
            font-weight: var(--font-weight-semibold);
            transition: color var(--transition-fast);
        }

        .auth-links a:hover {
            color: var(--color-accent-700);
            text-decoration: underline;
        }

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
                <h1 class="auth-title">Create Your Account</h1>
                <p class="auth-subtitle">Register as an employee to access the examination portal.</p>
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

            <form method="post" class="auth-form" data-form-type="register">
                <div class="form-field">
                    <label for="email">Work Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required autocomplete="email" autofocus>
                </div>

                <div class="form-field">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="" disabled <?php echo $department === '' ? 'selected' : ''; ?>>Select your department</option>
                        <?php foreach ($departmentOptions as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept['id']); ?>" <?php echo $department == $dept['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field password-field">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required minlength="8" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$" title="Use at least 8 characters with uppercase, lowercase, and a number." autocomplete="new-password" data-password-field>
                        <button type="button" class="password-toggle" data-toggle-password aria-label="Toggle password visibility">
                            <span class="toggle-text">Show</span>
                        </button>
                    </div>
                    <small class="field-hint">Use 8+ characters with a mix of uppercase, lowercase, and numbers.</small>
                </div>

                <div class="form-field password-field">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" required minlength="8" autocomplete="new-password" data-password-field data-match-target="#password">
                        <button type="button" class="password-toggle" data-toggle-password aria-label="Toggle password visibility">
                            <span class="toggle-text">Show</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-button">Register</button>

                <div class="auth-links">
                    <span>Already have an account? <a href="login.php">Sign in</a></span>
                </div>
            </form>
        </div>
    </div>

    <script src="../scripts/auth.js" defer></script>
</body>

</html>
