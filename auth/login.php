<?php
require('php/config.php'); // Add a semicolon here

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    global $conn;

    // Query to select user with the given email
    $sql = "SELECT * FROM staff WHERE Email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $row = $result->fetch_assoc();
        
        // Compare the input password directly with the password stored in the database
        if ($password == $row['Password']) {
            // Set session variables
            $_SESSION['email'] = $row['Email'];
            $_SESSION['role'] = $row['Role']; // assuming Role column stores roles like Admin, Examiner, Manager
            
            // Redirect based on role
            if ($row['Role'] == 'Admin') {
                header("Location: admin.php");
            } elseif ($row['Role'] == 'Examiner') {
                header("Location: examiner.php");
            } elseif ($row['Role'] == 'Manager') {
                header("Location: manager.php");
            } else {
                header("Location: index.php");
            }
        } else {
            echo '<script>alert("Invalid password!")</script>';
        }
    } else {
        echo '<script>alert("Invalid password or Email!")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/login.css">
</head>
<body>

<!--Login form content-->
<div class="container">
    <div class="login-box">
        <h2>Login</h2>
        <form id="login-form" action="login.php" method="post">

            <div class="textbox">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="textbox">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn" name="submit">Login</button>

            <p><a href="forgetPassword.php" id="forgetassword-link">Forgot Password?</a></p>
                <p>Don't have an account? <a href="registerUser.php" id="signup-link">Sign Up</a></p>
        </form>
    </div>
</div>
    
</body>
</html>
