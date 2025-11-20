<?php

require ('../config/config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] == 'Examiner' || $_SESSION['role'] == 'Staff'){
    header("Location: ../auth/login.php");
    exit();
}

//Delete User Operation
if(isset($_GET['deleteid'])){
    $userId = $_GET['deleteid'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'Admin'");
    $stmt->bind_param("i", $userId);
    $result = $stmt->execute();

    if($result && $stmt->affected_rows > 0){
        $stmt->close();
        header('location:../dashboards/admin_dashboard.php');
    }else{
        $stmt->close();
        die("Error: Unable to delete user or user is an admin.");
    }
}

?>
