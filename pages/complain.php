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
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php
        include ('../includes/header.php')
    ?>

    <div class="container mt-4 mb-4">
        <div class="card" style="max-width: 700px; margin: 0 auto;">
            <div class="card-header">
                <h2 class="card-title text-center">Submit Your Complaint</h2>
            </div>
            <div class="card-body">
                <form id="complaintForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="form-group">
                            <label for="C_no" class="form-label">Complain No:</label>
                            <input type="text" id="C_no" name="C_no" class="form-control" placeholder="Enter Complaint No" required>
                        </div>

                        <div class="form-group">
                            <label for="emp_id" class="form-label">Employee ID:</label>
                            <input type="text" id="emp_id" name="emp_id" class="form-control" placeholder="Enter your Employee ID" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="emp_email" class="form-label">Employee Email:</label>
                        <input type="email" id="emp_email" name="emp_email" class="form-control" placeholder="Enter your Email" required>
                    </div>

                    <div class="form-group">
                        <label for="c_title" class="form-label">Complaint Title:</label>
                        <input type="text" id="c_title" name="c_title" class="form-control" placeholder="Enter Complaint Title" required>
                    </div>

                    <div class="form-group">
                        <label for="complaint_date" class="form-label">Date of Incident:</label>
                        <input type="date" id="complaint_date" name="complaint_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="c_detail" class="form-label">Complaint Details:</label>
                        <textarea id="c_detail" name="c_detail" rows="5" class="form-control" placeholder="Enter the details of your complaint" required></textarea>
                    </div>

                    <div id="errorMessage" class="alert alert-error" style="display: none;"></div>
                    <div id="successMessage" class="alert alert-success" style="display: none;"></div>

                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg" style="width: 100%;">Submit Complaint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../scripts/script.js"></script>
    <script>
        // JavaScript form validation
        document.getElementById("complaintForm").onsubmit = function(event)
        {
            // Note: If you want PHP to handle the submission, you shouldn't prevent default unless validation fails.
            // But the original code prevented default and then didn't submit via AJAX, so it just showed a message.
            // I will assume the user wants client-side validation first, then submission.

            let emp_id = document.getElementById("emp_id").value;
            let emp_email = document.getElementById("emp_email").value;
            let c_title = document.getElementById("c_title").value;
            let c_detail = document.getElementById("c_detail").value;

            let errorMessage = document.getElementById("errorMessage");
            let successMessage = document.getElementById("successMessage");

            // Clear any previous messages
            errorMessage.style.display = "none";
            errorMessage.innerHTML = "";
            successMessage.style.display = "none";
            successMessage.innerHTML = "";

            // Validate fields
            if (!emp_id || !emp_email || !c_title || !c_detail) {
                event.preventDefault();
                errorMessage.style.display = "block";
                errorMessage.innerHTML = "All fields are required!";
                return;
            }

            if (!validateEmail(emp_email)) {
                event.preventDefault();
                errorMessage.style.display = "block";
                errorMessage.innerHTML = "Please enter a valid email address.";
                return;
            }

            // If validation is successful, let the form submit to PHP
            // Or if you want to show success message without submitting (demo mode), keep preventDefault
            // But since there is PHP code to handle insertion, we should let it submit.
        };

        // Function to validate email format
        function validateEmail(email) {
            var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return re.test(String(email).toLowerCase());
        }
    </script>
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
