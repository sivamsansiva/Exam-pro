<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'exam_system';
$port = '3308';

$conn = new mysqli($servername, $username, $password, $database, $port);
$conn->set_charset('utf8mb4');
?>
