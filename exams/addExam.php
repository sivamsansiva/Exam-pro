<?php
   include("../config/config.php");

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Employee'){
        header("Location: ../auth/login.php");
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

        if ($_SESSION['Role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        }
        elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        }
        elseif  ($_SESSION['Role'] == 'Examiner') {
            echo '<script>window.location.href = "../dashboards/examiner_dashboard.php";</script>';
        }
    }
    else{
        echo "Error".$sql ."<br>" . $conn->error;
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Exam</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <?php
        include ("../includes/header.php");
    ?>

    <!-- Add Exam Content -->
    <div class="container mt-xl mb-xl">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header">
                <h1 class="card-title"><i class="fas fa-plus-circle"></i> Add Exam</h1>
                <p class="card-subtitle">Create a new examination</p>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                    <div class="form-group">
                        <label for="eid" class="form-label">Exam ID</label>
                        <input type="text" id="eid" name="exam_id" class="form-control" required placeholder="Enter Exam ID">
                    </div>

                    <div class="form-group">
                        <label for="ename" class="form-label">Exam Name</label>
                        <input type="text" id="ename" name="ename" class="form-control" required placeholder="Enter Exam Name">
                    </div>

                    <div class="form-group">
                        <label for="qpassword" class="form-label">Quiz Password</label>
                        <input type="text" id="qpassword" name="qpassword" class="form-control" required placeholder="Enter Quiz Password">
                    </div>

                    <div class="form-group">
                        <label for="duration" class="form-label">Exam Duration (HH:MM:SS)</label>
                        <input type="text" name="duration" id="duration" class="form-control" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" required>
                    </div>

                    <div class="form-group">
                        <label for="sid" class="form-label">Staff ID</label>
                        <?php
                            // Select exams from database
                            $sql= "SELECT S_ID from staff";
                            $result = $conn->query($sql);
                            if($result->num_rows > 0){
                        ?>
                            <select name="sid" id="sid" class="form-control" required>
                            <option value="" disabled selected>Select Staff ID</option>

                        <?php
                            while ($row = $result->fetch_assoc()) {
                                $sid = $row['S_ID'];
                                echo "<option value=\"" . htmlspecialchars($sid) . "\">" . htmlspecialchars($sid) . "</option>";
                            }
                        ?>
                        </select>
                        <?php
                            }
                            else{
                            echo "<p class='text-error'>No Staff ID Found.</p>";
                            }
                        ?>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary" style="width: 100%;">Add Exam</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
        include ("../includes/footer.php");
    ?>

</body>
</html>
