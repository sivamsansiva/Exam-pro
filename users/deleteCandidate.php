<?php

include('php/config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Employee' || $_SESSION['role'] == 'Examiner'){
    header("Location: login.php");
    exit();
}
//Delete User Operation
if(isset($_GET['deleteid'])){
    $cid = $_GET['deleteid'];

    $delete = "DELETE FROM exam_candidate WHERE C_ID='$cid';";
    
    $result = $conn->query($delete);

    if($result){
        if ($_SESSION['Role'] == 'Manager') {
            echo '<script>window.location.href = "manager.php";</script>';
        } 
        elseif ($_SESSION['Role'] == 'Admin') {
            echo '<script>window.location.href = "admin.php";</script>';
        }
    }
    else{
        die($conn->error);
    }
}

?>