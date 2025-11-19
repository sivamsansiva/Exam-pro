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

// Initialize variables
$userData = [];
$phoneData = [];
$updateSuccess = false;

// Determine table and ID based on role
if ($userRole === 'Employee') {
    // For candidates/employees
    $idField = 'C_ID';
    $table = 'exam_candidate';
    $phoneTable = 'exam_candidate_phone_no';
    $phoneField = 'Phone_no';

    // Get user ID from session or database
    $query = "SELECT * FROM $table WHERE Email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);

    if ($userData) {
        $userId = $userData[$idField];

        // Fetch phone number
        $phoneQuery = "SELECT * FROM $phoneTable WHERE $idField = ?";
        $phoneStmt = mysqli_prepare($conn, $phoneQuery);
        mysqli_stmt_bind_param($phoneStmt, "s", $userId);
        mysqli_stmt_execute($phoneStmt);
        $phoneResult = mysqli_stmt_get_result($phoneStmt);
        $phoneData = mysqli_fetch_assoc($phoneResult);
    }
} else {
    // For staff (Admin, Manager, Examiner)
    $idField = 'S_ID';
    $table = 'staff';
    $phoneTable = 'staff_phone_no';
    $phoneField = 'S_phone_no';

    // Get user data
    $query = "SELECT * FROM $table WHERE Email = ? AND Role = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $userEmail, $userRole);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);

    if ($userData) {
        $userId = $userData[$idField];

        // Fetch phone number
        $phoneQuery = "SELECT * FROM $phoneTable WHERE $idField = ?";
        $phoneStmt = mysqli_prepare($conn, $phoneQuery);
        mysqli_stmt_bind_param($phoneStmt, "s", $userId);
        mysqli_stmt_execute($phoneStmt);
        $phoneResult = mysqli_stmt_get_result($phoneStmt);
        $phoneData = mysqli_fetch_assoc($phoneResult);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    if ($userRole === 'Employee') {
        $nic = mysqli_real_escape_string($conn, $_POST['nic']);
        $did = mysqli_real_escape_string($conn, $_POST['did']);

        $updateQuery = "UPDATE $table SET
            F_Name = ?,
            L_Name = ?,
            Email = ?,
            DOB = ?,
            NIC = ?,
            D_ID = ?
            WHERE $idField = ?";

        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "sssssss", $fname, $lname, $email, $dob, $nic, $did, $userId);
    } else {
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);

        $updateQuery = "UPDATE $table SET
            F_Name = ?,
            L_Name = ?,
            Email = ?,
            DOB = ?,
            Gender = ?
            WHERE $idField = ?";

        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ssssss", $fname, $lname, $email, $dob, $gender, $userId);
    }

    if (mysqli_stmt_execute($updateStmt)) {
        // Update phone number
        $phoneUpdateQuery = "UPDATE $phoneTable SET $phoneField = ? WHERE $idField = ?";
        $phoneUpdateStmt = mysqli_prepare($conn, $phoneUpdateQuery);
        mysqli_stmt_bind_param($phoneUpdateStmt, "ss", $phone, $userId);
        mysqli_stmt_execute($phoneUpdateStmt);

        $updateSuccess = true;

        // Refresh data
        header("Location: profile.php?success=1");
        exit();
    }
}

// Get exam results for employees
$examResults = [];
if ($userRole === 'Employee' && isset($userId)) {
    $examQuery = "SELECT exam.E_ID, exam.E_Name, attends.Result
                  FROM exam
                  JOIN attends ON exam.E_ID = attends.E_ID
                  WHERE attends.C_ID = ?";
    $examStmt = mysqli_prepare($conn, $examQuery);
    mysqli_stmt_bind_param($examStmt, "s", $userId);
    mysqli_stmt_execute($examStmt);
    $examResult = mysqli_stmt_get_result($examStmt);

    while ($row = mysqli_fetch_assoc($examResult)) {
        $examResults[] = $row;
    }
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
                <h1 style="margin: 0 0 0.5rem 0; font-size: 2rem;">Hello, <?php echo htmlspecialchars($userData['F_Name'] ?? 'User'); ?> <?php echo htmlspecialchars($userData['L_Name'] ?? ''); ?>!</h1>
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
                               value="<?php echo htmlspecialchars($userData['F_Name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" id="lname" name="lname" class="form-control"
                               value="<?php echo htmlspecialchars($userData['L_Name'] ?? ''); ?>" required>
                    </div>

                    <?php if ($userRole === 'Employee'): ?>
                        <div class="form-group">
                            <label for="cid" class="form-label">Employee ID</label>
                            <input type="text" id="cid" name="cid" class="form-control"
                                   value="<?php echo htmlspecialchars($userData['C_ID'] ?? ''); ?>" readonly style="background-color: #f8f9fa;">
                        </div>

                        <div class="form-group">
                            <label for="did" class="form-label">Department ID</label>
                            <input type="text" id="did" name="did" class="form-control"
                                   value="<?php echo htmlspecialchars($userData['D_ID'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="nic" class="form-label">NIC</label>
                            <input type="text" id="nic" name="nic" class="form-control"
                                   value="<?php echo htmlspecialchars($userData['NIC'] ?? ''); ?>"
                                   pattern="[0-9]{9}[Vv]|[0-9]{12}"
                                   title="NIC must be 9 digits followed by 'V' or 12 digits" required>
                        </div>

                        <div class="form-group">
                            <label for="gender_display" class="form-label">Gender</label>
                            <input type="text" id="gender_display" class="form-control"
                                   value="<?php echo htmlspecialchars($userData['Gender'] ?? ''); ?>" readonly style="background-color: #f8f9fa;">
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label for="sid" class="form-label">Staff ID</label>
                            <input type="text" id="sid" name="sid" class="form-control"
                                   value="<?php echo htmlspecialchars($userData['S_ID'] ?? ''); ?>" readonly style="background-color: #f8f9fa;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Gender</label>
                            <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                                <label style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="radio" name="gender" value="Male"
                                           <?php echo (isset($userData['Gender']) && $userData['Gender'] == 'Male') ? 'checked' : ''; ?>>
                                    Male
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="radio" name="gender" value="Female"
                                           <?php echo (isset($userData['Gender']) && $userData['Gender'] == 'Female') ? 'checked' : ''; ?>>
                                    Female
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?php echo htmlspecialchars($userData['Email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Mobile Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" pattern="[0-9]{10}"
                               placeholder="07XXXXXXXX"
                               value="<?php echo htmlspecialchars($phoneData[$phoneField] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="form-control"
                               value="<?php echo htmlspecialchars($userData['DOB'] ?? ''); ?>" required>
                    </div>

                    <?php if ($userRole !== 'Employee'): ?>
                        <div class="form-group">
                            <label for="age_display" class="form-label">Age</label>
                            <input type="text" id="age_display" class="form-control"
                                   value="<?php echo htmlspecialchars($userData['Age'] ?? ''); ?>" readonly style="background-color: #f8f9fa;">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-lg text-center">
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>

        <?php if ($userRole === 'Employee' && count($examResults) > 0): ?>
            <!-- Exam Results Card -->
            <div class="card mb-xl">
                <h2 class="text-primary mb-lg" style="border-bottom: 2px solid var(--secondary-color); padding-bottom: 0.5rem;">
                    <i class="fas fa-chart-line"></i> My Exam Results
                </h2>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Exam ID</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Exam Name</th>
                                <th style="padding: 1rem; text-align: left; color: var(--primary-color);">Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examResults as $exam): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($exam['E_ID']); ?></td>
                                    <td style="padding: 1rem;"><?php echo htmlspecialchars($exam['E_Name']); ?></td>
                                    <td style="padding: 1rem;">
                                        <span class="badge <?php echo ($exam['Result'] == 'Pass') ? 'badge-success' : (($exam['Result'] == 'Fail') ? 'badge-danger' : 'badge-warning'); ?>">
                                            <?php echo htmlspecialchars($exam['Result'] ?? 'Pending'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($userRole === 'Employee'): ?>
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

            <?php if ($userRole === 'Employee'): ?>
            const nic = document.getElementById('nic').value;
            const nicPattern = /^[0-9]{9}[Vv]$|^[0-9]{12}$/;

            if (!nicPattern.test(nic)) {
                e.preventDefault();
                alert('NIC must be 9 digits followed by V or 12 digits.');
                return false;
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>
