<?php

include 'connect.php'; #the database connect script I use in all my commands for the raffle system.

/* truncates the database */
function truncate($db) {
	$query = "TRUNCATE TABLE raffle";
	
	if ($db->query($query)) {
	return true;
	}
}

if (truncate($db)) {
echo "Succesfully cleared raffle entries";
} else {
echo "Unable to clear raffle entries, please contact your admin";
}