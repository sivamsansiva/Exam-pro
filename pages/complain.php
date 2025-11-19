<?php
//Linking the configuration file
require ('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
    header("Location: ../auth/login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Complaint Form</title>
    <link rel="stylesheet" href="../styles/style.css"> <!-- Link to the CSS file -->
    <style>

body{
    background: linear-gradient(90deg, #ffffff 0%, #EB8317 35%, #10375C 100%);
}
.complain h2{
    text-align: center;
}

.complain form {
    background-color: #fff;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
    animation: fadeIn 1.5s ease-in-out; /* Animation for form */
}

.complain input, textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: box-shadow 0.3s ease; /* Smooth focus animation */
}

.complain input:focus, textarea:focus, input[type="date"]:focus {
    box-shadow: 0 0 5px #031403; /* Green glow when focused */
}

.complain input[type="date"] {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    margin-bottom: 10px;
    font-family: Arial, sans-serif;
}

.complain button {
    padding: 10px 15px;
    background-color: #0c0e85;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease; /* Smooth hover animation */
}

.complain button:hover {
    background-color: #4b9cda;
}

.complain .error {
    color: red;
    font-size: 0.9em;
    margin-bottom: 10px;
}

.complain .success {
    color: rgb(13, 6, 76);
    font-size: 1em;
    margin-bottom: 10px;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
    <?php
        include ('../includes/header.php')
    ?>
    <div class="complain">
    <h2>Submit Your Complaint</h2>
    <form id="complaintForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="emp_id">Complain No:</label>
    <input type="text" id="emp_id" name="C_no" placeholder="Enter your Employee ID" required>

    <label for="emp_id">Employee ID:</label>
    <input type="text" id="emp_id" name="emp_id" placeholder="Enter your Employee ID" required>

    <label for="emp_email">Employee Email:</label>
    <input type="email" id="emp_email" name="emp_email" placeholder="Enter your Email" required>

    <label for="c_title">Complaint Title:</label>
    <input type="text" id="c_title" name="c_title" placeholder="Enter Complaint Title" required>

    <label for="complaint_date">Date of Incident:</label>
    <input type="date" id="complaint_date" name="complaint_date" required>

    <label for="c_detail">Complaint Details:</label>
    <textarea id="c_detail" name="c_detail" rows="5" placeholder="Enter the details of your complaint" required></textarea>

    <div id="errorMessage" class="error"></div>
    <div id="successMessage" class="success"></div>

    <button type="submit" name="submit">Submit Complaint</button>
</form>

    </div>

    <script src="../scripts/script.js"></script>
    <script>
        // JavaScript form validation
document.getElementById("complaintForm").onsubmit = function(event)
{
    event.preventDefault(); // Prevent default form submission behavior

    let emp_id = document.getElementById("emp_id").value;
    let emp_email = document.getElementById("emp_email").value;
    let c_title = document.getElementById("c_title").value;
    let c_detail = document.getElementById("c_detail").value;

    let errorMessage = document.getElementById("errorMessage");
    let successMessage = document.getElementById("successMessage");

    // Clear any previous messages
    errorMessage.innerHTML = "";
    successMessage.innerHTML = "";

    // Validate fields
    if (!emp_id || !emp_email || !c_title || !c_detail) {
        errorMessage.innerHTML = "All fields are required!";
        return;
    }

    if (!validateEmail(emp_email)) {
        errorMessage.innerHTML = "Please enter a valid email address.";
        return;
    }

    // If validation is successful, show a success message
    successMessage.innerHTML = "Complaint submitted successfully!";

    // Reset the form after successful submission
    document.getElementById("complaintForm").reset();
};

// Function to validate email format
function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(String(email).toLowerCase());
}

    </script> <!-- Link to the JavaScript file -->
    <?php
        include ('../includes/footer.php')
    ?>
</body>
</html>
<?php

if(isset($_POST["submit"])){

    // Sanitize inputs
    $C_No = $_POST["C_no"];
    $emp_id =$_POST["emp_id"];
    $emp_email = $_POST["emp_email"]; // Use FILTER_SANITIZE_EMAIL for emails
    $complaint_date =$_POST["complaint_date"];
    $c_title = $_POST["c_title"];
    $c_detail = $_POST["c_detail"];
    // Prepare SQL query
    $sql = "INSERT INTO complaint (C_No,C_ID, C_Email,C_Date, C_Title, C_Details)
            VALUES ('$C_No','$emp_id', '$emp_email', '$complaint_date','$c_title', '$c_detail')";

    // Execute query
    if($conn->query($sql) === TRUE) {
        echo "Inserted successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}


?>
