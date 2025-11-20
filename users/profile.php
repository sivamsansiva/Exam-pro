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
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $departmentId = $_POST['department_id'];

    // Update user information
    $stmt = $conn->prepare("
        UPDATE users SET
            first_name = ?,
            last_name = ?,
            email = ?,
            dob = ?,
            gender = ?,
            department_id = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssssi", $fname, $lname, $email, $dob, $gender, $departmentId, $userId);

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

        // Refresh data
        header("Location: profile.php?success=1");
        exit();
    }
    $stmt->close();
}

// Get exam results for staff role
$examResults = [];
if ($userRole === 'Staff' && isset($userId)) {
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
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <div class="container mt-xl">
        <!-- Profile Header -->
        <div class="card mb-lg" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white;">
            <div style="padding: 1rem;">
                <h1 style="margin: 0 0 0.5rem 0; font-size: 2rem;">Hello, <?php echo htmlspecialchars($userData['first_name'] ?? 'User'); ?> <?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>!</h1>
                <p style="opacity: 0.9; font-size: 1.1rem; margin: 0;">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userRole); ?>
                </p>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success mb-lg">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if ($userRole === 'Admin'): ?>
            <a href="../dashboards/admin_dashboard.php" class="btn btn-secondary mb-lg" style="display: inline-block;">
                <i class="fas fa-tachometer-alt"></i> Go to Admin Dashboard
            </a>
        <?php elseif ($userRole === 'Manager'): ?>
            <a href="../dashboards/manager_dashboard.php" class="btn btn-secondary mb-lg" style="display: inline-block;">
                <i class="fas fa-tachometer-alt"></i> Go to Manager Dashboard
            </a>
        <?php elseif ($userRole === 'Examiner'): ?>
            <a href="../dashboards/examiner_dashboard.php" class="btn btn-secondary mb-lg" style="display: inline-block;">
                <i class="fas fa-tachometer-alt"></i> Go to Examiner Dashboard
            </a>
        <?php elseif ($userRole === 'Staff'): ?>
            <a href="../dashboards/user_dashboard.php" class="btn btn-secondary mb-lg" style="display: inline-block;">
                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
            </a>
        <?php endif; ?>

        <!-- Profile Information Card -->
        <div class="card mb-xl">
            <h2 class="text-primary mb-lg" style="border-bottom: 2px solid var(--secondary-color); padding-bottom: 0.5rem;">
                <i class="fas fa-user-edit"></i> Profile Information
            </h2>

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
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Male"
                                       <?php echo (isset($userData['gender']) && $userData['gender'] == 'Male') ? 'checked' : ''; ?> required>
                                Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Female"
                                       <?php echo (isset($userData['gender']) && $userData['gender'] == 'Female') ? 'checked' : ''; ?> required>
                                Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
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
                        <label for="role_display" class="form-label">Role</label>
                        <input type="text" id="role_display" class="form-control"
                               value="<?php echo htmlspecialchars($userData['role'] ?? ''); ?>" readonly style="background-color: #f8f9fa;">
                    </div>
                </div>

                <div class="mt-lg text-center">
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>

        <?php if ($userRole === 'Staff' && count($examResults) > 0): ?>
            <!-- Exam Results Card -->
            <div class="card mb-xl">
                <h2 class="text-primary mb-lg" style="border-bottom: 2px solid var(--secondary-color); padding-bottom: 0.5rem;">
                    <i class="fas fa-chart-line"></i> My Exam Results
                </h2>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Exam Code</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Exam Name</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Attempt</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Score</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Percentage</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Status</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examResults as $exam):
                                $percentage = ($exam['score'] / $exam['max_score']) * 100;
                                $status = $percentage >= 60 ? 'Passed' : 'Failed';
                                $badgeClass = $percentage >= 60 ? 'badge-success' : 'badge-danger';
                            ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($exam['code']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($exam['name']); ?></td>
                                    <td style="padding: 1rem;">#<?php echo htmlspecialchars($exam['attempt_no']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($exam['score']); ?>/<?php echo htmlspecialchars($exam['max_score']); ?></td>
                                    <td style="padding: 1rem;"><?php echo number_format($percentage, 2); ?>%</td>
                                    <td style="padding: 1rem;">
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo date('Y-m-d H:i', strtotime($exam['started_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($userRole === 'Staff'): ?>
            <div class="card mb-xl text-center">
                <h2><i class="fas fa-chart-line"></i> My Exam Results</h2>
                <p class="text-muted mt-md">You haven't taken any exams yet.</p>
            </div>
        <?php endif; ?>
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
