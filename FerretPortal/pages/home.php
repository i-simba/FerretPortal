<?php
	/* TODO:
	 * ✓ Modal pop up for success instead of alert
	 * ✓ ID right now contains the log ID for each poop/sleep log - find another way to store log ID to be pulled
	 * ✓ Modal alerts error
	 * - Modal refactor (?)
	 * - IF POOP SUBMIT -> Check db for time & date & fert match
	 * - Alert user if ^ exists alter?
	 * - Running total for sleep
     * - Edit log functionality : Poop
	 * - Delete Log functionality : Poop
	 * ✓ Edit log functionality : Sleep
	 * ✓ Delete Log functionality : Sleep
     */

	// Current Time
	$time = date("H:i");

	// Current Date
	$date = date("Y-m-d");

	// Entry Container
    echo '<div class="body-c-r">';

		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//                                                                   IF POOP IS SUBMITTED                                                                  //
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		if ( isset( $_POST['pSubmit'] ) ) {
			
			// Var Assign
			$errors = "";
			$fert = $_POST['pName'];
			$pDate = $_POST['pDate'];
			$pTime = $_POST['pTime'];
			$pColor = $_POST['pColor'];
			
			// Null Check
			if ( $fert == NULL ) {
				$errors = "noFert";
			}
			if ( $pDate == NULL ) {
				$errors .= "noDate";
			}
			if ( $pTime == NULL ) {
				$errors .= "noTime";
			}
			if ( $pColor == NULL ) {
				$errors .= "noCat";
			}
			
			// Condition based on DB value - Ferret
			if ( $fert == 'tonka' )
				$fid = 1;
			else if ( $fert == 'koda' )
				$fid = 2;
			else
				$errors .= "FertCon";
			
			// Redirect if there is an error
			if ( $errors != NULL )
				header("Location: index.php?errMsg=$errors");
			
			// No errors
			else {
				
				// Check if date exists in DB
				$did = getID( $dblink, 'date', 'date', $pDate );

				// Check if time exists in DB
				$check_time = "SELECT `id` FROM `poop_log` WHERE `date_id` = '$did' AND `time` = '$pTime';";
				$result = $dblink->query( $check_time ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

				// Update log if it exists, create new log if it doesn't
				if ( mysqli_num_rows( $result ) > 0 ) {
					$data = $result->fetch_array( MYSQLI_ASSOC );
					$lid = $data['id'];
					
					if ( $fid == 1 )
						$sql_poop_up = "UPDATE `poop_log` SET `tonka` = '$pColor' WHERE `poop_log`.`id` = $lid;";
					else if ( $fid == 2 )
						$sql_poop_up = "UPDATE `poop_log` SET `koda` = '$pColor' WHERE `poop_log`.`id` = $lid;";

					$dblink->query( $sql_poop_up ) or
						die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
				} else {
					if ( $fid == 1 )
						$sql_time_in = "INSERT INTO `poop_log` (`date_id`, `time`, `tonka`, `koda`) VALUES ('$did', '$pTime', '$pColor', 'z');";
					else if ( $fid == 2 )
						$sql_time_in = "INSERT INTO `poop_log` (`date_id`, `time`, `tonka`, `koda`) VALUES ('$did', '$pTime', 'z', '$pColor');";

					$dblink->query( $sql_time_in ) or
						die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
				}

				// Success redirect (Maybe better indication for successful entry)
				// header("Location: index.php?pEntry=success");

				// Redirect
				header("Location: index.php?alert=successPoopAdd");
			}
		}


		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//                                                                  IF SLEEP IS SUBMITTED                                                                  //
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		if (isset( $_POST['sSubmit'] ) ) {

			// Var Assign	$errors = "";
			$fert = $_POST['sName'];
			$sDate = $_POST['sDate'];
			$sTime = $_POST['sTime'];
			$status = $_POST['status'];

			// Null Check
			if ( $fert == NULL ) {
				$errors .= "noSFert";
			}
			if ( $sDate == NULL ) {
				$errors .= "noSDate";
			}
			if ( $sTime == NULL ) {
				$errors .= "noSTime";
			}
			if ( $status == NULL ) {
				$errors .= "noSStat";
			}

			// Redirect if there is an error
			if ( $errors != NULL )
				header("Location: index.php?errMsg=$errors");

			// No errors from form
			else {

				// Logging errors
				$logError = false;
				
				// Check if date exists in DB
				$did = getID( $dblink, 'date', 'date', $sDate );
				
				// Check if log exists in DB
				$lid = getID( $dblink, 'sleep_log', 'date_id', $did );
				
				// Get corresponding time_logs if any exists
				$sql_time_logs = "SELECT * FROM `time_log` WHERE `log_id` = '$lid' AND `status` = 'open' AND `fert` = '$fert';";
				$result_time_logs = $dblink->query( $sql_time_logs ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
				$time_logs = $result_time_logs->fetch_array( MYSQLI_ASSOC );
				
				$tid = "";
				$fid = "";
				if ( $time_logs != NULL ) {
					$tid = $time_logs['id'];
					$fid = $time_logs['fert'];
				}
				
				//////////
				// WAKE //
				//////////

				if ( $status === 'wake' ) {
					if ( $tid != NULL ) {
						header("Location: index.php?alert=errorLogOpen");
						$logError = true;
					} else {
						$sql_active_in = "INSERT INTO `time_log` (`log_id`, `fert`, `wake`, `status`) VALUES ('$lid', '$fert', '$sTime', 'open');";
						$dblink->query( $sql_active_in ) or
							die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
					}
				}

				///////////
				// SLEEP //
				///////////

				else if ( $status == 'sleep' ) {
					if ( $tid == NULL ) {

						// Check previous day for open logs
						$sql_check_last = "SELECT * FROM `time_log` WHERE `log_id` = $lid-1 AND `status` = 'open' AND `fert` = '$fert';";
						$result_check_last = $dblink->query( $sql_check_last ) or
							die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
						$last_log = $result_check_last->fetch_array( MYSQLI_ASSOC );

						if ( $last_log != NULL ) {

							$pid = $last_log['id'];

							// Close previous day's log at 24:00
							$sql_update_last = "UPDATE `time_log` SET `sleep` = '24:00:00', `status` = 'closed' WHERE `id` = $pid;";
							$dblink->query( $sql_update_last ) or
								die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

							// Open today's log at 00:00 and close at sTime
							$sql_create_new = "INSERT INTO `time_log` (`log_id`, `fert`, `wake`, `sleep`, `status`) VALUES ('$lid', '$fert', '00:00:00', '$sTime', 'closed');";
							if ( $sTime != '00:00' ) {
								$dblink->query( $sql_create_new ) or
									die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
								echo $sTime;
							}

						} else {
							header("Location: index.php?alert=errorLogClosed");
							$logError = true;
						}
						
					} else {
						$sql_active_out = "UPDATE `time_log` SET `sleep` = '$sTime', `status` = 'closed' WHERE `id` = '$tid';";
						$dblink->query( $sql_active_out ) or
							die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
					}
				}

				// Redirect
				if ( !$logError ) {
					if ( $status === 'sleep' )
						header("Location: index.php?alert=successSleepAdd");
					if ( $status === 'wake' )
						header("Location: index.php?alert=successWakeAdd");
				}
			}
		}


		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//                                                                   IF NOTE IS SUBMITTED                                                                  //
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		if (isset( $_POST['noteButton'] ) ) {

			// Var Assign
			$errors = "";
			$note = $_POST['noteText'];
			$name = $_POST['noteName'];

			// Null Check
			if ( $note == null) {
				$errors .= 'noNote';
			}
			if ( $name == null) {
				$errors .= 'noName';
			}

			// Redirect if there is an error
			if ( $errors != NULL ) {
				header("Location: index.php?errMsg=$errors");
			}

			// No errors
			else {

				$did = getID( $dblink, 'date', 'date',$date );
				$sql_add_note = "INSERT INTO `notes` (`date_id`, `user`, `note`) VALUES ('$did', '$name', '$note');";
				$dblink->query( $sql_add_note ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
			}
			header("Location: index.php?alert=successNoteAdd");
		}


		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//                                                                  SLEEP MODAL - BACK END                                                                 //
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		///////////////////
		// Delete Button //
		///////////////////

		if ( isset( $_POST['sleepDeleteBtn'] ) ) {

			// Var Assign
			$errors = "";
			$lid = $_POST['sleepDelID'];

			// Null Check
			if ( $lid == NULL) {
				$errors .= 'noID-SM';
			}

			// Redirect if there is an error
			if ( $errors != NULL ) {
				header("Location: index.php?errMsg=$errors");
			}

			// No errors
			else {

				$sql_del_sleep = "DELETE FROM `time_log` WHERE `id` = $lid;";
				$dblink->query( $sql_del_sleep ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

				echo '<script>alert("DELETE FROM `time_log` WHERE `id` = '.$lid.';");</script>';
				//echo '<script>alert("Sleep Log Successfully Deleted!<br>ID : <b>'.$lid.'</b>");</script>';
			
				// Redirect
				header("Location: index.php?alert=successSleepDel");
			}
		}

		/////////////////
		// Edit Button //
		/////////////////

		if ( isset( $_POST['sleepEditBtn'] ) ) {
			
			// Var Assign
			$errors = "";
			$lid = $_POST['sleepDelID'];
			$edit_wake = $_POST['sleepWake'];
			$edit_sleep = $_POST['sleepSleep'];
			$no_sleep = false;

			// Null Check
			if ( $lid == NULL) {
				$errors .= 'noID-SM';
			}
			if ( $edit_wake == NULL ) {
				$errors .= 'noWake';
			}
			if ( $edit_sleep == NULL ) {
				$no_sleep = true;
			}

			// Redirect if there is an error
			if ( $errors != NULL ) {
				header("Location: index.php?errMsg=$errors");
			}

			// No errors
			else {
				if ( $no_sleep == true ) {
					$sql_update_sleep = "UPDATE `time_log` SET `wake` = '$edit_wake' WHERE `id` = $lid;";
				} else {
					$sql_update_sleep = "UPDATE `time_log` SET `wake` = '$edit_wake', `sleep` = '$edit_sleep' WHERE `id` = $lid;";
				}
				$dblink->query( $sql_update_sleep ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

				$sql_check_null = "SELECT `wake`, `sleep` FROM `time_log` WHERE `id` = 1;";
				$result_check_null = $dblink->query( $sql_check_null ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

				$isNull = false;
				while ( $row = mysqli_fetch_array( $result_check_null ) ) {
					if ( $row == null ) $isNull = true;
				}

				if ( $isNull ) {
					$sql_add_stat = "UPDATE `time_log` SET `status` = 'open' WHERE `id` = $lid;";
				} else {
					$sql_add_stat = "UPDATE `time_log` SET `status` = 'closed' WHERE `id` = $lid;";
				}

				$dblink->query( $sql_add_stat ) or
					die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
				
				// Redirect
				header("Location: index.php?alert=successSleepEdit");
			}
		}


		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//                                                                   PAGE CONTENT LAYOUT                                                                   //
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		// Left Container
		echo '<div class="container p-3">';

			// Poop Input
			echo '<form class="border rounded p-4 shadow" id="poopForm" method="post" action="">';
				
				// Name
				echo '<h5 class="text-c mb-4">Log Poop</h5>';
				echo '<div class="d-flex justify-content-evently w-100 mb-2" role="group">';
					echo '<input type="radio" class="btn-check shadow" value="tonka" name="pName" id="ptonka" autocomplete="off">';
					if (isset($_GET['errMsg']) && strstr($_GET['errMsg'], "noFert"))
						echo '<label id="ltonka" class="btn btn-outline-danger w-50 text-white" for="ptonka">TONKA</label>';
					else
						echo '<label id="ltonka" class="btn btn-outline-secondary w-50 text-white" for="ptonka">TONKA</label>';
					
					echo '<input type="radio" class="btn-check shadow" value="koda" name="pName" id="pkoda" autocomplete="off">';
					if (isset($_GET['errMsg']) && strstr($_GET['errMsg'], "noFert"))
						echo '<label id="lkoda" class="btn btn-outline-danger w-50 ms-2 text-white" for="pkoda">KODA</label>';
					else
						echo '<label id="lkoda" class="btn btn-outline-secondary w-50 ms-2 text-white" for="pkoda">KODA</label>';
				echo '</div>';

				// Date & Time
				echo '<div class="d-flex justify-content-evenly w-100">';
					echo '<input class="form-control mb-2 shadow" type="date" id="pDate" name="pDate" required>';
					echo '<input class="form-control mb-2 ms-2 shadow" type="time" id="pTime" name="pTime" required>';
				echo '</div>';

				// Category
				if (isset($_GET['errMsg']) && strstr($_GET['errMsg'], "noCat")) {
					echo '<div class="d-flex justify-content-evenly w-100 p-2" style="border: 1px solid red; border-radius: 5%;" role="group">';
				} else {
					echo '<div class="d-flex justify-content-evenly w-100" role="group">';
				}
					echo '<input type="radio" class="btn-check" id="r" name="pColor" value="r" autocomplete="off">';
					echo '<label class="btn btn-danger w-25" for="r" id="lr"> &ensp;</label>';

					echo '<input type="radio" class="btn-check" id="y" name="pColor" value="y" autocomplete="off">';
					echo '<label class="btn btn-warning w-25 ms-2" for="y" id="ly"> &ensp;</label>';

					echo '<input type="radio" class="btn-check" id="b" name="pColor" value="b" autocomplete="off">';
					echo '<label class="btn btn-primary w-25 ms-2" for="b" id="lb"> &ensp;</label>';

					echo '<input type="radio" class="btn-check" id="g" name="pColor" value="g" autocomplete="off">';
					echo '<label class="btn btn-success w-25 ms-2" for="g" id="lg"> &ensp;</label>';
				echo '</div>';

				// Submit
				echo '<button type="submit" class="btn btn-outline-secondary mt-2 w-100 shadow" name="pSubmit">ENTER</button>';
			echo '</form>';

		// End Left Container
		echo '</div>';

		// Right Container
		echo '<div class="container p-3">';

			// Sleep - Wake Input
			echo '<form class="border rounded p-4 shadow" id="sleepForm" method="post" action="">';

				// Name
				echo '<h5 class="text-c mb-4">Log Sleep</h5>';
				echo '<div class="d-flex justify-content-evently w-100 mb-2" role="group">';
					echo '<input type="radio" class="btn-check shadow" value="tonka" name="sName" id="stonka" autocomplete="off">';
					if (isset($_GET['errMsg']) && strstr($_GET['errMsg'], "noSFert"))
						echo '<label class="btn btn-outline-danger w-50 text-white" for="stonka">TONKA</label>';
					else
						echo '<label class="btn btn-outline-secondary w-50 text-white" for="stonka">TONKA</label>';
					
					echo '<input type="radio" class="btn-check shadow" value="koda" name="sName" id="skoda" autocomplete="off">';
					if (isset($_GET['errMsg']) && strstr($_GET['errMsg'], "noSFert"))
						echo '<label class="btn btn-outline-danger w-50 ms-2 text-white" for="skoda">KODA</label>';
					else
						echo '<label class="btn btn-outline-secondary w-50 ms-2 text-white" for="skoda">KODA</label>';
				echo '</div>';

				// Date & Time
				echo '<div class="d-flex justify-content-evenly w-100">';
					echo '<input class="form-control mb-2 shadow" type="date" id="sDate" name="sDate" required>';
					echo '<input class="form-control mb-2 ms-2 shadow" type="time" id="sTime" name="sTime" required>';
				echo '</div>';

				// Sleep or Wake
				echo '<div class="d-flex justify-content-evently w-100" role="group">';
					echo '<input type="radio" class="btn-check" value="wake" name="status" id="wake" autocomplete="off">';
					if (isset( $_GET['errMsg']) && strstr($_GET['errMsg'], "noSStat" ) )
						echo '<label class="btn btn-outline-danger w-50" for="wake">☀️</label>';
					else
						echo '<label class="btn btn-outline-secondary w-50" for="wake">☀️</label>';
					
					echo '<input type="radio" class="btn-check" value="sleep" name="status" id="sleep" autocomplete="off">';
					if (isset( $_GET['errMsg']) && strstr($_GET['errMsg'], "noSStat" ) )
						echo '<label class="btn btn-outline-danger w-50 ms-2" for="sleep">🌑</label>';
					else
						echo '<label class="btn btn-outline-secondary w-50 ms-2" for="sleep">🌑</label>';
				echo '</div>';

				// Submit
				echo '<button type="submit" class="btn btn-outline-secondary shadow mt-2 w-100" name="sSubmit">ENTER</button>';
			echo '</form>';

		// End Right Container
		echo '</div>';

	// End Entry Container
	echo '</div>';

	// Query for Date to use in Data Display
	$sql_data_date = "SELECT `id` FROM `date` WHERE `date` = '$date';";
	$result_date = $dblink->query( $sql_data_date ) or
		die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
	$d_arr = $result_date->fetch_array( MYSQLI_ASSOC );
	
	$date_id = "";
	if ( $d_arr != NULL )
		$date_id = $d_arr['id'];

	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                        DATA CONTAINER                                                                       //
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	

	echo '<div class="body-c-r border rounded mx-3 shadow">';

		// Query for Poop Data
		$sql_data_poop = "SELECT * FROM `poop_log` WHERE `date_id` = '$date_id';";
		$result_poop = $dblink->query( $sql_data_poop ) or
			die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

		//////////////////////////////////////
		// Data View - Today's Current Poop //
		//////////////////////////////////////

		echo '<div class="container p-3">';
			echo '<h5 class="text-c pb-2">Today\'s Poops</h5>';

			echo '<div class="container d-flex justify-content-center border rounded p-4 shadow">';

			if ( mysqli_num_rows( $result_poop ) > 0 ) {

				// TABLE
				echo '<table class="table table-bordered table-dark">';
					echo '<thead>';
						echo '<tr>';
							echo '<td class="w-33 " id="ptDate"></td>';
							echo '<td class="w-33 ">TONKA</td>';
							echo '<td class="w-33 ">KODA</td>';
						echo '</tr>';
					echo '</thead>';

					echo '<tbody>';

					while ( $data = $result_poop->fetch_array( MYSQLI_ASSOC ) ) {
						$am_pm = date( 'h:i A', strtotime( $data['time'] ) );

						echo '<tr>';
							echo '<td class="w-33 ">'.$am_pm.'</td>';
							if ( $data['tonka'] != 'z' ) {
								$color = getColor( $data['tonka'] );
								echo $color;
							} else {
								echo '<td class="w-33 "> </td>';
							}
							if ( $data['koda'] != 'z' ) {
								$color = getColor( $data['koda'] );
								echo $color;
							} else {
								echo '<td class="w-33 "> </td>';
							}
						echo '</tr>';
					}
					echo '</tbody>';
				echo '</table>';

			} else {
					echo '<img src="assets/images/eat.png" width="150">';
			}

			echo '</div>';

		// End Data - Poop
		echo '</div>';

		// Query for Sleep Data
		$sql_sleep_id = "SELECT * FROM `sleep_log` WHERE `date_id` = '$date_id';";
		$result_sleep = $dblink->query( $sql_sleep_id ) or
			die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
		$l_arr = $result_sleep->fetch_array( MYSQLI_ASSOC );

		$log_id = "";
		if ( $l_arr != NULL )
			$log_id = $l_arr['id'];

		$sql_data_tonka = "SELECT * FROM `time_log` WHERE `log_id` = '$log_id' AND `fert` = 'tonka';";
		$sql_data_koda = "SELECT * FROM `time_log` WHERE `log_id` = '$log_id' AND `fert` = 'koda';";
		$result_tonka_sleep = $dblink->query( $sql_data_tonka ) or
			die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
		$result_koda_sleep = $dblink->query( $sql_data_koda ) or
			die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

		///////////////////////////////////////
		// Data View - Today's Current Sleep //
		///////////////////////////////////////

		echo '<div class="container p-3">';
			echo '<h5 class="text-c pb-2">Today\'s Sleep/Wake</h5>';

			echo '<div class="body-c-r justify-content-evenly border rounded p-4 shadow">';

			if ( mysqli_num_rows( $result_tonka_sleep ) > 0 ) {
				echo '<table class="table table-bordered table-dark table-striped table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<td colspan="2">TONKA</td>';
						echo '</tr>';
					echo '</thead>';

					echo '<tbody>';

					while ( $data = $result_tonka_sleep->fetch_array( MYSQLI_ASSOC ) ) {

						$cid = $data['id'];
						if ( $data['sleep'] == NULL ) {
							$am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
							echo '<tr class="hover-hand" onClick="rowClick('.$cid.', \''.$data['fert'].'\', \''.$data['wake'].'\', \'empty\')">';
								echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
								echo '<td class="text-mobile"> </td>';
							echo '</tr>';
						} else {
							$am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
							$am_pm_sp = date( 'h:i A', strtotime( $data['sleep'] ) );
							echo '<tr class="hover-hand" onClick="rowClick('.$cid.', \''.$data['fert'].'\', \''.$data['wake'].'\', \''.$data['sleep'].'\')">';
								echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
								echo '<td class="text-mobile">'.$am_pm_sp.'</td>';
							echo '</tr>';
						}
					}
					echo '</tbody>';
					// echo '<tfoot>';
					// 	echo '<td colspan="2">5:30</td>';
					// echo '</tfoot>';
				echo '</table>';
			} else {
				echo '<div class="container d-flex justify-content-center">';
					echo '<img src="assets/images/sleep.png" width="150">';
				echo '</div>';
			}

			if ( mysqli_num_rows( $result_koda_sleep ) > 0 ) {
				echo '<table class="table table-bordered table-dark table-striped table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<td colspan="2">KODA</td>';
						echo '</tr>';
					echo '</thead>';

					echo '<tbody>';

					while ( $data = $result_koda_sleep->fetch_array( MYSQLI_ASSOC ) ) {
						
						$cid = $data['id'];
						if ( $data['sleep'] == NULL ) {
							$am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
							echo '<tr class="hover-hand" onClick="rowClick('.$cid.', \''.$data['fert'].'\', \''.$data['wake'].'\', \'empty\')">';
								echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
								echo '<td class="text-mobile"> </td>';
							echo '</tr>';
						} else {
							$am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
							$am_pm_sp = date( 'h:i A', strtotime( $data['sleep'] ) );
							echo '<tr class="hover-hand" onClick="rowClick('.$cid.', \''.$data['fert'].'\', \''.$data['wake'].'\', \''.$data['sleep'].'\')">';
								echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
								echo '<td class="text-mobile">'.$am_pm_sp.'</td>';
							echo '</tr>';
						}
					}
					echo '</tbody>';
					// echo '<tfoot>';
					// 	echo '<td colspan="2">6:00</td>';
					// echo '</tfoot>';
				echo '</table>';
			} else {
				echo '<div class="container d-flex justify-content-center">';
					echo '<img src="assets/images/sleep.png" width="150">';
				echo '</div>';
			}
			echo '</div>';

		// End Data - Sleep
		echo '</div>';

	// End Data Container
	echo '</div>';


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                        NOTE CONTAINER                                                                       //
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	echo '<form class="body-c-c border rounded m-3 p-3 shadow" method="post" action="">';

			echo '<h5>Leave a Note</h5>';
			echo '<textarea class="form-control mb-2" id="noteText" name="noteText" rows="4" required></textarea>';
			echo '<div class="d-flex justify-content-evenly">';
				echo '<input type="text" class="form-control w-25" placeholder="Name" name="noteName" id="noteName" required>';
				echo '<button class="btn btn-outline-secondary" style="margin-right: 0; margin-left: auto;" type="submit" name="noteButton" id="noteButton">ENTER</button>';
			echo '</div>';

	// End Note Container
	echo '</form>';


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                                    NOTE DISPLAY CONTAINER                                                                   //
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// TODO container not centering
	echo '<div class="body-c-c border rounded m-3 p-3 shadow">';

		// Query all notes
		$sql_notes = "SELECT * FROM `notes` ORDER BY `id` DESC;";
		$result_notes = $dblink->query( $sql_notes ) or
			die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

		// Card Container
		echo '<div id="rows" class="row row-cols-auto d-flex justify-content-center w-100 shadow">';
			while ( $card = $result_notes->fetch_array( MYSQLI_ASSOC ) ) {

				// Get corresponding date
				$did = $card['date_id'];
				$sql_date = "SELECT * FROM `date` WHERE `id` = '$did';";
				$result_date = $dblink->query( $sql_date ) or
                    die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
                $log_date = $result_date->fetch_array( MYSQLI_ASSOC );

				echo '<div class="card shadow overflow-hidden mb-4 w-100">';
					echo '<div class="card-header d-flex justify-content-between">';
						echo '<h5 class="card-title">'.$card['user'].'</h5>';
						echo '<h6 class="card-title">'.$log_date['date'].'</h6>';
					echo '</div>';
					echo '<div class="card-body no-scrollbar overflow-auto">';
						echo '<p>'.$card['note'].'</p>';
					echo '</div>';
				echo '</div>';
			}

	echo '</div>';


	// Modal
	include('modal.php');

?>