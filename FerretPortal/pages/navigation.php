<?php
    // Page Variable
    if (!isset($_GET['page']))
        $page = "home";
    else
        $page = $_GET['page'];

	// Day - Date
	$day = date("l m/d/y");

	echo '<div class="container">';
	echo '<h5 class="roboto-thin-italic text-c b-hr pb-3">' . $day . '</h5>';
    echo '<ul class="nav nav-tabs justify-content-center mt-3">';
    if ($page == "home")
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link active" href="index.php?page=home">HOME</a> </li>';
    else
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link" href="index.php?page=home">HOME</a> </li>';
    if ($page == "poop")
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link active" href="index.php?page=poop">POOP</a> </li>';
    else
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link" href="index.php?page=poop">POOP</a> </li>';
    if ($page == "sleep")
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link active" href="index.php?page=sleep">SLEEP</a> </li>';
    else
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link" href="index.php?page=sleep">SLEEP</a> </li>';
    if ($page == "weight")
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link active" href="index.php?page=weight">WEIGHT</a> </li>';
    else
        echo '<li class="nav-item w-25 text-center"> <a class="nav-link" href="index.php?page=weight">WEIGHT</a> </li>';
    echo '</ul>';
	echo '</div>';
?>