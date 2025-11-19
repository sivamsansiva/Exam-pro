<?php
// session_start();
include('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Employee'){
    header("Location: ../auth/login.php");
    exit();
}
global $conn;
$eid = $_GET['updateid'];
// Fetch the exam details

$sql1 = "SELECT * FROM exam WHERE E_ID= '$eid'";
$result = mysqli_query($conn, $sql1);
$row = $result->fetch_assoc();

$Ename = $row['E_Name'];
$Qpassword = $row['Q_password'];
$Duration = $row['Duration'];
$Sid=$row['S_ID'];

// Check if form is submitted
if (isset($_POST['submit'])) {
    // $eid = $_POST["eid"];
    $Ename = $_POST["ename"];
    $Qpassword = $_POST["Q_password"];
    $Duration = $_POST["Duration"];
    // $Sid = $_POST["Sid"];

    global $conn;
    // Update the exam information
    $sql2 = "UPDATE exam SET
        E_name='$Ename',
        Q_password='$Qpassword',
        Duration='$Duration'
        WHERE E_ID='$eid'";

    $result = mysqli_query($conn, $sql2);

    if ($result) {
        echo '<script>alert("Updated Successfully");</script>';

        // Redirect based on user role
        if ($_SESSION['Role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        } elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        }
    } else {
        die($conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <!-- <link rel="stylesheet" href="../styles/examStyle.css"> -->
    <style>
        * {
  box-sizing: border-box;
}
body {
  background: linear-gradient(90deg, #ffffff 0%, #EB8317 35%, #10375C 100%);
    font-family: Arial, Helvetica, sans-serif;
    /* display: flex; */
    justify-content: center;
    align-items: center;
    height: 100vh;
    transform: scale(1.05);
    /* margin: 0; */
  }

  form {
    background-color: #fff;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 20px auto;
    width: 80vw;
    max-width: 800px;

  }

  form:hover
  {
    transition:  0.3s;
    transform: scale(1.02);
    box-shadow: 0 10px 15px rgba(0,0,0,.2);
  }

  #submitbtn:hover{
    transform: scale(1.05);
    box-shadow: 0 10px 15px rgba(0,0,0,.2);
  }

  .Update input[type="text"],
  .Update input[type="email"],
  .Update input[type="tel"],
  .Update textarea,
  .Update input[type="date"],
  .Update input[type="password"],
  .Update select {
    display: block;
    width: 20 em;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
  }

  #form {
    display: flex;
    flex-direction: column;
    align-items: right;

  }
  #submitbtn{
      width: 200px;
      height:50px;
      border-radius: 25px;
      background-color: #0078D7;
      color:white;
      align-content:right;
      border:none;
      font-size: 3 rem;
  }
    </style>
    <title>Update exam</title>
</head>
<body>
    <?php
        include ('../includes/header.php')
    ?>
    <div class="Update" onsubmit="checkPassword()">
        <center> <h1>Update Exam</h1></center><br>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
            <label for="exam_id">Exam ID</label>
            <input type="text" name="eid" id="eid" placeholder="Enter Exam Id" value="<?php echo $eid; ?>" disabled>

            <label for="ename">Exam Name</label>
            <input type="text" name="ename" id="ename" placeholder="Enter Exam Name" value="<?php echo $Ename; ?>" required>

            <label for="Q_password">Quiz Password</label>
            <input type="text" name="Q_password" id="Q_password" placeholder="Enter Quiz Password" value="<?php echo $Qpassword; ?>" required>

            <label for="Duration">Duration</label>
            <input type="text" name="Duration" id="Duration" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" placeholder="00:00:00" value="<?php echo $Duration; ?>" required>

            <label for="Sid">Staff ID</label>
            <input type="text" name="Sid" id="Sid" placeholder="Enter Staff Id" value="<?php echo $Sid; ?>" disabled>

            <!-- <label for="exam_id">Confirm Password</label>
            <input type="text" name="exam-id" id="exam-id" placeholder="Confirm Quiz Password"> -->

            <center>
            <input type="submit" name="submit" id="submitbtn" value="Update"><br>
            </center>
        </form>
    </div>
<?php
    include ('../includes/footer.php');
?>
</body>
</html>
