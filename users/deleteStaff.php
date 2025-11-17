<?php

require ('php/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Employee'){
    header("Location: login.php");
    exit();
}

//Delete User Operation
if(isset($_GET['deleteid'])){
    $Sid = $_GET['deleteid'];

    $delete = "DELETE FROM staff WHERE S_ID='$Sid';";
    
    $result = $conn->query($delete);

    if($result){
        header('location:admin.php');
    }else{
        die($conn->error);
    }
}

?>