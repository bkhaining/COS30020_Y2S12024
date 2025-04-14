<?php
require_once("settings.php");

session_start();

$error = ''; 
$email = ''; 


$conn = new mysqli($host, $user, $pswd, $dbnm);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $email = $_POST['email']; 
    $password = $_POST['password'];

    
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
        $user = $result->fetch_assoc();

        // Check if the entered password matches the stored password
        if ($password === $user['password']) 
		{ 
            $_SESSION['email'] = $user['friend_email'];
            $_SESSION['profile_name'] = $user['profile_name'];
            header("Location: friendlist.php"); 
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Email not registered!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Log In</h1>
    <form method="POST" action="login.php">
        <label>Email: </label>
        <input type="email" name="email" required value="<?php echo htmlspecialchars($email); ?>"><br> <!-- Retain the email input -->
        <label>Password: </label>
        <input type="password" name="password" required><br>
		
        <button type="submit">Log In</button>
		<button type="reset">Clear</button>
    </form>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <a href="index.php">Home</a>
</body>
</html>
