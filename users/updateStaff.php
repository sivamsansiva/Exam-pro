<?php
require('php/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Employee'){
    header("Location: login.php");
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
            echo '<script>window.location.href = "manager.php";</script>';
        } elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "admin.php";</script>';
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
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/updateStyle.css">
    <link rel="stylesheet" href="style/examStyle.css"> 
    <title>Update Staff</title>
</head>
<body>
    <?php
        include ('php/header.php')
    ?>
    <div class="Update" onsubmit="checkPassword()">
        <center> <h1>Update Exam Staff Profile</h1></center><br>
        <form method="post" id="form" action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>">
            <label>First Name: </label>
            <input type="text" name="fname" placeholder="Enter First Name" value="<?php echo $Fname; ?>" required><br>

            <label>Last Name: </label>
            <input type="text" name="lname" placeholder="Enter Last Name" value="<?php echo $Lname; ?>" required><br>

            <label>Gender: </label>
            <label>
                <input type="radio" id="Male" name="gender" value="Male" <?php if ($gender == "Male") echo "checked"; ?>> Male
                <input type="radio" id="Female" name="gender" value="Female" <?php if ($gender == "Female") echo "checked"; ?>> Female<br>
            </label><br>

            <label>Mobile Number: </label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="07XXXXXXXX" value="<?php echo $phoneNo; ?>" required><br>

            <label>Email: </label>
            <input type="email" id="email" name="email" placeholder="Enter Email" value="<?php echo $Email; ?>" required><br>

            <label>Date of Birth: </label>
            <input type="date" name="dob" value="<?php echo $DOB; ?>" required><br>

            <label>Department ID: </label>
            <select name="D_ID" required>
                <option value="D001" <?php if ($Department_ID == "D001") echo "selected"; ?>>D001</option>
                <option value="D002" <?php if ($Department_ID == "D002") echo "selected"; ?>>D002</option>
                <option value="D003" <?php if ($Department_ID == "D003") echo "selected"; ?>>D003</option>
                <option value="D004" <?php if ($Department_ID == "D004") echo "selected"; ?>>D004</option>
                <option value="D005" <?php if ($Department_ID == "D005") echo "selected"; ?>>D005</option>
            </select><br>

            <label>Staff Role: </label>
            <select name="Role" required>
                <option value="Examiner" <?php if ($Role == "Examiner") echo "selected"; ?>>Examiner</option>
                <option value="Manager" <?php if ($Department_ID == "Manager") echo "selected"; ?>>Manager</option>
                <option value="Admin" <?php if ($Department_ID == "Admin") echo "selected"; ?>>Admin</option>
            </select><br>
            <center>
            <input type="submit" name="submit" id="submitbtn" value="Update"><br> 
            </center>
        </form>
    </div>
   <br>
   <br>

   <?php
        include ('php/footer.php');    
    ?>
</body>
</html>


