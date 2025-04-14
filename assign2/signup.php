<?php
require_once("settings.php");

$email = '';
$profile_name = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_name = $_POST['profile_name']; 
    $date_started = date('Y-m-d'); 

    // Check if the password and confirm password match
    if ($password !== $_POST['confirm_password']) 
	{
        $error = "Passwords do not match!";
    } elseif (preg_match('/\s/', $profile_name)) { // Check for spaces in profile name
        $error = "Profile name must not contain spaces!";
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $password)) { // Check for letters and numbers in password
        $error = "Password must contain only letters and numbers!";
    } else {
        $conn = new mysqli($host, $user, $pswd, $dbnm);
        
        if ($conn->connect_error) 
		{
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM friends WHERE friend_email = ?");
        if ($stmt === false) 
		{
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
		{
            $error = "Email is already registered!";
        } else {
            $stmt = $conn->prepare("INSERT INTO friends (friend_email, password, profile_name, date_started) VALUES (?, ?, ?, ?)"); 
            if ($stmt === false) 
			{
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ssss", $email, $password, $profile_name, $date_started); 
            $stmt->execute();

            // Check for success
            if ($stmt->affected_rows > 0) 
			{
                session_start(); 
                $_SESSION['email'] = $email; 
                $_SESSION['profile_name'] = $profile_name; 

                header("Location: friendlist.php");
                exit();
            } else {
                $error = "Error signing up!";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Sign Up</h1>
    <form method="POST" action="signup.php">
        <label>Email: </label><input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br>
        <label>Profile Name: </label><input type="text" name="profile_name" value="<?php echo htmlspecialchars($profile_name); ?>" required><br>
        <label>Password: </label><input type="password" name="password" required><br>
        <label>Confirm Password: </label><input type="password" name="confirm_password" required><br>
        

        <button type="submit">Sign Up</button>
        <button type="reset">Clear</button>
    </form>
    <?php if ($error) { echo "<p style='color:red;'>$error</p>"; } ?>
    <a href="index.php">Home</a>
</body>
</html>

