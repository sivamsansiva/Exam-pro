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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style/password.css">
</head>
<body>
    <?php 
        include ('php/header.php')
    ?>
    <div class="password_container">
        <h2>Forgot Password</h2>
        <form id="forgotPasswordForm" method="POST" action="forgetpassword.php">
            <label for="nic">NIC:</label>
            <input type="text" id="nic" name="nic" required >
            
            <label for="staffId">Staff ID:</label>
            <input type="text" id="staffId" name="staffId" required >

            <button type="submit">Reset Password</button>
        </form>
        <?php
require ('php/config.php');

if($_SERVER['REQUEST_METHOD']=='POST')
{
    $nic=$_POST['nic'];
    $staffId = $_POST['staffId'];

    $query="SELECT * From staff WHERE NIC = '$nic' AND S_ID= '$staffId'";
    $result = mysqli_query($conn,$query);

    if($result && mysqli_num_rows($result)>0)
    {
        $_SESSION['nic'] = $nic; // Store NIC in session
        $_SESSION['staffId'] = $staffId; // Store Staff ID in session
        header("Location: resetPassword.php"); 
        exit();
    }
    else{
        $_SESSION['error_message'] = "NIC or Staff ID is not correct"; // Store error message in session
        header("Location: forgetPassword.php"); // Redirect back to the same page
        exit();
    }
}
mysqli_close($conn);
?>

    <!-- <script src="script.js"></script> -->
</body>
</html>

    <!-- <script src="script.js"></script> -->
     <script>
        document.addEventListener("DOMContentLoaded", function () {
            const resetForm = document.getElementById('resetForm');
            const message = document.getElementById('message');
        
            // Show error message if present
            if (message.textContent) {
                message.classList.add('show');
            }
        
            // Add event listener for update password button
            document.getElementById('updatePasswordButton').addEventListener('click', function() {
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
        
                // Check if passwords match and are not empty
                if (newPassword && confirmPassword && newPassword === confirmPassword) {
                    // Simulate updating password in the database
                    alert('Password has been updated successfully!');
                    message.textContent = ''; // Clear any previous messages
                } else {
                    // Show error message if passwords do not match or are empty
                    message.textContent = 'Passwords do not match or fields are empty!';
                    message.classList.add('show'); // Trigger message animation
                }
            });
        });
        
     </script>
     <?php 
        include ('php/footer.php')
    ?>
</body>
</html>
