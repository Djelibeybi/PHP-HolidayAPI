#!/usr/bin/php
<?php
/**
 * A super simple example to send a gradient from off to full red in 50 steps/second.
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
	
	// Define the beginning colour in an array of RGB.
	$begin = array(0, 0, 0);
	
	// Define the ending colour in an array of RGB.
	$end   = array(255, 0, 0);
	
	// Specify the steps/second
	$steps = 50;
	
	// Do the magic
	$holiday->gradient($begin, $end, $steps);
			
} else {
	exit(1);
}
 
?>