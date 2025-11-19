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
    <style>
        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .profile-header {
            background: linear-gradient(135deg, #10375C 0%, #EB8317 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .profile-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
        }

        .profile-header .user-role {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .profile-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .profile-card h2 {
            color: #10375C;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #EB8317;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #EB8317;
        }

        .form-group input[readonly] {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: normal;
        }

        .btn-primary {
            background: linear-gradient(135deg, #EB8317 0%, #10375C 100%);
            color: white;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(235, 131, 23, 0.4);
        }

        .btn-dashboard {
            background: #10375C;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 1rem;
            transition: background 0.3s;
        }

        .btn-dashboard:hover {
            background: #0d2d4a;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
        }

        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .exam-table th,
        .exam-table td {
            padding: 0.875rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .exam-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #10375C;
        }

        .exam-table tbody tr:hover {
            background: #f8f9fa;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <h1>Hello, <?php echo htmlspecialchars($userData['F_Name'] ?? 'User'); ?> <?php echo htmlspecialchars($userData['L_Name'] ?? ''); ?>!</h1>
            <p class="user-role">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($userRole); ?>
            </p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if ($userRole === 'Admin'): ?>
            <a href="../dashboards/admin_dashboard.php" class="btn-dashboard">
                <i class="fas fa-tachometer-alt"></i> Go to Admin Dashboard
            </a>
        <?php endif; ?>

        <!-- Profile Information Card -->
        <div class="profile-card">
            <h2><i class="fas fa-user-edit"></i> Profile Information</h2>

            <form method="POST" action="profile.php" id="profileForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname"
                               value="<?php echo htmlspecialchars($userData['F_Name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname"
                               value="<?php echo htmlspecialchars($userData['L_Name'] ?? ''); ?>" required>
                    </div>

                    <?php if ($userRole === 'Employee'): ?>
                        <div class="form-group">
                            <label for="cid">Employee ID</label>
                            <input type="text" id="cid" name="cid"
                                   value="<?php echo htmlspecialchars($userData['C_ID'] ?? ''); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="did">Department ID</label>
                            <input type="text" id="did" name="did"
                                   value="<?php echo htmlspecialchars($userData['D_ID'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="nic">NIC</label>
                            <input type="text" id="nic" name="nic"
                                   value="<?php echo htmlspecialchars($userData['NIC'] ?? ''); ?>"
                                   pattern="[0-9]{9}[Vv]|[0-9]{12}"
                                   title="NIC must be 9 digits followed by 'V' or 12 digits" required>
                        </div>

                        <div class="form-group">
                            <label for="gender_display">Gender</label>
                            <input type="text" id="gender_display"
                                   value="<?php echo htmlspecialchars($userData['Gender'] ?? ''); ?>" readonly>
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label for="sid">Staff ID</label>
                            <input type="text" id="sid" name="sid"
                                   value="<?php echo htmlspecialchars($userData['S_ID'] ?? ''); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="gender" value="Male"
                                           <?php echo (isset($userData['Gender']) && $userData['Gender'] == 'Male') ? 'checked' : ''; ?>>
                                    Male
                                </label>
                                <label>
                                    <input type="radio" name="gender" value="Female"
                                           <?php echo (isset($userData['Gender']) && $userData['Gender'] == 'Female') ? 'checked' : ''; ?>>
                                    Female
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($userData['Email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Mobile Number</label>
                        <input type="tel" id="phone" name="phone" pattern="[0-9]{10}"
                               placeholder="07XXXXXXXX"
                               value="<?php echo htmlspecialchars($phoneData[$phoneField] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob"
                               value="<?php echo htmlspecialchars($userData['DOB'] ?? ''); ?>" required>
                    </div>

                    <?php if ($userRole !== 'Employee'): ?>
                        <div class="form-group">
                            <label for="age_display">Age</label>
                            <input type="text" id="age_display"
                                   value="<?php echo htmlspecialchars($userData['Age'] ?? ''); ?>" readonly>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 2rem; text-align: center;">
                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>

        <?php if ($userRole === 'Employee' && count($examResults) > 0): ?>
            <!-- Exam Results Card -->
            <div class="profile-card">
                <h2><i class="fas fa-chart-line"></i> My Exam Results</h2>

                <table class="exam-table">
                    <thead>
                        <tr>
                            <th>Exam ID</th>
                            <th>Exam Name</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examResults as $exam): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($exam['E_ID']); ?></td>
                                <td><?php echo htmlspecialchars($exam['E_Name']); ?></td>
                                <td><?php echo htmlspecialchars($exam['Result'] ?? 'Pending'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($userRole === 'Employee'): ?>
            <div class="profile-card">
                <h2><i class="fas fa-chart-line"></i> My Exam Results</h2>
                <p class="no-results">You haven't taken any exams yet.</p>
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
