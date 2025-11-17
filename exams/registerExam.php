<?php

    include('php/config.php');
    // session_start();
    
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
    <title>Exam Registration</title>
    <link rel="stylesheet" href="style/examStyle.css">
</head>
<body>

    <!-- Header -->
    <?php
        include ("php/header.php");
       
    ?>

    <!-- Exam registration form -->
    <div class="registration">

        <h1>Register Exam</h1>
        <form action="registerExam.php" method="post">
            
            <label for="emp_id">Employee ID:</label>
            <input type="text" name="employee-id" id="employee-id" placeholder="Enter Employee ID" required><br>
            
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" placeholder="Enter Email" required><br>

            <label for="exam">Select Exam:</label>
            <?php
                $sql = "SELECT E_Name FROM exam";
                $result = $conn->query($sql);
                if($result->num_rows > 0){
            ?>
            <select name="exam" id="exam" required>
                <option value="" disabled selected>Select an exam</option>

                <?php

                    global $conn;
                    while ($row = $result->fetch_assoc()) {
                        $examName = $row['E_Name'];
                        echo "<option>$examName</option>";
                    }
               
                ?>
            </select><br>

                <?php
                    }
                    else{
                    echo "No exam found.";
                    }
                ?>

            <input type="submit" value="Register Exam">
        </form>
        
    </div>

    <!-- Footer -->
    <?php
        include ("php/footer.php");
    ?>

    <script src="script/mainScript.js"></script>

</body>
</html>