<?php

    // Main Container
    echo '<div class="container pt-4">';

        if ( isset( $_POST['wSubmit'] ) ) {

            // Var Assign
            $errors = "";
            $date = $_POST['wDate'];
            $tonka = $_POST['tonkaWeight'];
            $koda = $_POST['kodaWeight'];
            $isTonka = false;
            $isKoda = false;

            // Null Check - Only if both are null
            if ( $tonka != NULL ) {
                $isTonka = true;
            }
            if ( $koda != NULL ) {
                $isKoda = true;
            }
            if ( !$isTonka && !$isKoda ) {
                $errors .= 'noWeightEntry';
            }

            // Redirect if there is an error
            if ( $errors != NULL )
                header("Location: index.php?page=weight&errMsg=$errors");

            // No errors
            else {

                // Check if date exists in DB
				$did = getID( $dblink, 'date', 'date', $date );

                // Check if log exists in DB
                $lid = getID( $dblink, 'weight_log', 'date_id', $did );

                // Check if tonka or koda is null
                $sql_null = "SELECT * FROM `weight_log` WHERE `id` = $lid;";
                $result = $dblink->query( $sql_null ) or
			        die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
                $null_check = $result->fetch_array( MYSQLI_ASSOC );

                // Query
                if ( $isTonka && $isKoda && $null_check['tonka'] == null && $null_check['koda'] == null )
                    $sql_weight = "UPDATE `weight_log` SET `date_id` = '$did', `tonka` = '$tonka', `koda` = '$koda' WHERE `id` = $lid;";
                else if ( $isTonka && $null_check['tonka'] == null )
                    $sql_weight = "UPDATE `weight_log` SET `date_id` = '$did', `tonka` = '$tonka' WHERE `id` = $lid;";
                else if ( $isKoda && $null_check['koda'] == null )
                    $sql_weight = "UPDATE `weight_log` SET `date_id` = '$did', `koda` = '$koda' WHERE `id` = $lid;";

                if ( $sql_weight == null ) {
                    header("Location: index.php?page=weight&alert=errorWeightExists");
                } else {
                    $dblink->query( $sql_weight ) or
                        die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
                    header("Location: index.php?page=weight&alert=successWeightAdd");
                }
            }
        }

        // Entry Container
        echo '<form class="border rounded p-4 mb-4 shadow d-flex flex-column justify-content-center align-items-center" id="weightForm" method="post" action="">';
            echo '<h5 class="text-c mb-4">Weight Entry</h5>';
            echo '<div class="d-flex flex-row justify-content-center w-100 mb-2 px-2">';
                echo '<label class="text-c border rounded w-25 py-2 me-2">Tonka</label>';
                echo '<input type="number" id="tonkaWeight" name="tonkaWeight" class="form-control w-50" min="0" max="3000">';
            echo '</div>';
            echo '<div class="d-flex flex-row justify-content-center w-100 mb-2 px-2">';
                echo '<label class="text-c border rounded w-25 py-2 me-2">Koda</label>';
                echo '<input type="number" id="kodaWeight" name="kodaWeight" class="form-control w-50" min="0" max="3000">';
            echo '</div>';
            echo '<input class="form-control mb-2 w-75 shadow" type="date" id="wDate" name="wDate" required>';
            echo '<button type="submit" name="wSubmit" class="btn btn-outline-secondary w-75 shadow">ENTER</button>';
        echo '</form>';

        // Graph Container
        echo '<div>';

        echo '</div>';

        // Card Data Query
        $sql_log = "SELECT * FROM `weight_log` ORDER BY id DESC;";
        $result_log = $dblink->query( $sql_log ) or
            die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

        // Card Container
        echo '<div id="rows" class="row row-cols-auto d-flex justify-content-center">';
            while ( $card = $result_log->fetch_array( MYSQLI_ASSOC ) ) {

                // Get corressponding date
                $did = $card['date_id'];
                $sql_date = "SELECT * FROM `date` WHERE `id` = '$did';";
                $result_date = $dblink->query( $sql_date ) or
                    die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
                $log_date = $result_date->fetch_array( MYSQLI_ASSOC );

                echo '<div class="col d-flex justify-content-center">';

                    // Card
                    echo '<div class="card shadow overflow-hidden mb-4" style="width: 24rem;">';
                        echo '<div class="card-header">'.$log_date['date'].'</div>';
                        echo '<div class="card-body no-scrollbar overflow-auto" style="max-height: 24rem;">';
                            echo '<div class="body-c-r">';

                                echo '<table class="table table-bordered table-dark">';
                                    echo '<thead>';
                                        echo '<tr>';
                                            echo '<td>TONKA</td>';
                                            echo '<td>KODA</td>';
                                        echo '</tr>';
                                    echo '</thead>';

                                    echo '<tbody>';
                                        echo '<tr>';
                                            echo '<td>'.$card['tonka'].'</td>';
                                            echo '<td>'.$card['koda'].'</td>';
                                        echo '</tr>';
                                    echo '</tbody>';
                                echo '</table>';

                            echo  '</div>';
                        echo '</div>';
                    echo '</div>';

                echo '</div>';
            }
        echo '</div>';

    echo '</div>';

    // Modal
	include('modal.php');
    
?>