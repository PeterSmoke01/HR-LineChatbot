<?php
date_default_timezone_set("Asia/Bangkok");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "line_bot";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
?>