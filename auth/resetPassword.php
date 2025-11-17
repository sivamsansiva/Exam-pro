<?php
include('php/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style/password.css">
</head>
<body>
    <?php 
        include ('php/header.php')
    ?>
    <div class="password_container">
        <h2>Reset Password</h2>
        <form method="POST" action="resetpassword.php" method="post">
            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <button type="submit">Update Password</button>
        </form>
        <div id="message"></div>
    </div>
    <?php 
        include ('php/footer.php')
    ?>
</body>
</html>
<?php
// if (!isset($_SESSION['nic']) || !isset($_SESSION['staffId'])) {
//     die("Session variables not set. Please go back to the forgot password page.");
// }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    
     if($newPassword === $confirmPassword)
     {
        $nic = $_SESSION['nic'];
        $staffId = $_SESSION['staffId'];

        $hashedPassword = password_hash($newPassword,PASSWORD_DEFAULT);

        $query="UPDATE staff SET password = '$hashedPassword' WHERE NIC = '$nic' AND S_ID='$staffId'";

        if(mysqli_query($conn,$query))
        {
            $message = "Password has been update successfully....";
            session_destroy();
          header("Location: login.php"); // Redirect after successful update
        exit();
        }
        else{
            $message = "Error in updating password.". mysqli_error($conn); ;
        }
    }
        else{
            $message ="password do not match!!!!";
        }
     }

?>