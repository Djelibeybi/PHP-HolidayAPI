#!/usr/bin/php
<?php
/**
 * A chaser app that picks a random colour and then chases it up the Holiday string
 *
 * @author Avi Miller <avi.miller@gmail.com>
 * @version 1.0
 * @copyright 2013 Avi Miller
 * @license MIT
 */
 
include 'class.holiday.php';

// Need to pass the IP address or hostname of the Holiday on the command-line
if ( isset($argv[1]) ) {

	$holiday = new Holiday($argv[1]);
	$count = 0;
	
	while ($count < 2) {

		$r = rand(0, 255);
		$g = rand(0, 255);
		$b = rand(0, 255);
		
		// Now, based on another random value, squash one of these values
		switch (rand(0,2)) {
			
			case 0:
				$r = 0;
			break;
			case 1:
				$g = 0;
			break;
			case 2:
				$b = 0;
			break;
		}
	
		for ($globe = 0; $globe < $holiday->NUM_GLOBES; $globe++) {

			// First turn off all the globes
			$holiday->fill(0, 0, 0);
			
			// Now, turn on the next globe in sequence with the random colour
			$holiday->setglobe($globe, $r, $g, $b);
			
			// Render the globes
			$holiday->render();
			
			// Pause for audience applause
			usleep(100000);
			
		}
		
		unset($globe);
		$count++;
			
	}

} else {
	exit(1);
}
 
?>