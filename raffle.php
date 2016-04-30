<?php

require 'connect.php'; #the database connect script I use in all my commands for the raffle system.

/* same function as in join_raffle that checks the amount of entries. */
function get_entry_amount($db) {
	if($count = $db->query("SELECT * FROM raffle")) {
		if($count = $count->num_rows) {
			return $count;
			
			$count->free();
		}
	}
}

$entries = get_entry_amount($db);

/* Roll a dice picks a random number between 1 and the amount of people in the list*/
function roll_dice($dice_size) {
	$size = $dice_size;
	
	$random = rand(1, $dice_size);
	
	return $random;
	
}

$winner = roll_dice($entries);

/* With the result of the dice roll, the winner's username and friendcode will be looked up */
function who_won($db, $winner) {
	if($result = $db->query("SELECT * FROM raffle WHERE id = {$winner}")) {
			
			while($row = $result->fetch_object()) {
				$entry["user"] = $row->user;
				$entry["fc"] = $row->friendcode;
			}
			
			return $entry;
			
			$result->free();
		}

	}	

$the_winner = who_won($db, $winner);

/* checks if the winner has a username and a friendcode else say there is nobody in there */
/*announces the winner if condition is met */
if (!empty ($the_winner["user"]) && !empty ($the_winner["fc"])) {
echo "Next up for battle is: @" . $the_winner["user"] . " and their friendcode is: " . $the_winner["fc"];
} else {
	echo "There is nobody in the Raffle!";
}

/* get everyone in the list and save them all in an array */
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

/* when making this part, I noticed I didn't have to do this at all, but since I already made it I decided to use it, */
/* The $entry array goes from 0 to $entries-1 due to the counting in PHP whice this changes to 1 - $entries */
$i = $entries;
while ($i > 0) {
	$items[$i]["user"] = $raffler[($i-1)]["user"];
	$items[$i]["fc"] = $raffler[($i-1)]["fc"];
	$items[$i]["id"] = $raffler[($i-1)]["id"];
	
	$i--;
} 

#The winner will be removed from the array.
$items[$winner] = null;

/* Delete every entry in the database table */
function truncate($db) {
	$query = "TRUNCATE TABLE raffle";
	
	$db->query($query);
}

truncate($db);

/* The database format I applied is a simple database table with 3 indexes called id, user, friendcode */
/* This function checks if there is a user and friendcode specefied and if so adds them to the database. Crashes with an error if there is a problem. Which should never happen due to other error reporters in this code */
function add_to_raffle($db, $user, $fc){
$query = "INSERT INTO raffle (user, friendcode) VALUES (?,?)";

if (!empty($user)) {
	if (!empty($fc)) {
		if ($prepare = $db->prepare($query)) {
			
			$user =  htmlspecialchars($user);
			$fc =  htmlspecialchars($fc);
			
			$prepare->bind_param("ss", $user, $fc);
			$prepare->execute();	
			return $prepare->affected_rows;
			$prepare->close();
		} else {
			die($db->error());
			}
		}
	}
}
/* for every entry in the array, put them back in the database, skip the entry in the array if the result is null */
$z = 0;
while($z < $entries) {

	
	if (!empty($items[$z+1])) {
	
	if (add_to_raffle($db, $items[$z+1]["user"], $items[$z+1]["fc"])) {
		
	} else {
	}
	
	}
	
	$z++;
}