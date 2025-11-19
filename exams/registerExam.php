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
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");
    ?>

    <!-- Exam registration container -->
    <div class="container mt-xl mb-xl">
        <div class="grid-2">
            <!-- Exam Details Section -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-info-circle"></i> Exam Details</h2>
                    <p class="card-subtitle">Information about the selected exam</p>
                </div>
                <div class="card-body">
                    <?php if ($examDetails): ?>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-file-alt"></i> Exam Name</label>
                            <div class="form-control" readonly><?php echo htmlspecialchars($examDetails['E_Name']); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-hashtag"></i> Exam ID</label>
                            <div class="form-control" readonly><?php echo htmlspecialchars($examDetails['E_ID']); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-clock"></i> Duration</label>
                            <div class="form-control" readonly><?php echo htmlspecialchars($examDetails['Duration']); ?> minutes</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-user-tie"></i> Examiner</label>
                            <div class="form-control" readonly><?php echo htmlspecialchars($examDetails['F_Name'] . ' ' . $examDetails['L_Name']); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-building"></i> Department</label>
                            <div class="form-control" readonly><?php echo htmlspecialchars($examDetails['D_Name']); ?></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                            <div class="form-control" readonly style="height: auto; min-height: 100px;"><?php echo isset($examDetails['Description']) ? htmlspecialchars($examDetails['Description']) : 'No description available'; ?></div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please select an exam from the registration form to view its details.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Registration Form Section -->
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title"><i class="fas fa-user-plus"></i> Register Exam</h1>
                    <p class="card-subtitle">Fill in the form to register for an exam</p>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType === 'error' ? 'error' : ($messageType === 'warning' ? 'warning' : 'success'); ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="registerExam.php<?php echo $selectedExamId ? '?id=' . htmlspecialchars($selectedExamId) : ''; ?>" method="post">

                        <div class="form-group">
                            <label for="employee-id" class="form-label">Employee ID</label>
                            <input type="text" name="employee-id" id="employee-id" class="form-control" placeholder="Enter Employee ID" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="exam" class="form-label">Select Exam</label>
                            <?php
                                $sql = "SELECT E_Name FROM exam";
                                $result = $conn->query($sql);
                                if($result && $result->num_rows > 0){
                            ?>
                            <select name="exam" id="exam" class="form-control" required onchange="if(this.value) window.location.href='registerExam.php?id=' + (this.options[this.selectedIndex].getAttribute('data-id') || '') + '&exam_name=' + encodeURIComponent(this.value)">
                                <option value="" disabled <?php echo !$selectedExamId ? 'selected' : ''; ?>>Select an exam</option>

                                <?php
                                    // We need E_ID to reload the page with details.
                                    // The current logic uses ?id=E_ID.
                                    // But the select option value is E_Name.
                                    // I need to fetch E_ID as well.
                                    $sql = "SELECT E_ID, E_Name FROM exam";
                                    $result = $conn->query($sql);

                                    while ($row = $result->fetch_assoc()) {
                                        $examName = $row['E_Name'];
                                        $examId = $row['E_ID'];
                                        $selected = ($examDetails && $examDetails['E_Name'] === $examName) ? 'selected' : '';
                                        echo "<option value=\"" . htmlspecialchars($examName) . "\" data-id=\"" . htmlspecialchars($examId) . "\" $selected>" . htmlspecialchars($examName) . "</option>";
                                    }

                                ?>
                            </select>
                            <?php
                                }
                                else{
                                echo "<p class='text-error'>No exam found.</p>";
                                }
                            ?>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">Register Exam</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
        include ("../includes/footer.php");
    ?>

    <script src="../scripts/script.js"></script>

</body>
</html>
