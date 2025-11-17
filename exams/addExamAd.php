<?php
   include("php/config.php");

   if (session_status() == PHP_SESSION_NONE) {
    session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Admin'){
        header("Location: login.php");
        exit();
    }
    // Database content 

   if($_SERVER["REQUEST_METHOD"] == "POST"){
        $exam_id = $_POST["exam_id"];
        $ename = $_POST["ename"];
        $qpassword = $_POST["qpassword"];
        $Duration = $_POST["duration"];
        $sid = $_POST["sid"];
        global $conn;
        $message = "";

        $sql = "INSERT INTO exam (E_ID,E_Name,Q_password,Duration,S_ID)
                VALUES ('$exam_id','$ename','$qpassword','$Duration','$sid')";
        if($conn->query($sql) === TRUE){
            // echo "New Exam added Sucessfully";
            echo '<script>alert("New Exam added Sucessfully");</script>';
            if ($_SESSION['Role'] == 'Examiner') {
                echo '<script>window.location.href = "examiner.php";</script>';
            }
            elseif ($_SESSION['Role'] == 'Manager') {
                echo '<script>window.location.href = "manager.php";</script>';
            } 
            elseif ($_SESSION['Role'] == 'Admin') {
                echo '<script>window.location.href = "admin.php";</script>';
            }
        }
        else{
            echo "Connection Error".$sql ."<br>".$conn->error;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/addExamAd.css">
    <title> Add Exam </title>
</head>
<body>
    <!-- Add Exam content -->
    <div class="add-exam">
        <h2> Add Exam </h2>

        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">

            <label for="ename">Exam ID:</label><br>
            <input type="text" id="eid" name="exam_id" required placeholder="Exam ID"><br>

            <label for="ename">Exam Name:</label><br>
            <input type="text" id="ename" name="ename" required placeholder="Exam Name"><br>

            <label for="qpassword">Quiz Password:</label><br>
            <input type="text" id="ename" name="qpassword" required
            placeholder="Quiz Password"><br>

            <label for="duration">Exam Duration:</label><br>
            <input type="text" name="duration" id="duration" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" required><br>

            <label for="">Staff ID:</label><br>
            <?php

                // Select exams from database

                $sql= "SELECT S_ID from staff";
                $result = $conn->query($sql);
                if($result->num_rows > 0){

            ?>
                <select name="sid" id="sid" required>
                <option value="" disabled selected>Select Staff ID</option>

            <?php
                while ($row = $result->fetch_assoc()) {
                    $sid = $row['S_ID'];
                    echo "<option>$sid</option>";
                }
            ?>
            </select><br>

            <?php
                }
                else{
                echo "No Staff ID Found.";
                }

            ?>

            <input type="submit" name="submit" value="Add Exam" >
        </form>
    </div>

</body>
</html>