<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job Vacancy</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<?php
if (!isset($_POST['position_id']) || !isset($_POST['title']) || !isset($_POST['description']) || !isset($_POST['closing_date']) || !isset($_POST['position']) || !isset($_POST['contract']) || !isset($_POST['location']) || !isset($_POST['accept_by'])) 
{
    $error = "Please fill in all fields.";
} elseif (!preg_match('/ID[0-9]{3}/', $_POST['position_id'])) {
    $error = "Invalid Position ID. It must start with 'ID' followed by 3 digits.";
} elseif (!preg_match('/[a-zA-Z0-9\s,\.!]{1,10}/', $_POST['title'])) {
    $error = "Invalid Title. It can only contain alphanumeric characters, spaces, comma, period, and exclamation point.";
} elseif (strlen($_POST['description']) > 250) {
    $error = "Description is too long. It can only contain up to 250 characters.";
} elseif (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{2}$/', $_POST['closing_date'])) {
    $error = "Invalid Closing Date. It must be in the format dd/mm/yy.";
} else {

    $dir = '../../data/jobs';
    if (!is_dir($dir)) 
	{
        umask(0007);
        if (!mkdir($dir, 02770)) 
		{
            echo "Failed to create directory: $dir";
            exit;
        }
    }
    
    $positions_file = $dir . '/' . 'positions.txt';
    if (file_exists($positions_file)) 
	{
        $positions = file($positions_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($positions as $position) 
		{
            $fields = explode("\t", $position);
            if ($fields[0] == $_POST['position_id']) 
			{
                $error = "Position ID already exists.";
                break;
            }
        }
    }

    if (!isset($error)) 
	{
        //$accept_by_post = isset($_POST['accept_by'][0]) && $_POST['accept_by'][0] == 'Post' ? 'Post' : '';
        //$accept_by_email = isset($_POST['accept_by'][1]) && $_POST['accept_by'][1] == 'Email' ? 'Email' : '';
		$accept_by = isset($_POST['accept_by']) ? $_POST['accept_by'] : [];
        $accept_by_post = in_array('Post', $accept_by) ? 'Post' : '';
        $accept_by_email = in_array('Email', $accept_by) ? 'Email' : '';
        $record = $_POST['position_id'] . "\t" . $_POST['title'] . "\t" . $_POST['description'] . "\t" . $_POST['closing_date'] . "\t" . $_POST['position'] . "\t" . $_POST['contract'] . "\t" . $_POST['location'] . "\t" . $accept_by_post . "\t" . $accept_by_email . "\n";
        $handle = fopen($positions_file, "a");
        if ($handle) 
		{  
		
            fwrite($handle, $record);
            fclose($handle);
            $message = "Job vacancy saved successfully.";
        } else {
            $error = "Cannot save job vacancy to file.";
        }
    }
}

if (isset($error)) 
{
    echo "<p>$error</p>";
    echo "<p><a href='index.php'>Return to Home page</a> | <a href='postjobform.php'>Post Job Vacancy</a></p>";
} else {
    echo "<p>$message</p>";
    echo "<p><a href='index.php'>Return to Home page</a></p>";
}
?>

</body>
</html>
