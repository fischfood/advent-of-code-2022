<?php

/**
 * Day 6: Tuning Trouble
 */

$data = file_get_contents('data/data-06.txt');

// The data is all on one line, so break it apart by character
$characters = str_split( $data );

// Set stopping points so we don't keep searching after finding
$found = false;
$found_two = false;

///For every character...
foreach ($characters as $key => $value) {

	// Stop part one if a four character code is found
	if ( $found === false ) {

		// Set the array to compare
		$these_four = [];

		// Instead of writing for $key, $key + 1, $key + 2, and $key + 3
		// Let the four loop do it for as long as we want
		for ($count = 0; $count < 4; $count++) {

			// Add each character to the array
			$these_four[] = $characters[$key + $count];
		}

		// Get the size of the array after stripping non-unique characters
		// If it doesn't equal four, it's not the code
		// If it is, that's the code, and we can stop there
		// Code is at start ($key) + 1 for the array + 3 for the length
		if ( 4 === count( array_unique( $these_four ) ) ) {
			$found = ( $key + 4 );
		}
	}

	// Do it all again for part two, using 14 instead of 4
	if ( $found_two === false ) {
		$these_fourteen = [];

		for ($count = 0; $count < 14; $count++) {
			$these_fourteen[] = $characters[$key + $count];
		}

		if ( 14 === count( array_unique( $these_fourteen ) ) ) {
			$found_two = ( $key + 14 );
		}
	}
}

echo PHP_EOL . 'Day 6: Tuning Trouble' . PHP_EOL;
echo 'Four Character Marker After: ' . $found . PHP_EOL;
echo 'Fourteen Character Marker After: ' . $found_two . PHP_EOL . PHP_EOL;