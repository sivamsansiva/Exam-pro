<?php
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['nic']) || !isset($_SESSION['staffId'])) {
    header("Location: forgetPassword.php");
    exit();
}

$errors = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if (strlen($newPassword) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        $nic = $_SESSION['nic'];
        $staffId = $_SESSION['staffId'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Use prepared statement
        $stmt = $conn->prepare("UPDATE staff SET password = ? WHERE NIC = ? AND S_ID = ?");
        if ($stmt) {
            $stmt->bind_param("sss", $hashedPassword, $nic, $staffId);
            if ($stmt->execute()) {
                session_destroy();
                header("Location: login.php?reset=success");
                exit();
            } else {
                $errors[] = "Error updating password: " . $conn->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Database error.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--color-accent-600) 0%, var(--color-primary-600) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
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
            background-image: radial-gradient(circle at 20px 20px, rgba(255, 255, 255, 0.1) 2px, transparent 0);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: white;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-2xl);
            padding: var(--spacing-3xl);
            animation: slideUp 0.4s ease-out;
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
            margin-bottom: var(--spacing-xl);
        }

        .auth-title {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
            color: var(--color-secondary-900);
            margin: 0 0 var(--spacing-sm) 0;
        }

        .auth-subtitle {
            font-size: var(--font-size-base);
            color: var(--color-secondary-600);
            margin: 0;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xs);
        }

        .form-field label {
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-semibold);
            color: var(--color-secondary-700);
        }

        .password-field .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-field input {
            width: 100%;
            padding: var(--spacing-sm) var(--spacing-md);
            padding-right: 80px;
            border: 2px solid var(--color-secondary-200);
            border-radius: var(--radius-lg);
            font-size: var(--font-size-base);
            transition: all var(--transition-base);
            outline: none;
        }

        .password-field input:focus {
            border-color: var(--color-primary-500);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: var(--spacing-sm);
            background: transparent;
            border: none;
            color: var(--color-primary-600);
            cursor: pointer;
            padding: var(--spacing-xs) var(--spacing-sm);
            font-size: var(--font-size-sm);
            font-weight: var(--font-weight-medium);
            transition: color var(--transition-fast);
        }

        .password-toggle:hover {
            color: var(--color-primary-700);
        }

        .auth-button {
            width: 100%;
            padding: var(--spacing-sm) var(--spacing-xl);
            background: linear-gradient(135deg, var(--color-accent-600) 0%, var(--color-primary-600) 100%);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            font-size: var(--font-size-base);
            font-weight: var(--font-weight-semibold);
            cursor: pointer;
            transition: all var(--transition-base);
            margin-top: var(--spacing-md);
        }

        .auth-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .auth-button:active {
            transform: translateY(0);
        }

        .alert {
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
        }

        .alert-error {
            background-color: var(--color-error-50);
            color: var(--color-error-700);
            border-left: 4px solid var(--color-error-500);
        }

        .alert-list {
            margin: 0;
            padding-left: var(--spacing-lg);
        }

        .alert-list li {
            margin: var(--spacing-xs) 0;
        }

        @media (max-width: 640px) {
            .auth-card {
                padding: var(--spacing-xl);
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
                <h1 class="auth-title">Reset Password</h1>
                <p class="auth-subtitle">Create a new password for your account.</p>
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

            <form method="post" class="auth-form">
                <div class="form-field password-field">
                    <label for="newPassword">New Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="newPassword" name="newPassword" required minlength="8" autocomplete="new-password" data-password-field>
                        <button type="button" class="password-toggle" data-toggle-password aria-label="Toggle password visibility">
                            <span class="toggle-text">Show</span>
                        </button>
                    </div>
                </div>

                <div class="form-field password-field">
                    <label for="confirmPassword">Confirm Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" required minlength="8" autocomplete="new-password" data-password-field>
                        <button type="button" class="password-toggle" data-toggle-password aria-label="Toggle password visibility">
                            <span class="toggle-text">Show</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-button">Update Password</button>
            </form>
        </div>
    </div>
    <script src="../scripts/auth.js" defer></script>
</body>

</html>
