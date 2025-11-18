<?php
    require ("../config/config.php");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attempt Exam</title>
    <link rel="stylesheet" href="../styles/examStyle.css">
</head>
<body>
    <?php
        include ("../includes/header.php");
    ?>
    <div class="attemptExam">
        <h1>Attempt Exam</h1>
        <form method="post" action="">
            <label for="exam">Select Exam:</label>
            <?php
                $exams = "SELECT E_Name FROM exam";
                $result = $conn->query($exams);
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
                else {
                    echo "<p style='color: #721c24;'>No Exam found</p>";
                }
            ?>

            <label for="employee-id">Employee ID:</label>
            <input type="text" name="employee-id" id="employee-id" placeholder="Enter Employee ID" required><br>

            <label for="quiz-password">Quiz Password</label>
            <input type="password" name="quiz-password" id="quiz-password" placeholder="Enter Quiz Password" required><br>

            <input type="submit" value="Attempt Exam">
        </form>
    </div>
    <?php
        include ("../includes/footer.php");
    ?>
    <script src="../scripts/mainScript.js"></script>
</body>
</html>
