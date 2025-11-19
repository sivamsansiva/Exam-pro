<?php
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$email = '';
$department = '';

$departmentOptions = [];
$departmentQuery = $conn->query('SELECT D_ID, D_Name FROM department ORDER BY D_Name');

if ($departmentQuery) {
    while ($row = $departmentQuery->fetch_assoc()) {
        $departmentOptions[] = $row;
    }
}

$validDepartments = array_column($departmentOptions, 'D_ID');

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
                $role = 'employee';
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $conn->prepare('INSERT INTO users (email, password_hash, role, department, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');

                if ($insertStmt) {
                    $insertStmt->bind_param('ssss', $email, $passwordHash, $role, $department);

                    if ($insertStmt->execute()) {
                        $newUserId = $insertStmt->insert_id ?: $conn->insert_id;
                        $insertStmt->close();
                        $checkStmt->close();

                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = ucfirst($role);
                        $_SESSION['role_key'] = $role;
                        $_SESSION['department'] = $department;

                        header('Location: ../index.php');
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
    <title>Create Account</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/auth.css">
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
                        <option value="<?php echo htmlspecialchars($dept['D_ID']); ?>" <?php echo $department === $dept['D_ID'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept['D_Name']); ?></option>
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
