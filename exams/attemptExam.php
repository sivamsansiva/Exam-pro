<?php
    require ("../config/config.php");

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
    <title>Attempt Exam</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php
        include ("../includes/header.php");
    ?>
    <div class="container mt-xl mb-xl">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header">
                <h1 class="card-title"><i class="fas fa-pen-to-square"></i> Attempt Exam</h1>
                <p class="card-subtitle">Enter your credentials to start the exam</p>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="exam" class="form-label">Select Exam</label>
                        <?php
                            $exams = "SELECT E_Name FROM exam";
                            $result = $conn->query($exams);
                            if($result && $result->num_rows > 0){
                        ?>
                        <select name="exam" id="exam" class="form-control" required>
                            <option value="" disabled selected>Select an exam</option>
                            <?php
                                while ($row = $result->fetch_assoc()) {
                                    $examName = $row['E_Name'];
                                    echo "<option value=\"" . htmlspecialchars($examName) . "\">" . htmlspecialchars($examName) . "</option>";
                                }
                            ?>
                        </select>
                        <?php
                            }
                            else {
                                echo "<p class='text-error'>No Exam found</p>";
                            }
                        ?>
                    </div>

                    <div class="form-group">
                        <label for="employee-id" class="form-label">Employee ID</label>
                        <input type="text" name="employee-id" id="employee-id" class="form-control" placeholder="Enter Employee ID" required>
                    </div>

                    <div class="form-group">
                        <label for="quiz-password" class="form-label">Quiz Password</label>
                        <input type="password" name="quiz-password" id="quiz-password" class="form-control" placeholder="Enter Quiz Password" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Attempt Exam</button>
                </form>
            </div>
        </div>
    </div>
    <?php
        include ("../includes/footer.php");
    ?>
    <script src="../scripts/script.js"></script>
</body>
</html>
