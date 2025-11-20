<?php

include('../config/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Staff'){
    header("Location: ../auth/login.php");
    exit();
}

// Delete Exam Operation
if(isset($_GET['deleteid'])){
    $examId = (int)$_GET['deleteid'];

    $stmt = $conn->prepare("DELETE FROM exam WHERE id = ?");
    $stmt->bind_param("i", $examId);
    $result = $stmt->execute();

    if($result){
        if ($_SESSION['role'] == 'Manager') {
            echo '<script>window.location.href = "../dashboards/manager_dashboard.php";</script>';
        }
        elseif ($_SESSION['role'] == 'Admin') {
            echo '<script>window.location.href = "../dashboards/admin_dashboard.php";</script>';
        }
        elseif  ($_SESSION['role'] == 'Examiner') {
            echo '<script>window.location.href = "../dashboards/examiner_dashboard.php";</script>';
        }
    }else{
        die($conn->error);
    }
}

?>
