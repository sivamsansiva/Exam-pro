<?php
//Linking the configuration file
require('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Staff') {
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
</head>

<body>
    <?php
    include('../includes/header.php')
    ?>

    <div class="container mt-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title text-center">Submit Your Complaint</h2>
            </div>
            <div class="card-body">
                <form id="complaintForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="c_title" class="form-label">Complaint Title:</label>
                        <input type="text" id="c_title" name="c_title" class="form-control" placeholder="Enter Complaint Title" required>
                    </div>

                    <div class="form-group">
                        <label for="c_detail" class="form-label">Complaint Details:</label>
                        <textarea id="c_detail" name="c_detail" rows="8" class="form-control" placeholder="Describe your complaint in detail" required></textarea>
                    </div>

                    <div id="errorMessage" class="alert alert-error"></div>
                    <div id="successMessage" class="alert alert-success"></div>

                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg">Submit Complaint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../scripts/script.js"></script>
    <script>
        // JavaScript form validation
        document.getElementById("complaintForm").onsubmit = function(event) {
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
            if (!c_title || !c_detail) {
                event.preventDefault();
                errorMessage.style.display = "block";
                errorMessage.innerHTML = "All fields are required!";
                return;
            }
        };
    </script>
    <?php
    include('../includes/footer.php')
    ?>
</body>

</html>
<?php

if (isset($_POST["submit"])) {
    $userId = $_SESSION['user_id'];
    $c_title = $_POST["c_title"];
    $c_detail = $_POST["c_detail"];
    $messageType = 'complaint';
    $status = 'open';

    // Combine title and detail for message content
    $messageContent = "Title: " . $c_title . "\n\n" . $c_detail;

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO message (user_id, type, content, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $userId, $messageType, $messageContent, $status);

    // Execute query
    if ($stmt->execute()) {
        $stmt->close();
        echo '<script>alert("Complaint submitted successfully!");</script>';
        echo '<script>window.location.href = "../index.php";</script>';
    } else {
        $stmt->close();
        echo "Error: Unable to submit complaint.";
    }
}

?>
