<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Post Job Vacancy</title>
	<link rel="stylesheet" href="style.css">

</head>
<body>
	<h1>Post Job Vacancy</h1>
	<form action="postjobprocess.php" method="post">
		<p>
			<label for="position_id">Position ID:</label>
			<input type="text" id="position_id" name="position_id" required pattern="ID[0-9]{3}" maxlength="5">
		</p>
		<p>
			<label for="title">Title:</label>
			<input type="text" id="title" name="title" required pattern="[a-zA-Z0-9\s,\.!]{1,10}">
		</p>
		<p>
			<label for="description">Description:</label>
			<textarea id="description" name="description" required maxlength="250"></textarea>
		</p>
		<p>
			<label for="closing_date">Closing Date:</label>
			<input type="text" id="closing_date" name="closing_date" required value="<?php echo date('d/m/y'); ?>">
		</p>
		<p>
			<label>Position:</label>
			<span class="radio-group">
				<input type="radio" id="full_time" name="position" value="Full Time">
				<label for="full_time">Full Time</label>
				<input type="radio" id="part_time" name="position" value="Part Time">
				<label for="part_time">Part Time</label>
			</span>
		</p>
		<p>
			<label>Contract:</label>
			<span class="radio-group">
				<input type="radio" id="on_going" name="contract" value="On-going">
				<label for="on_going">On-going</label>
				<input type="radio" id="fixed_term" name="contract" value="Fixed term">
				<label for="fixed_term">Fixed term</label>
			</span>
		</p>
		<p>
			<label>Location:</label>
			<span class="radio-group">
				<input type="radio" id="on_site" name="location" value="On site">
				<label for="on_site">On site</label>
				<input type="radio" id="remote" name="location" value="Remote">
				<label for="remote">Remote</label>
			</span>
		</p>
		<p>
			<label>Accept Application by:</label>
			 <span class="checkbox-group">
				<input type="checkbox" id="post" name="accept_by[]" value="Post">
				<label for="post">Post</label>
				<input type="checkbox" id="email" name="accept_by[]" value="Email">
				<label for="email">Email</label>
			</span>
		</p>
		<p>
			<input type="submit" value="Post Job Vacancy">
		</p>
	</form>
	<p><a href="index.php">Return to Home page</a></p>
</body>
</html>