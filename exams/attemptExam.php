<?php
    require ("../config/config.php");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Staff'){
        header("Location: ../auth/login.php");
        exit();
    }

    $userId = $_SESSION['user_id'] ?? null;
    $message = "";
    $messageType = "";

    // Handle exam attempt start
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $userId) {
        $examId = (int)$_POST['exam_id'];
        $quizPassword = $_POST['quiz_password'];

        // Verify exam exists and get password hash
        $examQuery = "SELECT id, name, quiz_password_hash, duration_minutes FROM exam WHERE id = ?";
        $stmt = $conn->prepare($examQuery);
        $stmt->bind_param("i", $examId);
        $stmt->execute();
        $exam = $stmt->get_result()->fetch_assoc();

        if ($exam) {
            // Verify password
            if (password_verify($quizPassword, $exam['quiz_password_hash'])) {
                // Check if registered
                $regCheck = "SELECT * FROM exam_registration WHERE exam_id = ? AND user_id = ? AND status = 'registered'";
                $stmt = $conn->prepare($regCheck);
                $stmt->bind_param("ii", $examId, $userId);
                $stmt->execute();
                $registration = $stmt->get_result()->fetch_assoc();

                if ($registration) {
                    // Check for existing incomplete attempts
                    $attemptCheck = "SELECT id FROM exam_attempt WHERE exam_id = ? AND user_id = ? AND completed = 0 ORDER BY started_at DESC LIMIT 1";
                    $stmt = $conn->prepare($attemptCheck);
                    $stmt->bind_param("ii", $examId, $userId);
                    $stmt->execute();
                    $existingAttempt = $stmt->get_result()->fetch_assoc();

                    if ($existingAttempt) {
                        $message = "You have an incomplete attempt for this exam. Please complete it first.";
                        $messageType = "warning";
                        // Redirect to exam taking page (to be implemented)
                        // header("Location: takeExam.php?attempt_id=" . $existingAttempt['id']);
                    } else {
                        // Get attempt number
                        $attemptCountQuery = "SELECT COUNT(*) as count FROM exam_attempt WHERE exam_id = ? AND user_id = ?";
                        $stmt = $conn->prepare($attemptCountQuery);
                        $stmt->bind_param("ii", $examId, $userId);
                        $stmt->execute();
                        $attemptCount = $stmt->get_result()->fetch_assoc()['count'];
                        $attemptNo = $attemptCount + 1;

                        // Create new attempt
                        $createAttempt = "INSERT INTO exam_attempt (exam_id, user_id, attempt_no, started_at, completed) VALUES (?, ?, ?, NOW(), 0)";
                        $stmt = $conn->prepare($createAttempt);
                        $stmt->bind_param("iii", $examId, $userId, $attemptNo);
                        
                        if ($stmt->execute()) {
                            $attemptId = $conn->insert_id;
                            $message = "Exam started successfully! Redirecting to exam page...";
                            $messageType = "success";
                            // Redirect to exam taking page (to be implemented)
                            // header("Location: takeExam.php?attempt_id=" . $attemptId);
                            echo '<script>alert("Exam attempt created! (Exam taking interface to be implemented)"); window.location.href="../index.php";</script>';
                            exit;
                        } else {
                            $message = "Error starting exam: " . $conn->error;
                            $messageType = "error";
                        }
                    }
                } else {
                    $message = "You must register for this exam before attempting it.";
                    $messageType = "error";
                }
            } else {
                $message = "Incorrect quiz password. Please try again.";
                $messageType = "error";
            }
        } else {
            $message = "Selected exam not found.";
            $messageType = "error";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attempt Exam</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include ("../includes/header.php"); ?>
    
    <div class="container mt-xl mb-xl">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header">
                <h1 class="card-title"><i class="fas fa-pen-to-square"></i> Attempt Exam</h1>
                <p class="card-subtitle">Enter your credentials to start the exam</p>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType === 'error' ? 'error' : ($messageType === 'warning' ? 'warning' : 'success'); ?> mb-lg">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="exam" class="form-label">Select Exam</label>
                        <?php
                            // Show only registered exams
                            $exams = "SELECT e.id, e.code, e.name, e.scheduled_at 
                                     FROM exam e
                                     INNER JOIN exam_registration r ON e.id = r.exam_id
                                     WHERE r.user_id = ? AND r.status = 'registered'
                                     ORDER BY e.scheduled_at DESC";
                            $stmt = $conn->prepare($exams);
                            $stmt->bind_param("i", $userId);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if($result && $result->num_rows > 0){
                        ?>
                        <select name="exam_id" id="exam" class="form-control" required>
                            <option value="" disabled selected>Select an exam you're registered for</option>
                            <?php
                                while ($row = $result->fetch_assoc()) {
                                    $examLabel = $row['code'] . ' - ' . $row['name'];
                                    if ($row['scheduled_at']) {
                                        $examLabel .= ' (Scheduled: ' . date('M d, Y H:i', strtotime($row['scheduled_at'])) . ')';
                                    }
                                    echo "<option value=\"" . htmlspecialchars($row['id']) . "\">" . htmlspecialchars($examLabel) . "</option>";
                                }
                            ?>
                        </select>
                        <?php
                            } else {
                                echo "<p class='text-error'>No registered exams found. Please register for an exam first.</p>";
                                echo "<a href='registerExam.php' class='btn btn-primary mt-lg' style='width: 100%;'><i class='fas fa-user-plus'></i> Register for Exam</a>";
                            }
                        ?>
                    </div>

                    <?php if($result && $result->num_rows > 0): ?>
                    <div class="form-group">
                        <label for="quiz-password" class="form-label">Quiz Password</label>
                        <input type="password" name="quiz_password" id="quiz-password" class="form-control" placeholder="Enter Quiz Password" required>
                        <small class="field-hint">Enter the password provided by your examiner</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-play"></i> Start Exam
                    </button>
                    <?php endif; ?>
                </form>

                <div class="mt-lg">
                    <a href="../index.php" class="btn btn-outline" style="width: 100%; justify-content: center;">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include ("../includes/footer.php"); ?>
    <script src="../scripts/script.js"></script>
</body>
</html>
