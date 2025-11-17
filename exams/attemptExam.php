<?php
    require ("php/config.php");

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
    <link rel="stylesheet" href="style/examStyle.css">
</head>
<body>
    <?php
        include ("php/header.php");
    ?>
    <div class="attemptExam">
        <h1>Attempt Exam</h1>
        <form>
            <label for="exam">Select Exam:</label>
            <?php
                $exams = "SELECT E_Name FROM exam";
                $result = $conn->query($exams);
                if($result->num_rows > 0){
            ?>
            <select name="exam" id="exam">
                <option value="" disabled selected>Select an exam</option>
                <?php
                    while ($row = $result->fetch_assoc()) {
                        $examName = $row['E_Name'];
                        echo "<option>$examName</option>";
                    }
                ?>
            </select><br>
            <?php
                }
                else {
                    echo "No Exam found";
                }
            ?>

            <label for="emp_id">Employee ID:</label>
            <input type="text" name="employee-id" id="employee-id" placeholder="Enter Employee ID"><br>

            <label for="password">Quiz Password</label>
            <input type="password" name="quiz-password" id="quiz-password" placeholder="Enter Quiz Password" required><br>

            <input type="submit" value="Register Exam" onclick="errorMessage()">
        </form>
    </div>
    <?php
        include ("php/footer.php");
    ?>
    <script src="script/mainScript.js"></script>
</body>
</html>