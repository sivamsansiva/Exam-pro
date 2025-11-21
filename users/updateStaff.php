<?php
require('../config/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}
global $conn;
$userId = $_GET['updateid'];

// Fetch the user details using prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$Fname = $row['first_name'];
$Lname = $row['last_name'];
$Department_ID = $row['department_id'];
$DOB = $row['dob'];
$Email = $row['email'];
$gender = $row['gender'];
$Role = $row['role'];

// Fetch user phone number
$stmt = $conn->prepare("SELECT phone_number FROM user_phone WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$phoneRow = $result->fetch_assoc();
$stmt->close();

$phoneNo = $phoneRow['phone_number'] ?? '';

// Check if form is submitted
if (isset($_POST['submit'])) {
    $Fname = $_POST["fname"];
    $Lname = $_POST["lname"];
    $Department_ID = $_POST["department_id"];
    $Email = $_POST["email"];
    $DOB = $_POST["dob"];
    $gender = $_POST["gender"];
    $Role = $_POST["role"];
    $phoneNo = $_POST['phone_no'];

    // Update the user information
    $stmt = $conn->prepare("
        UPDATE users SET
            first_name = ?,
            last_name = ?,
            department_id = ?,
            gender = ?,
            email = ?,
            dob = ?,
            role = ?
        WHERE id = ?
    ");
    $stmt->bind_param("sssssssi", $Fname, $Lname, $Department_ID, $gender, $Email, $DOB, $Role, $userId);
    $result = $stmt->execute();
    $stmt->close();

    // Update or insert phone number
    $stmt = $conn->prepare("SELECT id FROM user_phone WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $phoneResult = $stmt->get_result();
    $stmt->close();

    if ($phoneResult->num_rows > 0) {
        // Update existing phone
        $stmt = $conn->prepare("UPDATE user_phone SET phone_number = ? WHERE user_id = ?");
        $stmt->bind_param("si", $phoneNo, $userId);
        $stmt->execute();
        $stmt->close();
    } else {
        // Insert new phone
        $stmt = $conn->prepare("INSERT INTO user_phone (user_id, phone_number) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $phoneNo);
        $stmt->execute();
        $stmt->close();
    }

    if ($result) {
        echo '<script>alert("Updated Successfully");</script>';

        // Redirect based on user role
        if ($_SESSION['role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        } elseif ($_SESSION['role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        }
    } else {
        die("Update failed.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User - ExamPro</title>
    <link rel="stylesheet" href="../styles/core.css">
    <link rel="stylesheet" href="../styles/components.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-form-wrapper {
            max-width: 800px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="card">
            <h2 class="text-primary mb-lg text-center">
                <i class="fas fa-user-edit"></i> Update Exam Staff Profile
            </h2>

            <form method="post" id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?updateid=' . $userId; ?>">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="fname" class="form-control" placeholder="Enter First Name" value="<?php echo htmlspecialchars($Fname); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="lname" class="form-control" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($Lname); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <div>
                            <label>
                                <input type="radio" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" name="phone_no" class="form-control" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($phoneNo); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" value="<?php echo htmlspecialchars($Email); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($DOB); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-control" required>
                            <option value="">Select Department</option>
                            <?php
                            $deptQuery = $conn->query("SELECT id, name FROM department ORDER BY name");
                            while ($dept = $deptQuery->fetch_assoc()) {
                                $selected = ($Department_ID == $dept['id']) ? 'selected' : '';
                                echo "<option value='{$dept['id']}' {$selected}>{$dept['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Staff Role</label>
                        <select name="role" class="form-control" required>
                            <option value="Staff" <?php if ($Role == "Staff") echo "selected"; ?>>Staff</option>
                            <option value="Examiner" <?php if ($Role == "Examiner") echo "selected"; ?>>Examiner</option>
                            <option value="Manager" <?php if ($Role == "Manager") echo "selected"; ?>>Manager</option>
                        </select>
                    </div>
                </div>

                <div class="mt-lg text-center">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Staff
                    </button>
                    <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>

</html>
