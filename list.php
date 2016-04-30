<?php

include 'connect.php'; #the database connect script I use in all my commands for the raffle system.

/* get the amount of entries in the database */
function get_entry_amount($db) {
	if($count = $db->query("SELECT * FROM raffle")) {
		if($count = $count->num_rows) {
			return $count;
			
			$count->free();
		}
	}
}

$entries = get_entry_amount($db);

/* get all information about every single entry and save them in $raffler */
function get_all_entries($db) {
		if($result = $db->query("SELECT * FROM raffle")) {
			
			$x = 0;
			while($row = $result->fetch_object()) {
				$entry[$x]["user"] = $row->user;
				$entry[$x]["fc"] = $row->friendcode;
				$entry[$x]["id"] = $row->id;
				$x++;
			}
			
			return $entry;
			
			$result->free();
		}

}	

$raffler = get_all_entries($db);

/* as long as there is an entry, show them in the format "User: 'name' | FC: 'friendcode' " */
$z = 0;

while($z < $entries) {
	echo 'User: ' . $raffler[$z]["user"] . ' | FC = ' . $raffler[$z]["fc"] . '<br>';
	$z++;
}
?>