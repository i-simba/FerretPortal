<?php
	include("assets/creds.php");
	function db_connect( $db ) {
		$dbusername = get_name(); // DB Credentials not included
		$dbpassword = get_pass(); // DB Credentials not included
		$host = get_host();       // DB Credentials not included
		$dblink = new mysqli($host, $dbusername, $dbpassword, $db);
		return $dblink;
	}

	function redirect( $uri ) {
		?>
			<script type="text/javascript">
				document.location.href="<?php echo $uri; ?>";
			</script>
		<?php die;
	}

	// HOME - DATA DISPLAY
	function getColor ( $color ) {
		switch ( $color ) {
			case 'r':
				return '<td class="w-33 bg-danger"> </td>';
			case 'y':
				return '<td class="w-33 bg-warning"> </td>';
			case 'b':
				return '<td class="w-33 bg-primary"> </td>';
			case 'g':
				return '<td class="w-33 bg-success"> </td>';
			default:
				return '<td class="w-33 bg-secondary"> </td>';
		}
	}

	// HOME - GET DATE ID or LOG ID
	function getID ( $dblink, $table, $column, $cond ) {

		// Queries
		$sql = "SELECT `id` FROM `$table` WHERE `$column` = '$cond';";
		$sql_in = "INSERT INTO `$table` (`$column`) VALUES ('$cond');";
		$sql_id = "SELECT `id` FROM `$table` WHERE `id` = (SELECT max(`id`) FROM `$table`);";

		$result = $dblink->query( $sql ) or
			die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
		
		if ( mysqli_num_rows( $result ) > 0 ) {
			$data = $result->fetch_array( MYSQLI_ASSOC );
			return $data['id'];
		} else {
			$dblink->query( $sql_in ) or
				die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
			$result = $dblink->query( $sql_id ) or
				die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

			$data = $result->fetch_array( MYSQLI_ASSOC );

			if ( $data != NULL )
				return $data['id'];
		}
	}
?>