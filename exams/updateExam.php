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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Exam</title>
    <link rel="stylesheet" href="../styles/theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php
        include ('../includes/header.php')
    ?>
    <div class="container mt-xl mb-xl">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="card-header">
                <h1 class="card-title"><i class="fas fa-edit"></i> Update Exam</h1>
                <p class="card-subtitle">Modify exam details</p>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?updateid=' . htmlspecialchars($eid); ?>" method="post">
                    <div class="form-group">
                        <label for="eid" class="form-label">Exam ID</label>
                        <input type="text" id="eid" name="eid" class="form-control" value="<?php echo htmlspecialchars($eid); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="ename" class="form-label">Exam Name</label>
                        <input type="text" id="ename" name="ename" class="form-control" value="<?php echo htmlspecialchars($Ename); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Q_password" class="form-label">Quiz Password</label>
                        <input type="text" id="Q_password" name="Q_password" class="form-control" value="<?php echo htmlspecialchars($Qpassword); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Duration" class="form-label">Duration (HH:MM:SS)</label>
                        <input type="text" id="Duration" name="Duration" class="form-control" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" value="<?php echo htmlspecialchars($Duration); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="Sid" class="form-label">Staff ID</label>
                        <input type="text" id="Sid" name="Sid" class="form-control" value="<?php echo htmlspecialchars($Sid); ?>" disabled>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary" style="width: 100%;">Update Exam</button>
                </form>
            </div>
        </div>
    </div>
<?php
    include ('../includes/footer.php');
?>
</body>
</html>
