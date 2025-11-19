<?php
require('../config/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Employee'){
    header("Location: ../auth/login.php");
    exit();
}
global $conn;
$Sid = $_GET['updateid'];

// Fetch the candidate details
$sql1 = "SELECT * FROM staff WHERE S_ID= '$Sid'";
$result = mysqli_query($conn, $sql1);
$row = $result->fetch_assoc();

$Fname = $row['F_Name'];
$Lname = $row['L_Name'];
$Department_ID = $row['D_ID'];
$DOB = $row['DOB'];
$Email = $row['Email'];
$gender = $row['Gender'];
$Age=$row['Age'];
$Role=$row['Role'];

// Fetch candidate phone number
$sql1 = "SELECT * FROM staff_phone_no WHERE S_ID= '$Sid'";
$result = mysqli_query($conn, $sql1);
$row = $result->fetch_assoc();

$phoneNo = $row['S_phone_no'];

// Check if form is submitted
if (isset($_POST['submit'])) {
    $Fname = $_POST["fname"];
    $Lname = $_POST["lname"];
    $Department_ID = $_POST["D_ID"];
    $Email = $_POST["email"];
    $DOB = $_POST["dob"];
    $gender = $_POST["gender"];
    $Role = $_POST["Role"];
    $phoneNo =$_POST['S_phone_no'];


    // Update the candidate information
    $sql2 = "UPDATE staff SET
        F_name='$Fname',
        L_name='$Lname',
        D_ID='$Department_ID',
        Gender= '$gender',
        Email ='$Email',
        DOB='$DOB',
        Role='$Role'
        WHERE S_ID='$Sid'";

    $result = mysqli_query($conn, $sql2);

   $sql2="UPDATE  staff_phone_no SET S_phone_no='$phoneNo' WHERE S_ID='$Sid'";

   $result = mysqli_query($conn, $sql2);

    if ($result) {
        echo '<script>alert("Updated Successfully");</script>';

        // Redirect based on user role
        if ($_SESSION['Role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        } elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        }
    } else {
        die($conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Staff - ExamPro</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container mt-xl">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <h2 class="text-primary mb-lg text-center">
                <i class="fas fa-user-edit"></i> Update Exam Staff Profile
            </h2>

            <form method="post" id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?updateid=' . $Sid; ?>">
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
                        <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number</label>
                        <input type="tel" name="S_phone_no" class="form-control" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo htmlspecialchars($phoneNo); ?>" required>
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
                        <label class="form-label">Department ID</label>
                        <select name="D_ID" class="form-control" required>
                            <option value="D001" <?php if ($Department_ID == "D001") echo "selected"; ?>>D001</option>
                            <option value="D002" <?php if ($Department_ID == "D002") echo "selected"; ?>>D002</option>
                            <option value="D003" <?php if ($Department_ID == "D003") echo "selected"; ?>>D003</option>
                            <option value="D004" <?php if ($Department_ID == "D004") echo "selected"; ?>>D004</option>
                            <option value="D005" <?php if ($Department_ID == "D005") echo "selected"; ?>>D005</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Staff Role</label>
                        <select name="Role" class="form-control" required>
                            <option value="Examiner" <?php if ($Role == "Examiner") echo "selected"; ?>>Examiner</option>
                            <option value="Manager" <?php if ($Role == "Manager") echo "selected"; ?>>Manager</option>
                            <option value="Admin" <?php if ($Role == "Admin") echo "selected"; ?>>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="mt-lg text-center">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Staff
                    </button>
                    <a href="javascript:history.back()" class="btn btn-secondary" style="margin-left: 1rem;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>


