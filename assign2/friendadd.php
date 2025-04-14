<?php
require_once("settings.php");
session_start();
if (!isset($_SESSION['email'])) 
{
    header("Location: login.php"); 
    exit();
}

$conn = new mysqli($host, $user, $pswd, $dbnm);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];
$profile_name = $_SESSION['profile_name'];

// Get the current user's ID
$user_id_query = "SELECT friend_id FROM friends WHERE friend_email = ?";
$stmt = $conn->prepare($user_id_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();
$user_row = $user_result->fetch_assoc();
$current_user_id = $user_row['friend_id'];

// Pagination setup
$limit = 10; // Number of friends to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Add friend functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $new_friend_id = $_POST['friend_id'];

    // Check if they are already friends
    $check_friend_query = "SELECT * FROM myfriends WHERE friend_id1 = ? AND friend_id2 = ?";
    $stmt = $conn->prepare($check_friend_query);
    $stmt->bind_param("ii", $current_user_id, $new_friend_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows === 0) 
	{
        // Add the new friend
        $add_friend_query = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES (?, ?)";
        $stmt = $conn->prepare($add_friend_query);
        $stmt->bind_param("ii", $current_user_id, $new_friend_id);

        if ($stmt->execute()) 
		{
            // Update num_of_friends for both users
            $update_friends_query = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id IN (?, ?)";
            $update_stmt = $conn->prepare($update_friends_query);
            $update_stmt->bind_param("ii", $current_user_id, $new_friend_id);
            $update_stmt->execute();

            echo "<p style='color:green;'>Friend added successfully!</p>";

            // Refresh the non-friends list
            $non_friends_query = "
                SELECT f.friend_id, f.profile_name, 
                    (SELECT COUNT(*) FROM myfriends AS mf 
                     WHERE mf.friend_id1 IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) 
                     AND mf.friend_id2 = f.friend_id) AS mutual_friends 
                FROM friends AS f 
                WHERE f.friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) 
                AND f.friend_id != ?
                LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($non_friends_query);
            $stmt->bind_param("iiiii", $current_user_id, $current_user_id, $current_user_id, $limit, $offset);
            $stmt->execute();
            $non_friends_result = $stmt->get_result();

            // Optionally display the updated non-friends list here
        } else {
            echo "<p style='color:red;'>Error adding friend: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>You are already friends with this user.</p>";
    }
}


// Fetch non-friends with pagination
$total_count_query = "SELECT COUNT(*) as total FROM friends WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) AND friend_id != ?";
$stmt = $conn->prepare($total_count_query);
$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_non_friends = $total_row['total'];
$total_pages = ceil($total_non_friends / $limit); // Calculate total pages

$non_friends_query = "
    SELECT f.friend_id, f.profile_name, 
        (SELECT COUNT(*) FROM myfriends AS mf 
         WHERE mf.friend_id1 IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) 
         AND mf.friend_id2 = f.friend_id) AS mutual_friends 
    FROM friends AS f 
    WHERE f.friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) 
    AND f.friend_id != ?
    LIMIT ? OFFSET ?";

$stmt = $conn->prepare($non_friends_query);
$stmt->bind_param("iiiii", $current_user_id, $current_user_id, $current_user_id, $limit, $offset);
$stmt->execute();
$non_friends_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Friends</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($profile_name); ?> - Add Friends</h1>
    <table>
        <thead>
            <tr>
                <th>Available Friends</th>
                <th>Mutual Friends</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $non_friends_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['profile_name']); ?></td>
                    <td><?php echo $row['mutual_friends']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="friend_id" value="<?php echo $row['friend_id']; ?>">
                            <button type="submit">Add as friend</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination controls -->
    <div>
        <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
    </div>

    <div>
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next</a>
        <?php endif; ?>
    </div>

    <a href="friendlist.php">Friend List</a> | <a href="logout.php">Log out</a>
</body>
</html>
