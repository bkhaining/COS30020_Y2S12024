<?php
require_once("settings.php");

session_start();
if (!isset($_SESSION['email'])) 
{
    header("Location: login.php"); 
    exit();
}

$conn = new mysqli($host, $user, $pswd, $dbnm);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];
$profile_name = $_SESSION['profile_name'];

// Retrieve the current user's ID
$user_id_query = "SELECT friend_id FROM friends WHERE friend_email = ?";
$stmt = $conn->prepare($user_id_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user_row = $user_result->fetch_assoc();
$current_user_id = $user_row['friend_id'];

// Retrieve friend list
$query = "
    SELECT f2.profile_name, f2.friend_id
    FROM friends f1
    JOIN myfriends mf ON f1.friend_id = mf.friend_id1
    JOIN friends f2 ON mf.friend_id2 = f2.friend_id
    WHERE f1.friend_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total number of friends
$total_friends = $result->num_rows;

// Unfriend functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $unfriend_id = $_POST['unfriend_id'];

    // Remove the friendship
    $unfriend_query = "DELETE FROM myfriends WHERE friend_id1 = ? AND friend_id2 = ?";
    $stmt = $conn->prepare($unfriend_query);
    $stmt->bind_param("ii", $current_user_id, $unfriend_id);
    $stmt->execute();

    // Update num_of_friends for the current user
    $update_friends_query = "UPDATE friends SET num_of_friends = num_of_friends - 1 WHERE friend_id = ?";
    $update_stmt = $conn->prepare($update_friends_query);
    $update_stmt->bind_param("i", $current_user_id);
    $update_stmt->execute();

    // Update num_of_friends for the unfriended user
    $update_friends_query = "UPDATE friends SET num_of_friends = num_of_friends - 1 WHERE friend_id = ?";
    $update_stmt = $conn->prepare($update_friends_query);
    $update_stmt->bind_param("i", $unfriend_id);
    $update_stmt->execute();

    header("Location: friendlist.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Friend List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($profile_name); ?>'s Friend List</h1>
    <table>
        <thead>
            <tr>
                <th>Friend Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['profile_name']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="unfriend_id" value="<?php echo $row['friend_id']; ?>">
                            <button type="submit">Unfriend</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <p>Total Friends: <?php echo $total_friends; ?></p>
    <a href="friendadd.php">Add Friends</a> | <a href="logout.php">Log out</a>
</body>
</html>
