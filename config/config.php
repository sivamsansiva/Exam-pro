<?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'exam_system';
    $port = '3308';

    $conn = new mysqli($servername, $username, $password, $database, $port);

    if ($conn->connect_error) {
        die ("Connection Failed".$conn->connect_error);
    }
    else {
       echo '<script>console.log( "connected" )</script>';
    }
?>
