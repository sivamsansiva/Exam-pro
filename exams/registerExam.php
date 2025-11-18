<?php

    include('../config/config.php');
    // session_start();

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
        header("Location: ../auth/login.php");
        exit();
    }

    // Handle form submission
    $message = "";
    $messageType = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $employee_id = mysqli_real_escape_string($conn, $_POST['employee-id']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $exam_name = mysqli_real_escape_string($conn, $_POST['exam']);

        // Get exam ID from exam name
        $exam_query = "SELECT E_ID FROM exam WHERE E_Name = '$exam_name'";
        $exam_result = $conn->query($exam_query);

        if ($exam_result && $exam_result->num_rows > 0) {
            $exam_row = $exam_result->fetch_assoc();
            $exam_id = $exam_row['E_ID'];

            // Check if already registered
            $check_query = "SELECT * FROM attends WHERE E_ID = '$exam_id' AND C_ID = '$employee_id'";
            $check_result = $conn->query($check_query);

            if ($check_result && $check_result->num_rows > 0) {
                $message = "You are already registered for this exam!";
                $messageType = "warning";
            } else {
                // Register for exam (insert with Result as 0.00 initially)
                $register_query = "INSERT INTO attends (E_ID, C_ID, Result) VALUES ('$exam_id', '$employee_id', 0.00)";

                if ($conn->query($register_query)) {
                    $message = "Successfully registered for exam: " . htmlspecialchars($exam_name);
                    $messageType = "success";
                } else {
                    $message = "Error registering for exam: " . $conn->error;
                    $messageType = "error";
                }
            }
        } else {
            $message = "Selected exam not found!";
            $messageType = "error";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Registration</title>
    <link rel="stylesheet" href="../styles/examStyle.css">
    <style>
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: 500;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");

    ?>

    <!-- Exam registration form -->
    <div class="registration">

        <h1>Register Exam</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="registerExam.php" method="post">

            <label for="employee-id">Employee ID:</label>
            <input type="text" name="employee-id" id="employee-id" placeholder="Enter Employee ID" required><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" placeholder="Enter Email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required><br>

            <label for="exam">Select Exam:</label>
            <?php
                $sql = "SELECT E_Name FROM exam";
                $result = $conn->query($sql);
                if($result && $result->num_rows > 0){
            ?>
            <select name="exam" id="exam" required>
                <option value="" disabled selected>Select an exam</option>

                <?php
                    while ($row = $result->fetch_assoc()) {
                        $examName = $row['E_Name'];
                        echo "<option value=\"" . htmlspecialchars($examName) . "\">" . htmlspecialchars($examName) . "</option>";
                    }

                ?>
            </select><br>

                <?php
                    }
                    else{
                    echo "<p style='color: #721c24;'>No exam found.</p>";
                    }
                ?>

            <input type="submit" value="Register Exam">
        </form>

    </div>

    <!-- Footer -->
    <?php
        include ("../includes/footer.php");
    ?>

    <script src="../scripts/mainScript.js"></script>

</body>
</html>
