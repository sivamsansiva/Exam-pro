<?php
// Unified Profile Page for All User Roles
include("../config/config.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userRole = $_SESSION['role'];
$userEmail = $_SESSION['email'];
$userId = $_SESSION['user_id'];

// Check if this is profile completion mode
$isProfileCompletion = isset($_GET['complete']) && $_GET['complete'] == '1';
$redirectAfterCompletion = isset($_GET['redirect']) ? $_GET['redirect'] : '../index.php';

// Initialize variables
$userData = [];
$phoneData = [];
$updateSuccess = false;

// Fetch user data using prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

// Fetch phone number
$stmt = $conn->prepare("SELECT phone FROM user_phone WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$phoneRow = $result->fetch_assoc();
$stmt->close();

$phoneNo = $phoneRow['phone'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $departmentId = mysqli_real_escape_string($conn, $_POST['department_id']);
    $nic = mysqli_real_escape_string($conn, $_POST['nic']);

    // Validate NIC (mandatory for staff)
    if ($userRole === 'staff' && empty($nic)) {
        $errorMessage = "NIC is mandatory for staff members.";
    } else {
        // Update user information including NIC
        $stmt = $conn->prepare("
            UPDATE users SET
                first_name = ?,
                last_name = ?,
                email = ?,
                dob = ?,
                gender = ?,
                department_id = ?,
                nic = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("sssssssi", $fname, $lname, $email, $dob, $gender, $departmentId, $nic, $userId);

        if ($stmt->execute()) {
            $stmt->close();

            // Update or insert phone number
            $stmt = $conn->prepare("SELECT user_id FROM user_phone WHERE user_id = ? LIMIT 1");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $phoneResult = $stmt->get_result();
            $stmt->close();

            if ($phoneResult->num_rows > 0) {
                // Update existing phone
                $stmt = $conn->prepare("UPDATE user_phone SET phone = ? WHERE user_id = ?");
                $stmt->bind_param("si", $phone, $userId);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new phone
                $stmt = $conn->prepare("INSERT INTO user_phone (user_id, phone) VALUES (?, ?)");
                $stmt->bind_param("is", $userId, $phone);
                $stmt->execute();
                $stmt->close();
            }

            $updateSuccess = true;

            // If this was profile completion, redirect to original page
            if ($isProfileCompletion && !empty($nic) && !empty($fname) && !empty($lname)) {
                header("Location: " . $redirectAfterCompletion);
                exit();
            }

            // Otherwise refresh current page
            header("Location: profile.php?success=1");
            exit();
        }
        $stmt->close();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Fetch current password hash
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($currentPassword, $user['password_hash'])) {
        $errorMessage = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New password and confirmation do not match.";
    } elseif (strlen($newPassword) < 8) {
        $errorMessage = "New password must be at least 8 characters long.";
    } else {
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $newPasswordHash, $userId);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: profile.php?password_success=1");
            exit();
        } else {
            $errorMessage = "Failed to update password. Please try again.";
        }
    }
}

// Get exam results for staff role
$examResults = [];
if ($userRole === 'staff' && isset($userId)) {
    $stmt = $conn->prepare("
        SELECT e.id, e.code, e.name, ea.attempt_no, ea.score, ea.started_at, ea.ended_at, e.max_score
        FROM exam_attempt ea
        JOIN exam e ON ea.exam_id = e.id
        WHERE ea.user_id = ? AND ea.completed = 1
        ORDER BY ea.started_at DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $examResults[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userRole); ?> Profile - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">

    <style>
        /* Profile Page Styles */
        .profile-wrapper {
            min-height: calc(100vh - 72px);
            background: linear-gradient(180deg, var(--color-secondary-50) 0%, var(--color-white) 100%);
            padding: var(--spacing-3xl) 0;
        }

        .profile-header-card {
            background: linear-gradient(135deg, var(--color-primary-600), var(--color-accent-600));
            color: var(--text-inverse);
            padding: var(--spacing-2xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            margin-bottom: var(--spacing-xl);
        }

        .profile-header-card h1 {
            font-size: var(--font-size-3xl);
            font-weight: var(--font-weight-bold);
            margin-bottom: var(--spacing-xs);
        }

        .profile-header-card p {
            font-size: var(--font-size-lg);
            opacity: 0.95;
            margin: 0;
        }

        .profile-header-card i {
            margin-right: var(--spacing-sm);
        }
    </style>
</head>

<body>
    <?php include("../includes/header.php"); ?>

    <div class="profile-wrapper">
        <div class="container">
            <!-- Profile Completion Notice -->
            <?php if ($isProfileCompletion): ?>
                <div class="alert alert-warning mb-lg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Profile Completion Required!</strong><br>
                        Please complete your profile with all mandatory fields (including NIC) before you can register or take exams.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Profile Header -->
            <div class="profile-header-card">
                <h1>Hello, <?php echo htmlspecialchars($userData['first_name'] ?? 'User'); ?> <?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>!</h1>
                <p>
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars(ucfirst($userRole)); ?>
                </p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success mb-lg">
                    <i class="fas fa-check-circle"></i> Profile updated successfully!
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['password_success'])): ?>
                <div class="alert alert-success mb-lg">
                    <i class="fas fa-check-circle"></i> Password changed successfully!
                </div>
            <?php endif; ?>

            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-error mb-lg">
                    <i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <?php if (!$isProfileCompletion): ?>
                <?php if ($userRole === 'admin'): ?>
                    <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary mb-lg">
                        <i class="fas fa-tachometer-alt"></i> Go to Admin Dashboard
                    </a>
                <?php elseif ($userRole === 'manager'): ?>
                    <a href="../dashboards/manager_dashboard.php" class="btn btn-secondary mb-lg">
                        <i class="fas fa-tachometer-alt"></i> Go to Manager Dashboard
                    </a>
                <?php elseif ($userRole === 'examiner'): ?>
                    <a href="../dashboards/examiner_dashboard.php" class="btn btn-secondary mb-lg">
                        <i class="fas fa-tachometer-alt"></i> Go to Examiner Dashboard
                    </a>
                <?php elseif ($userRole === 'staff'): ?>
                    <a href="../dashboards/user_dashboard.php" class="btn btn-secondary mb-lg">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Profile Information Card -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-edit"></i> Profile Information
                    </h2>
                    <p class="card-subtitle">Update your personal details</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="profile.php" id="profileForm">
                        <div class="grid-2">
                            <div class="form-group">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" id="fname" name="fname" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['first_name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" id="lname" name="lname" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Mobile Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}"
                                    placeholder="07XXXXXXXX"
                                    value="<?php echo htmlspecialchars($phoneNo); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" id="dob" name="dob" class="form-control"
                                    value="<?php echo htmlspecialchars($userData['dob'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <div style="display: flex; gap: var(--spacing-lg); padding-top: var(--spacing-sm);">
                                    <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                                        <input type="radio" name="gender" value="Male"
                                            <?php echo (isset($userData['gender']) && $userData['gender'] == 'Male') ? 'checked' : ''; ?> required
                                            style="width: 18px; height: 18px; cursor: pointer;">
                                        Male
                                    </label>
                                    <label style="display: flex; align-items: center; gap: var(--spacing-sm); cursor: pointer;">
                                        <input type="radio" name="gender" value="Female"
                                            <?php echo (isset($userData['gender']) && $userData['gender'] == 'Female') ? 'checked' : ''; ?> required
                                            style="width: 18px; height: 18px; cursor: pointer;">
                                        Female
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="department_id" class="form-label">Department</label>
                                <select name="department_id" id="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    $deptQuery = $conn->query("SELECT id, name FROM department ORDER BY name");
                                    while ($dept = $deptQuery->fetch_assoc()) {
                                        $selected = ($userData['department_id'] == $dept['id']) ? 'selected' : '';
                                        echo "<option value='{$dept['id']}' {$selected}>{$dept['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="nic" class="form-label">
                                    NIC (National Identity Card)
                                    <?php if ($userRole === 'staff'): ?>
                                        <span style="color: var(--color-error);">*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="nic" name="nic" class="form-control"
                                    placeholder="e.g., 199512345678 or 951234567V"
                                    value="<?php echo htmlspecialchars($userData['nic'] ?? ''); ?>"
                                    <?php echo ($userRole === 'staff') ? 'required' : ''; ?>>
                                <?php if ($userRole === 'staff'): ?>
                                    <small class="field-hint">NIC is mandatory for staff members to register or take exams</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="role_display" class="form-label">Role</label>
                                <input type="text" id="role_display" class="form-control"
                                    value="<?php echo htmlspecialchars(ucfirst($userData['role'] ?? '')); ?>" readonly>
                            </div>
                        </div>

                        <div style="margin-top: var(--spacing-xl); text-align: center;">
                            <button type="submit" name="update_profile" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card mb-xl">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-key"></i> Change Password
                    </h2>
                    <p class="card-subtitle">Update your account password</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="profile.php" id="passwordForm" style="max-width: 600px;">
                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control"
                                minlength="8" required>
                            <small class="field-hint">Password must be at least 8 characters long</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                minlength="8" required>
                        </div>

                        <div style="margin-top: var(--spacing-xl);">
                            <button type="submit" name="change_password" class="btn btn-secondary">
                                <i class="fas fa-lock"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($userRole === 'staff' && count($examResults) > 0): ?>
                <!-- Exam Results Card -->
                <div class="card mb-xl">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class="fas fa-chart-line"></i> My Exam Results
                        </h2>
                        <p class="card-subtitle">Your completed exams and performance</p>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Exam Code</th>
                                        <th>Exam Name</th>
                                        <th>Attempt</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($examResults as $exam):
                                        $percentage = ($exam['score'] / $exam['max_score']) * 100;
                                        $status = $percentage >= 60 ? 'Passed' : 'Failed';
                                        $badgeClass = $percentage >= 60 ? 'badge-success' : 'badge-error';
                                    ?>
                                        <tr>
                                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($exam['code']); ?></span></td>
                                            <td style="font-weight: var(--font-weight-semibold);"><?php echo htmlspecialchars($exam['name']); ?></td>
                                            <td>#<?php echo htmlspecialchars($exam['attempt_no']); ?></td>
                                            <td><?php echo htmlspecialchars($exam['score']); ?>/<?php echo htmlspecialchars($exam['max_score']); ?></td>
                                            <td style="font-weight: var(--font-weight-bold); color: var(--color-primary-600);"><?php echo number_format($percentage, 2); ?>%</td>
                                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($exam['started_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($userRole === 'staff'): ?>
                <div class="card mb-xl">
                    <div class="card-body" style="text-align: center; padding: var(--spacing-3xl);">
                        <i class="fas fa-chart-line" style="font-size: 3rem; color: var(--color-secondary-300); margin-bottom: var(--spacing-lg);"></i>
                        <h2 style="margin-bottom: var(--spacing-sm);">My Exam Results</h2>
                        <p style="color: var(--text-secondary);">You haven't taken any exams yet.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../includes/footer.php"); ?>

    <script>
        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const dob = document.getElementById('dob').value;
            const dobDate = new Date(dob);
            const today = new Date();

            if (dobDate > today) {
                e.preventDefault();
                alert('Date of Birth cannot be in the future.');
                return false;
            }
        });
    </script>
</body>

</html>
