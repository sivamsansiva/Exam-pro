<?php
include('../config/config.php');

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
        <h2>Forgot Password</h2>
        <form id="forgotPasswordForm" method="POST" action="forgetpassword.php">
            <label for="nic">NIC:</label>
            <input type="text" id="nic" name="nic" required >

            <label for="staffId">Staff ID:</label>
            <input type="text" id="staffId" name="staffId" required >

            <button type="submit">Reset Password</button>
        </form>
        <?php
require ('../config/config.php');

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
    </div>
    <?php
        include ('../includes/footer.php')
    ?>
</body>
</html>
