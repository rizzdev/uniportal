<?php
	ini_set('display_errors', 'On');
        error_reporting(E_ALL);

        $mac = $_GET['id'];
        $ap = $_GET['ap'];
        $t = $_GET['t'];
        $url = filter_var($_GET['url'], FILTER_SANITIZE_URL);
        $ssid = $_GET['ssid'];


	$location = "http://portal.babb.tech/index.php?id=" . $mac . "&ap=" . $ap . "&t=" . $t . "&url=" . $url . "&ssid=" . $ssid;

	header("Location: $location");

