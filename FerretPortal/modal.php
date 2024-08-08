<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                         Alert MODAL                                                                         //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


if ( isset( $_GET['alert'] ) ) {
    echo '<div class="modal" style="background-color: rgba(10, 10, 10, 0.75); display: block;" id="alertModal" method="post" action="">';
        echo '<div class="modal-dialog modal-dialog-centered">';
            if ( strstr($_GET['alert'], "success" )	)
                echo '<div class="modal-content border-success">';
            if ( strstr($_GET['alert'], "error" ) )
                echo '<div class="modal-content border-danger">';
                echo '<div class="modal-header shadow">';
                    if ( strstr($_GET['alert'], "success" ) )
                        echo '<h5 class="modal-title">SUCCESS!</h5>';
                    if ( strstr($_GET['alert'], "error" ) )
                        echo '<h5 class="modal-title">ERROR!</h5>';
                echo '</div>';
                echo '<div class="modal-body d-flex flex-row justify-content-center align-items-center p-4">';
                    if ( strstr($_GET['alert'], "SleepDel" ) ) {
                        echo '<img src="assets/images/cross.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Sleep Log Successfully Deleted!</p>';
                    }
                    if ( strstr($_GET['alert'], "SleepAdd" ) ) {
                        echo '<img src="assets/images/sleepico.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Sleep Log Successfully Added!</p>';
                    }
                    if ( strstr($_GET['alert'], "WakeAdd") ) {
                        echo '<img src="assets/images/wake.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Wake Log Successfully Added!</p>';
                    }
                    if ( strstr($_GET['alert'], "PoopAdd" ) ) {
                        echo '<img src="assets/images/poop.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Poop Log Successfully Added!</p>';
                    }
                    if ( strstr($_GET['alert'], "WeightAdd" ) ) {
                        echo '<img src="assets/images/weight.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Weight Log Successfully Added!</p>';
                    }
                    if ( strstr($_GET['alert'], "NoteAdd" ) ) {
                        echo '<img src="assets/images/written-paper.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Note Added Successfully!</p>';
                    }
                    if ( strstr($_GET['alert'], "WeightExists" ) ) {
                        echo '<img src="assets/images/error_1.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Weight Log Already Present!</p>';
                    }
                    if ( strstr($_GET['alert'], "SleepEdit" ) ) {
                        echo '<img src="assets/images/written-paper.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">Sleep Log Edited!</p>';
                    }
                    if ( strstr($_GET['alert'], "LogOpen" ) ) {
                        echo '<img src="assets/images/alertError.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">There is still an open log!</p>';
                    }
                    if ( strstr($_GET['alert'], "LogClosed" ) ) {
                        echo '<img src="assets/images/alertError.png" width="50" height="50">';
                        echo '<p class="ms-4 mb-0">There are no open logs to close!</p>';
                    }
                echo '</div>';
                echo '<div class="modal-footer">';
                    if ( strstr($_GET['alert'], 'Weight' ) ) {
                        echo '<button id="alertCloseBtn" class="btn btn-secondary" onclick="alertClose(\'w\')">Close</button>';
                    } else {
                        echo '<button id="alertCloseBtn" class="btn btn-secondary" onclick="alertClose(\'h\')">Close</button>';
                    }
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                         Sleep MODAL                                                                         //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


echo '<form class="modal" style="background-color: rgba(10, 10, 10, 0.75);" id="sleepEdit" method="post" action="">';
    echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
            echo '<div class="modal-header body-c-c shadow">';
                echo '<table class="table table-sm table-dark table-borderless mb-0">';
                    echo '<thead>';
                        echo '<tr>';
                            echo '<td class="w-25 border-end"><b>SLEEP LOG</b></td>';
                            echo '<td class="w-25 border-end" id="sleepEditName">TONKA</td>';
                            echo '<td class="w-25 border-end" id="sleepEditWake">--:--</td>';
                            echo '<td class="w-25" id="sleepEditSleep">--:--</td>';
                        echo '</tr>';
                    echo '</thead>';
                echo '</table>';
            echo '</div>';

            ///////////////////////////
            // Choose Edit || Delete //
            ///////////////////////////

            echo '<div class="modal-body d-flex flex-row justify-content-center p-5" id="sleepBodySelect" style="display: block;">';
                echo '<button id="sleepDelBtn" type="button" class="btn btn-outline-secondary w-33 text-white" onclick="selectDel()">DELETE</button>';
                echo '<button id="sleepEditBtn" type="button" class="btn btn-outline-secondary ms-2 w-33 text-white" onclick="selectEdit()">EDIT</button>';
            echo '</div>';

            //////////
            // Edit //
            //////////

            echo '<div class="modal-body d-flex flex-column p-4" id="sleepBodyEdit" style="display: none !important;">';
                echo '<div class="d-flex flex-row justify-content-center">';
                    echo '<label class="text-c border rounded py-2 me-2 w-25">Wake</label>';
                    echo '<input type="time" id="sleepWake" name="sleepWake" class="form-control" value="">';
                echo '</div>';
                echo '<div class="d-flex flex-row justify-content-center mt-2">';
                    echo '<label class="text-c border rounded py-2 me-2 w-25">Sleep</label>';
                    echo '<input type="time" id="sleepSleep" name="sleepSleep" class="form-control" value="">';
                echo '</div>';
            echo '</div>';

            ////////////
            // Delete //
            ////////////

            echo '<div class="modal-body d-flex flex-row justify-content-center p-4" id="sleepBodyDel" method="post" style="display: none !important;">';
                echo '<img src="assets/images/warning.png" width="50" height="50">';
                echo '<p class="ms-4">Are you sure you want to delete this log?<br><b>This action cannot be undone!</b></p>';
                echo '<input type="hidden" id="sleepDelID" name="sleepDelID" value="">';
            echo '</div>';

            echo '<div class="modal-footer" id="sleepFoot">';
                echo '<button id="sleepBack" type="button" class="btn btn-secondary" onclick="resetSleep()" hidden="true"><</button>';
                echo '<div>';
                    echo '<button id="sleepClose" type="button" class="btn btn-secondary mx-2" onclick="closeSleep()">Cancel</button>';
                    echo '<button id="sleepSave" name="sleepSave" type="submit" class="btn btn-success mx-2" hidden="true">Save</button>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</form>';


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                         Poop MODAL                                                                          //
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


echo '<form class="modal" style="background-color: rgba(10, 10, 10, 0.75);" id="poopEdit" method="post" action="">';
    echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
            echo '<div class="modal-header shadow">';
                // TODO: Header
            echo '</div>';
            echo '<div class="modal-body">';
                // TODO: Body
            echo '</div>';
            echo '<div class="modal-footer">';
                // TODO: Footer
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</form>';

?>