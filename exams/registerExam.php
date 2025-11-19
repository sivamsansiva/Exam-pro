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

    // Get exam details if ID is provided
    $examDetails = null;
    $selectedExamId = isset($_GET['id']) ? $_GET['id'] : null;
    if ($selectedExamId) {
        $examQuery = "SELECT e.*, s.F_Name, s.L_Name, d.D_Name
                      FROM exam e
                      JOIN staff s ON e.S_ID = s.S_ID
                      JOIN department d ON s.D_ID = d.D_ID
                      WHERE e.E_ID = ?";
        $stmt = $conn->prepare($examQuery);
        $stmt->bind_param("s", $selectedExamId);
        $stmt->execute();
        $examDetails = $stmt->get_result()->fetch_assoc();
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
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");
    ?>

    <!-- Exam registration container -->
    <div class="registration-container">
        <!-- Exam Details Section -->
        <?php if ($examDetails): ?>
        <div class="exam-details">
            <h2><i class="fas fa-info-circle"></i> Exam Details</h2>
            <div class="exam-info">
                <label><i class="fas fa-file-alt"></i> Exam Name:</label>
                <span><?php echo htmlspecialchars($examDetails['E_Name']); ?></span>
            </div>
            <div class="exam-info">
                <label><i class="fas fa-hashtag"></i> Exam ID:</label>
                <span><?php echo htmlspecialchars($examDetails['E_ID']); ?></span>
            </div>
            <div class="exam-info">
                <label><i class="fas fa-clock"></i> Duration:</label>
                <span><?php echo htmlspecialchars($examDetails['Duration']); ?> minutes</span>
            </div>
            <div class="exam-info">
                <label><i class="fas fa-user-tie"></i> Examiner:</label>
                <span><?php echo htmlspecialchars($examDetails['F_Name'] . ' ' . $examDetails['L_Name']); ?></span>
            </div>
            <div class="exam-info">
                <label><i class="fas fa-building"></i> Department:</label>
                <span><?php echo htmlspecialchars($examDetails['D_Name']); ?></span>
            </div>
            <div class="exam-info">
                <label><i class="fas fa-align-left"></i> Description:</label>
                <span><?php echo isset($examDetails['Description']) ? htmlspecialchars($examDetails['Description']) : 'No description available'; ?></span>
            </div>
        </div>
        <?php else: ?>
        <div class="exam-details">
            <h2><i class="fas fa-info-circle"></i> Exam Information</h2>
            <p>Please select an exam from the registration form to view its details.</p>
        </div>
        <?php endif; ?>

        <!-- Registration Form Section -->
        <div class="registration-form">
            <h1><i class="fas fa-user-plus"></i> Register Exam</h1>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="registerExam.php<?php echo $selectedExamId ? '?id=' . htmlspecialchars($selectedExamId) : ''; ?>" method="post">

                <div class="form-field">
                    <label for="employee-id">Employee ID:</label>
                    <input type="text" name="employee-id" id="employee-id" placeholder="Enter Employee ID" required>
                </div>

                <div class="form-field">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                </div>

                <div class="form-field">
                    <label for="exam">Select Exam:</label>
                    <?php
                        $sql = "SELECT E_Name FROM exam";
                        $result = $conn->query($sql);
                        if($result && $result->num_rows > 0){
                    ?>
                    <select name="exam" id="exam" required>
                        <option value="" disabled <?php echo !$selectedExamId ? 'selected' : ''; ?>>Select an exam</option>

                        <?php
                            while ($row = $result->fetch_assoc()) {
                                $examName = $row['E_Name'];
                                $selected = ($examDetails && $examDetails['E_Name'] === $examName) ? 'selected' : '';
                                echo "<option value=\"" . htmlspecialchars($examName) . "\" $selected>" . htmlspecialchars($examName) . "</option>";
                            }

                        ?>
                    </select>
                    <?php
                        }
                        else{
                        echo "<p style='color: #721c24;'>No exam found.</p>";
                        }
                    ?>
                </div>

                <input type="submit" value="Register Exam">
            </form>
        </div>
    </div>

    <!-- Footer -->
    <?php
        include ("../includes/footer.php");
    ?>

    <script src="../scripts/script.js"></script>

</body>
</html>
