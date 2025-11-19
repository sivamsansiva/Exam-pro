<?php
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #3e3939;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.password_container {
    background-color: #e0f7fa;
    padding: 35px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
    opacity: 2;
    /* transform: translateY(0); */
    /* transition: all 0.3s ease; */
}

h2 {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #00bcd4 ;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #ab4eba;
}

.hidden {
    opacity: 0;
    height: 0;
    visibility: hidden;
    transform: translateY(-20px);
    transition: all 0.3s ease;
}

#resetForm.show {
    opacity: 1;
    height: auto;
    visibility: visible;
    transform: translateY(0);
}

#message {
    color: red;
    margin-top: 10px;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

#message.show {
    opacity: 1;
    transform: translateY(0);
}
#errorMessage {
    color: red;
    margin-top: 10px;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s ease;
}

#errorMessage.show {
    opacity: 1;
    transform: translateY(0);
}
    </style>
</head>
<body>
    <?php
        include ('../includes/header.php')
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
        include ('../includes/footer.php')
    ?>
</body>
</html>
