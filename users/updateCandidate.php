<?php

require('php/config.php');
session_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Employee'){
    header("Location: login.php");
    exit();
}

$cid = $_GET['updatcid'];

// Fetch the candidate details
$sql1 = "SELECT * FROM exam_candidate WHERE C_ID= '$cid'";
$result = mysqli_query($conn, $sql1);
$row = $result->fetch_assoc();

$Fname = $row['F_Name'];
$Lname = $row['L_Name'];
$Department_ID = $row['D_ID'];
$DOB = $row['DOB'];
$Email = $row['Email'];
$gender = $row['Gender'];

// Fetch candidate phone number
$sql1 = "SELECT * FROM exam_candidate_phone_no WHERE C_ID= '$cid'";
$result = mysqli_query($conn, $sql1);
$row = $result->fetch_assoc();

$phoneNo = $row['Phone_no'];

// Check if form is submitted
if (isset($_POST['submit'])) {
    $Fname = $_POST["fname"];
    $Lname = $_POST["lname"];
    $Department_ID = $_POST["D_ID"];
    $Email = $_POST["email"];
    $DOB = $_POST["dob"];
    $gender = $_POST["gender"];
    $phoneNo = $_POST["phone"];

    // Update the candidate information
    $sql2 = "UPDATE exam_candidate SET
        F_name='$Fname',
        L_name='$Lname',
        D_ID='$Department_ID',
        Gender= '$gender',
        Email ='$Email',
        DOB='$DOB'
        WHERE C_ID ='$cid'";

    $result = mysqli_query($conn, $sql2);

    $sql2 = "UPDATE exam_candidate_phone_no SET Phone_no='$phoneNo';";

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
    <title>Update Candidate</title>
</head>
<body>
    <?php
        include ('php/header.php')
    ?>
    <div class="Update" onsubmit="checkPassword()">
        <center> <h1>Update Exam Candidate Profile</h1></center><br>
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

            <center>
            <input type="submit" name="submit" id="submitbtn" value="Update"><br> 
            </center>
        </form>
    </div>
    <?php
        include ('php/footer.php');    
    ?>
</body>
</html>
