<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Search Job Vacancy</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<h1>Search Job Vacancy</h1>
	<form action="searchjobprocess.php" method="get">
		<p>
			<label for="job_title">Job Title:</label>
			<input type="text" id="job_title" name="job_title">
		</p>
		<p>
			<label for="position">Position:</label>
			<select id="position" name="position">
				<option value="">Any</option>
				<option value="Part Time">Part Time</option>
				<option value="Full Time">Full Time</option>
			</select>
		</p>
		<p>
			<label for="contract">Contract:</label>
			<select id="contract" name="contract">
				<option value="">Any</option>
				<option value="Fixed term">Fixed term</option>
				<option value="Permanent">Permanent</option>
			</select>
		</p>
		<p>
			<label for="application_type">Application Type:</label>
			<select id="application_type" name="application_type">
				<option value="">Any</option>
				<option value="Post">Post</option>
				<option value="Email">Email</option>
			</select>
		</p>
		<p>
			<label for="location">Location:</label>
			<select id="location" name="location">
				<option value="">Any</option>
				<option value="Remote">Remote</option>
				<option value="On-site">On-site</option>
			</select>
		</p>
		<p>
			<input type="submit" value="Search">
		</p>
	</form>
	<p><a href="index.php">Return to Home page</a></p>
</body>
</html>