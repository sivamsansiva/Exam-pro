<?php
   include("php/config.php");

   if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'Employee'){
    // echo '<script>alert("you do not have to do that here!!")</script>';
    header("Location: login.php");
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
        echo '<script>window.location.href = "index.php";</script>';
      
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

    <link rel="stylesheet" href="style/contact.css">
    <title> Add feedback </title>
</head>
<body>
<?php
    include ("php/header.php");
?>
    <!-- Add Exam content -->
     
    <div class="add-exam">
        <h2> Add feedback </h2>

        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">

            <label for="feedback_id">Feedback ID:</label><br>
            <input type="text" id="feedback_id" name="feedback_id" required placeholder="Feedback ID"><br>

            <label for="cid">Employee ID :</label><br>
            <input type="text" id="cid" name="cid" required placeholder="Employee ID :"><br>

            <label for="date">Date : </label><br>
            <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d');?>" disabled><br>

            <label for="duration">Details:</label><br>
            <textarea id="details" name="details" rows="5" placeholder="Enter the feedback"required></textarea><br>

            <input type="submit" name="submit" value="Add Feedback" >
        </form>
    </div>
<?php
    include ("php/footer.php");
?>
</body>
</html>