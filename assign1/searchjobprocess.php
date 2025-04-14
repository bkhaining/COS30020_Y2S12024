<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Job Vacancy</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<?php
$dir = '../../data/jobs';
$positions_file = $dir . '/' . 'positions.txt';

if (!file_exists($positions_file)) 
{
    echo "Error: File '$positions_file' not found.";
    exit;
}

if (!is_readable($positions_file)) 
{
    echo "Error: File '$positions_file' is not readable.";
    exit;
}

if (!isset($_GET['job_title']) || empty($_GET['job_title'])) 
{
    echo "Please enter a job title to search.";
    echo "<p><a href='index.php'>Return to Home page</a> | <a href='searchjobform.php'>Search Job Vacancy</a></p>";
    exit;
}

$job_title = $_GET['job_title'];
$position = isset($_GET['position']) ? $_GET['position'] : '';
$contract = isset($_GET['contract']) ? $_GET['contract'] : '';
$application_type = isset($_GET['application_type']) ? $_GET['application_type'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';

$positions = file($positions_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$matches = array();


foreach ($positions as $pos) 
{
    $fields = explode("\t", $pos);
    if (stripos($fields[1], $job_title) !== false) 
	{
        $matches[] = $fields;
    }
}

$filtered_matches = array();
$today = new DateTime();



foreach ($matches as $match) 
{
    $closing_date = DateTime::createFromFormat('d/m/y', $match[3]);
    
    if ($closing_date >= $today) 
	{
		
        if (($position == "" || $match[4] == $position) &&
            ($contract == "" || $match[5] == $contract) &&
            ($application_type == "" || (stripos($match[7], $application_type) !== false || stripos($match[8], $application_type) !== false)) &&
            ($location == "" || $match[6] == $location)) 
		{
            $filtered_matches[] = $match;
        }
    }
}


if (!empty($filtered_matches)) 
{
    echo "<p>Job vacancies found with the title '$job_title':</p>";
    echo "<ol>";
    foreach ($filtered_matches as $match) 
	{
        echo "<li>";
        echo "<p>Position ID: $match[0]<br>";
        echo "Title: $match[1]<br>";
        echo "Description: $match[2]<br>";
        echo "Closing Date: $match[3]<br>";
        echo "Position: $match[4]<br>";
        echo "Contract: $match[5]<br>";
        echo "Location: $match[6]<br>";
        echo "Accept Application by: $match[7] &nbsp; $match[8]<br>";
        echo "</p>";
        echo "</li>";
    }
    echo "</ol>";
} else {
    echo "<p>No job vacancies found with the title '$job_title'.</p>";
}
echo "<p><a href='index.php'>Return to Home page</a> | <a href='searchjobform.php'>Search Job Vacancy</a></p>";

?>

</body>
</html>



