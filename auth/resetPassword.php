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
    <title>Reset Password</title>
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
