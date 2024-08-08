<?php
    /* TODO:
     * ✓ Convert 24 hr time display to AM/PM
     * - Use arrs in line 27/28 to add filter by month/year
     * - Summarization of poop stat per day, per month (% of r/y/b/g)
     */

    // Query
    $sql_date = "SELECT * FROM `date` ORDER BY id DESC;";
    $sql_log = "SELECT * FROM `poop_log`;";
    $result_date = $dblink->query( $sql_date ) or
        die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
    $result_log = $dblink->query( $sql_log ) or
        die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

    // Get dates
    $dates = [];
    while ( $data = $result_date->fetch_array( MYSQLI_ASSOC ) ) {
        $dates[] = $data;
    }

    // Split dates into years and months
    $years = [];
    $months = [];

    foreach ( $dates as $temp ) {
        $piece = explode('-', $temp['date']);
        
        if ( !in_array( $piece[0], $years ) ) {
            $years[] = $piece[0];
        }
        if ( !in_array( $piece[1], $months ) ) {
            $months[] = $piece[1];
        }
    }

    // Convert SQL Log results to array
    $logs = [];
    while ( $data = $result_log->fetch_array( MYSQLI_ASSOC ) ) {
        $logs[] = $data;
    }

    // Main Container
    echo '<div class="container pt-4">';

        // Filter Container
        echo '<div>';

        echo '</div>';

        echo '<div id="rows" class="row row-cols-auto d-flex justify-content-center">';
        foreach ( $dates as $card ) {

            // Filter logs corresponding to date
            $idx = $card['id'];
            $date_log = array_filter( $logs, function( $element ) use ( $idx ) {
                return $element['date_id'] === $idx;
            });

            if ( count($date_log) != 0 ) { 
                echo '<div class="col d-flex justify-content-center">';

                    // Card
                    echo '<div class="card shadow overflow-hidden mb-4" style="width: 24rem;">';
                        echo '<div class="card-header">'.$card['date'].'</div>';
                        echo '<div class="card-body no-scrollbar overflow-auto" style="max-height: 24rem;">';

                        echo '<table class="table table-bordered table-dark">';
                            echo '<thead>';
                                echo '<tr>';
                                    echo '<td class="w-33 " id="ptDate"></td>';
                                    echo '<td class="w-33 ">TONKA</td>';
                                    echo '<td class="w-33 ">KODA</td>';
                                echo '</tr>';
                            echo '</thead>';

                            echo '<tbody>';
                            foreach ( $date_log as $row ) {
                                $am_pm = date( 'h:i A', strtotime( $row['time'] ) );

                                echo '<tr>';
                                    echo '<td class="w-33 ">'.$am_pm.'</td>';
                                    if ( $row['tonka'] != 'z' ) {
                                        $color = getColor( $row['tonka'] );
                                        echo $color;
                                    } else {
                                        echo '<td class="w-33 "> </td>';
                                    }
                                    if ( $row['koda'] != 'z' ) {
                                        $color = getColor( $row['koda'] );
                                        echo $color;
                                    } else {
                                        echo '<td class="w-33 "> </td>';
                                    }
                                echo '</tr>';
                            }
                            echo '</tbody>';
                        echo '</table>';
                    echo '</div>';

                    // End Card
                    echo '</div>';

                echo '</div>';
            }
        }
        echo '</div>';

    // End Main Container
    echo '</div>';
?>