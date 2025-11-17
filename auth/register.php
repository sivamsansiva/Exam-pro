<?php
    include("php/config.php");

    // session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style/register.css">
    
</head>
<body>
    <div class="signUp">
        <h1>Sign Up</h1>

        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST" >

            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" required>

            <label>Gender</label>
            <label><input type="radio" name="gender" value="male" id="Male" > Male</label>
            <label><input type="radio" name="gender" value="female" id="Female"> Female</label>
            <label><input type="radio" name="gender" value="others"> Others</label>

            <label for="address">Address</label>
            <textarea id="address" name="address" rows="4" cols="50" required></textarea>

            <label for="dob">DOB</label>
            <input type="date" id="dob" name="dob" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" min="21" max="40" required>

            <label for="department">Department</label>
            <input type="text" id="department" name="department" required>

            <label for="employeeID">Employee ID</label>
            <input type="text" id="employeeID" name="employeeID" required>

            <label for="employeeID">Role</label>
            <input type="text" id="employeeID" name="role" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="NIC">NIC Number</label>
            <input type="text" id="NIC" name="NIC" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required> 

            <div class="term">
                <input type="checkbox" id="termCon" name="termCon" onclick="enableButton()" required > 
                <label for="termCon">Accept Terms & Conditions.</label>
            </div>

            <input type="submit" onclick="validateForm()" value="Register" id="submitbtn" name="submit">

            <p style="color: black;">Already have an account? <a href="orglogin.php">Login here</a></p> 
            
        </form>
    </div>

    <script src="script/registerUser.js"></script>
</body>
</html>

<?php

if(isset($_POST['submit'])){
    
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $department = $_POST['department'];
    $employeeID = $_POST['employeeID'];
    $email = $_POST['email'];
    $role=$_POST['role'];
    $NIC = $_POST['NIC'];
    $password = ($_POST['password']);
    $confirmPassword = ($_POST['confirmPassword']);
    // if ($password !== $confirmPassword) {
    //     echo "Passwords do not match!";
    //     exit();
    // }
    $select = "SELECT * 
               FROM staff 
               WHERE email = '$email'  && password = '$password'";

    $result = $conn->query($select);

    if($result->num_rows > 0){
        echo "User already exist!"; 

    }
    else{
        $insert = "INSERT 
                   INTO staff(S_ID,F_Name,L_Name,D_ID,DOB,NIC, Email,Gender,Age,Role,Password) 
                   VALUES ('$employeeID','$firstName', '$lastName','$department','$dob','$NIC','$email','$gender','$age','$role','$password')";
          
        if($conn->query($insert)){
            echo "Registration successful! ";
        }
        else{
            die($conn->error);
        }
    }
}
?>