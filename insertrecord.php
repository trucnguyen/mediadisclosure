<?php

	include('./config.php');
	
	/* establish connection */
	$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbDatabase);
	if ($mysqli->connect_errno) {
	    printf("Connect failed: %s\n", $mysqli->connect_error);
	    exit();
	}	
	
	if(isset($_POST['json'])){
		$json = $_POST['json'];
		$guid = $_POST['guid'];

		// loop through categories
		for($i=1; $i<=4; $i++){
			// loop through stories within categories
			for($j=1; $j<=5; $j++){
				$story = $json[$i][$j];
				
				echo var_dump($story);
				
				$query = sprintf("INSERT INTO `history` (guid, catorder, storyorder, headline, description, category, selecttime, hidetime)
								 VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
						mysql_escape_string($guid), 
						mysql_escape_string($story['catorder']),
						mysql_escape_string($story['storyorder']),
						stripslashes($story['title']),
						stripslashes($story['desc']),
						mysql_escape_string($story['cat']),
						mysql_escape_string($story['selecttime']),
						mysql_escape_string($story['hidetime']));
						
				echo $query . "\n";
				$query_result = $mysqli->query($query);
			}
		}
		
		echo "Success!";
	}

	$mysqli->close();		
?>