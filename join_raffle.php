<?php

require 'connect.php'; #the database connect script I use in all my commands for the raffle system.

$user = htmlspecialchars($_GET["user"]); #get user by link (http://[linktowebpage]/join_raffle.php/?user=$(user)&fc=$(query)) where $(user) is the user of the command and $(query) is the friendcode they type after it.
$fc = htmlspecialchars($_GET["fc"]); 

function add_to_raffle($db, $user, $fc){ 
/* The database format I applied is a simple database table with 3 indexes called id, user, friendcode */
/* This function checks if there is a user and friendcode specefied and if so adds them to the database. Crashes with an error if there is a problem. Which should never happen due to other error reporters in this code */
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
	
	

/* This function checks if the user does not already excist, it does not check friendcodes.  */
function check_if_non_dupe($db, $user) {
	$acceptance = get_entries($db, "SELECT * FROM raffle WHERE user = \"{$user}\"", "user");
	
	if ($acceptance) {
		return true;
	} else {
		return false;
	}
	}	
	
if (check_if_non_dupe($db, $user) == false) {	#if it is not a dupelicate
	if (!empty($fc)) {		#and if there is a friend code
			if (add_to_raffle($db, $user, $fc)) {	#then add the user to the database
			echo "@$user successfully added to raffle!";
		} else { #if this fails give a simple error message
			echo "@$user, There was a problem handling your request, please try again.";
		} 
	} else { #tell them to add a friendcode
		echo "@$user, You forgot to include your Friend Code! !join [friendcode]";
	}
} else { #Tells the user they are already in there
	echo "You're already in the Raffle! @$user";
}


?>