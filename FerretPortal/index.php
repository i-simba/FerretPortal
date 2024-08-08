<?php include("functions.php") ?>

<html lang="en" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ferret Portal</title>
		<!-- CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.css">
		<link rel="stylesheet" href="assets/css/styles.css">
		<!-- ROBOTO -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    </head>

    <body>
		<div class="container">
			<h1 class="text-c">Ferret Portal</h1>
			<?php
				// Set local date-time
				date_default_timezone_set('America/Chicago');

				// Set DB connection
				$dblink = db_connect("ferret_portal");
			
				////////////////
				// NAVIGATION //
				////////////////
			
				include("pages/navigation.php");

				///////////////
				// MAIN BODY //
				///////////////
			
				// Page Variable
				if (!isset($_GET['page']))
					$page = "home";
				else
					$page = $_GET['page'];

				// Display Selected Page
				switch ($page) {
					case "home":
						include("pages/home.php");
						break;
					case "poop":
						include("pages/poop.php");
						break;
					case "sleep":
						include("pages/sleep.php");
						break;
					case "weight":
						include("pages/weight.php");
						break;
					default:
						include("pages/home.php");
						break;
				}
			?>
		</div>
    </body>
	<!-- SCRIPT -->
	 <script src="assets/js/modal-script.js"></script>
	 <script src="assets/js/date-script.js"></script>
</html>