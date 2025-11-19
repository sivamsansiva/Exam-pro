<?php
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$nic = '';
$staffId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nic = trim($_POST['nic'] ?? '');
    $staffId = trim($_POST['staffId'] ?? '');

    if ($nic === '') {
        $errors[] = 'Please enter your NIC.';
    }

    if ($staffId === '') {
        $errors[] = 'Please enter your Staff ID.';
    }

    if (!$errors) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM staff WHERE NIC = ? AND S_ID = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $nic, $staffId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $_SESSION['nic'] = $nic;
                $_SESSION['staffId'] = $staffId;
                header("Location: resetPassword.php");
                exit();
            } else {
                $errors[] = "NIC or Staff ID is incorrect.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/auth.css">
</head>
<body>
<div class="auth-wrapper" role="main">
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Forgot Password</h1>
            <p class="auth-subtitle">Enter your NIC and Staff ID to reset your password.</p>
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
            <div class="form-field">
                <label for="nic">NIC</label>
                <input type="text" id="nic" name="nic" value="<?php echo htmlspecialchars($nic); ?>" required autofocus>
            </div>

            <div class="form-field">
                <label for="staffId">Staff ID</label>
                <input type="text" id="staffId" name="staffId" value="<?php echo htmlspecialchars($staffId); ?>" required>
            </div>

            <button type="submit" class="auth-button">Verify Identity</button>

            <div class="auth-links">
                <span>Remember your password? <a href="login.php">Sign in</a></span>
            </div>
        </form>
    </div>
</div>
<script src="../scripts/auth.js" defer></script>
</body>
</html>
