<?php
   include("../config/config.php");

   if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
    // echo '<script>alert("you do not have to do that here!!")</script>';
    header("Location: ../auth/login.php");
    exit();
}
    // Database content

   if($_SERVER["REQUEST_METHOD"] == "POST"){
         $fid = $_POST["feedback_id"];
         $cid = $_POST["cid"];
         $date = $_POST["date"];
         $Fdetails = $_POST["details"];


   global $conn;
   $message = "";

   $sql = "INSERT INTO feedback (Feedback_ID,C_ID,Date,F_Details)
           VALUES ('$fid','$cid','$date','$Fdetails')";

    if($conn->query($sql) === TRUE){
        // echo "New Exam added Sucessfully";
        echo '<script>alert("feedback added Sucessfully");</script>';
        echo '<script>window.location.href = "../index.php";</script>';

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
    <title>Add Feedback</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php
    include ("../includes/header.php");
?>
    <!-- Add Feedback content -->
    <div class="container mt-4 mb-4">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header">
                <h2 class="card-title text-center">Add Feedback</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="feedback_id" class="form-label">Feedback ID:</label>
                        <input type="text" id="feedback_id" name="feedback_id" class="form-control" required placeholder="Enter Feedback ID">
                    </div>

                    <div class="form-group">
                        <label for="cid" class="form-label">Employee ID:</label>
                        <input type="text" id="cid" name="cid" class="form-control" required placeholder="Enter Employee ID">
                    </div>

                    <div class="form-group">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" id="date" name="date" class="form-control" required value="<?php echo date('Y-m-d');?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="details" class="form-label">Details:</label>
                        <textarea id="details" name="details" rows="5" class="form-control" placeholder="Enter your feedback details here..." required></textarea>
                    </div>

                    <div class="text-center">
                        <input type="submit" name="submit" value="Add Feedback" class="btn btn-primary btn-lg" style="width: 100%;">
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
    include ("../includes/footer.php");
?>
</body>
</html>
