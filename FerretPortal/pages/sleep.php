<?php
    /* TODO:
     * ✓ Redo -> if date1 wake has date2 sleep : I.E. 3/7/24 wake 11:00 PM, 3/8/24 sleep 12:30 AM
     * ✓ Don't calculate if status = open
     * - Total time display works, but ideally pull from 'sleep_log'.active_time
     * - Query to save total time to sleep_log.active_time here or else where?
     */

    // Query
    $sql_log = "SELECT * FROM `sleep_log` ORDER BY id DESC;";
    $result_log = $dblink->query( $sql_log ) or
        die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

    // Main Container
    echo '<div class="container pt-4">';

        // Filter Container
        echo '<div>';

        echo '</div>';

        echo '<div id="rows" class="row row-cols-auto d-flex justify-content-center">';
        while ( $card = $result_log->fetch_array( MYSQLI_ASSOC ) ) {
        
            // Get corressponding date
            $did = $card['date_id'];
            $sql_date = "SELECT * FROM `date` WHERE `id` = '$did';";
            $result_date = $dblink->query( $sql_date ) or
                die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
            $log_date = $result_date->fetch_array( MYSQLI_ASSOC );

            // Get corressponding time logs
            $lid = $card['id'];
            $sql_data_tonka = "SELECT * FROM `time_log` WHERE `log_id` = '$lid' AND `fert` = 'tonka';";
            $sql_data_koda = "SELECT * FROM `time_log` WHERE `log_id` = '$lid' AND `fert` = 'koda';";
            $result_tonka_sleep = $dblink->query( $sql_data_tonka ) or
                die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');
            $result_koda_sleep = $dblink->query( $sql_data_koda ) or
                die('<p>Something went wrong with: $sql<br>'.$dblink->error.'</p>');

            /* Add condition -> Don't display card IF sleep log doesn't have time logs
             * i.e. check if result_koda/tonka_sleep != NULL -> display card
             * else -> don't display 
             */

                echo '<div class="col d-flex justify-content-center">';

                    // Card
                    echo '<div class="card shadow overflow-hidden mb-4" style="width: 24rem;">';
                        echo '<div class="card-header">'.$log_date['date'].'</div>';
                        echo '<div class="card-body no-scrollbar overflow-auto" style="max-height: 24rem;">';
                        echo '<div class="body-c-r">';

                            echo '<table class="table table-bordered table-dark table-striped">';
                                echo '<thead>';
                                    echo '<tr>';
                                        echo '<td colspan="2">TONKA</td>';
                                    echo '</tr>';
                                echo '</thead>';

                                echo '<tbody>';
                                
                                $active_sum = [];
                                while ( $data = $result_tonka_sleep->fetch_array( MYSQLI_ASSOC ) ) {
                                    
                                    if ( $data['sleep'] == NULL ) {
                                        $am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
                                        echo '<tr>';
                                            echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
                                            echo '<td class="text-mobile"> </td>';
                                        echo '</tr>';
                                    } else {
                                        $am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
                                        $am_pm_sp = date( 'h:i A', strtotime( $data['sleep'] ) );
                                        echo '<tr>';
                                            echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
                                            echo '<td class="text-mobile">'.$am_pm_sp.'</td>';
                                        echo '</tr>';
                                    }

                                    $sleep = strtotime( $data['sleep'] );
                                    $wake = strtotime( $data['wake'] );

                                    // 64800 is the offset from UTC to CST
                                    if ( $data['sleep'] == NULL )
                                        $diff = 0;
                                    else
                                        $diff = $sleep - $wake - 64800;

                                    $active_sum[] = $diff;
                                }
                                echo '</tbody>';

                                $total = 0;;
                                foreach ( $active_sum as $time ) {
                                    $hours = (int)date('H', $time);
                                    $mins = (int)date('i', $time);
                                    $total += ( $hours * 3600 ) + ( $mins * 60 );
                                }
                                $total_hours = floor( $total / 3600 );
                                $total_mins = floor( ($total % 3600 ) / 60 );
                                $total_time = sprintf('%02d:%02d', $total_hours, $total_mins);

                                echo '<tfoot>';
                                	echo '<td colspan="2"><b>'.$total_time.'</b></td>';
                                echo '</tfoot>';

                            echo '</table>';

                            echo '<table class="table table-bordered table-dark table-striped">';
                                echo '<thead>';
                                    echo '<tr>';
                                        echo '<td colspan="2">KODA</td>';
                                    echo '</tr>';
                                echo '</thead>';

                                echo '<tbody>';

                                $active_sum = [];
                                while ( $data = $result_koda_sleep->fetch_array( MYSQLI_ASSOC ) ) {
                                    
                                    if ( $data['sleep'] == NULL ) {
                                        $am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
                                        echo '<tr>';
                                            echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
                                            echo '<td class="text-mobile"> </td>';
                                        echo '</tr>';
                                    } else {
                                        $am_pm_wk = date( 'h:i A', strtotime( $data['wake'] ) );
                                        $am_pm_sp = date( 'h:i A', strtotime( $data['sleep'] ) );
                                        echo '<tr>';
                                            echo '<td class="text-mobile">'.$am_pm_wk.'</td>';
                                            echo '<td class="text-mobile">'.$am_pm_sp.'</td>';
                                        echo '</tr>';
                                    }

                                    $sleep = strtotime( $data['sleep'] );
                                    $wake = strtotime( $data['wake'] );

                                    // 64800 is the offset from UTC to CST
                                    if ( $data['sleep'] == NULL )
                                        $diff = 0;
                                    else
                                        $diff = $sleep - $wake - 64800;

                                    $active_sum[] = $diff;
                                }
                                echo '</tbody>';

                                $total = 0;;
                                foreach ( $active_sum as $time ) {
                                    $hours = (int)date('H', $time);
                                    $mins = (int)date('i', $time);
                                    $total += ( $hours * 3600 ) + ( $mins * 60 );
                                }
                                $total_hours = floor( $total / 3600 );
                                $total_mins = floor( ($total % 3600 ) / 60 );
                                $total_time = sprintf('%02d:%02d', $total_hours, $total_mins);

                                echo '<tfoot>';
                                	echo '<td colspan="2"><b>'.$total_time.'</b></td>';
                                echo '</tfoot>';

                            echo '</table>';

                        echo '</div>';
                    echo '</div>';

                    // End Card
                    echo '</div>';

                echo '</div>';
        }
        echo '</div>';

    // End Main Container
    echo '</div>';

?>