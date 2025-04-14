<?php
require_once("settings.php");
session_start();

$conn = new mysqli($host, $user, $pswd, $dbnm);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

// Create tables
$friends_table = "CREATE TABLE IF NOT EXISTS friends (
    friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    friend_email VARCHAR(50) NOT NULL,
    password VARCHAR(20) NOT NULL,
    profile_name VARCHAR(30) NOT NULL,
    date_started DATE NOT NULL,
    num_of_friends INT UNSIGNED DEFAULT 0
)";

$myfriends_table = "CREATE TABLE IF NOT EXISTS myfriends (
    friend_id1 INT NOT NULL,
    friend_id2 INT NOT NULL
)";


$tables_created = false;
if ($conn->query($friends_table) === TRUE && $conn->query($myfriends_table) === TRUE) 
{
    $tables_created = true;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Friend System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>My Friend System</h1>
    <p><b>Name:</b> Christine Khai Ning BONG</p>
    <p><b>Student ID:</b> 102787457</p>
    <p><b>Email:</b> 102787457@student.swin.edu.au</p>
    <p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student's work or from any other source.</p>

    <?php if ($tables_created): ?>
        <p>Tables successfully created and populated.</p>
    <?php endif; ?>

    <a href="signup.php">Sign-Up</a> | <a href="login.php">Log-In</a> | <a href="about.php">About</a>
</body>
</html>
