<?php

include('../config/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Employee'){
    header("Location: ../auth/login.php");
    exit();
}
//Delete User Operation
if(isset($_GET['deleteid'])){
    $eid = $_GET['deleteid'];

    $delete = "DELETE FROM exam WHERE E_ID='$eid';";

    $result = $conn->query($delete);

    if($result){
        if ($_SESSION['Role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        }
        elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        }
        elseif  ($_SESSION['Role'] == 'Examiner') {
            echo '<script>window.location.href = "../dashboards/examiner_dashboard.php";</script>';
        }
    }else{
        die($conn->error);
    }
}

?>
