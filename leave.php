<?php

require 'connect.php'; #the database connect script I use in all my commands for the raffle system.

/* get user by link (http://[linktowebpage]/leave.php/?user=$(user)) where $(user) is the user of the command */
/* a moderator can also use this command where you change $(user) to $(query) and fills in the name of the user.   */
$user = htmlspecialchars($_GET["user"]);

/* get the amount of entries in the list */
function get_entry_amount($db) {
	if($count = $db->query("SELECT * FROM raffle")) {
		if($count = $count->num_rows) {
			return $count;
			
			$count->free();
		}
	}
}

$entries = get_entry_amount($db);

/* A function I set up that searches for something in my database with a given query and table */
function get_entries($db, $query, $table) {
	if($result = $db->query($query)) {
			$i = 0;
			$entry = array();
			while($row = $result->fetch_object()) {
				$entry[$i] = $row->$table;
				$i++;
				
			}
			return $entry;
			
			$result->free();
		}

	}

/* checks if the user that was supplied through a command excists in the list. Returns true in that case, else returns false. */	
function check_if_excist($db, $user) {
	$acceptance = get_entries($db, "SELECT * FROM raffle WHERE user = \"{$user}\"", "user");
	
	if ($acceptance) {
		return true;
	} else {
		return false;
	}
	}	

/* deletes the entry from the database (Decided to do it in a new way instead of how I did in raffle.php just because c: ) */ 	
function remove_from_raffle($db, $user){
$query = "DELETE FROM raffle WHERE user = \"{$user}\"";

if (!empty($user)) {
		if ($prepare = $db->prepare($query)) {
			
			$user =  htmlspecialchars($user);
			
			$prepare->bind_param("s", $user);
			$prepare->execute();	
			return $prepare->affected_rows;
			$prepare->close();
		} else {
			die($db->error());
		}
	} else {
		echo "An error occured, No user found.";
	}
}
/* A combination of all the things I used in raffle.php that takes all entries and reorders them so there is not a hole in between entries */
function reset_data($db, $entries, $user) {
	
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

$i = $entries;
while ($i > 0) {
	$items[$i]["user"] = $raffler[($i-1)]["user"];
	$items[$i]["fc"] = $raffler[($i-1)]["fc"];
	$items[$i]["id"] = $raffler[($i-1)]["id"];
	
	$i--;
} 

$items[$user] = null;

function truncate($db) {
	$query = "TRUNCATE TABLE raffle";
	
	$db->query($query);
}

truncate($db);

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

$z = 0;
while($z < $entries) {

	
	if (!empty($items[$z+1])) {
	
	if (add_to_raffle($db, $items[$z+1]["user"], $items[$z+1]["fc"])) {
		
	} else {
	}
	
	}
	
	$z++;
}

	return true;
}




/* As long as a user is supplied, which will be automatic if a user uses !leave and needs a variable with !force_leave */
if (!empty($user)) {
	if (check_if_excist($db, $user) == true) { #And the user is indeed in the listing
		if (remove_from_raffle($db, $user)) { #Remove him from it and be sure it succeeds
			if (reset_data($db, $entries, $user)) { #Did the reset not crash?
				echo "@$user successfully removed from Raffle List.";
			} else { #give an error
				echo "An unknown error occurred. Failed to reorder.";
			}
			
		} else { #give an error
			echo "An unknown error occurred. Failed to delete";
		}
		
	} else { #tell them they aren't on the list #gitgood
		echo "@$user you are not in the current Raffle Pool!";
	}
} else { #tell the mods they forgot to include a username!
	echo "Unknown format, !force_leave [username]";
}