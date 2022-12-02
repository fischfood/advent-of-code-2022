<?php

/**
 * Day 1: Calorie Counting
 */

// Get data from the list of elves and their calories
$data = file_get_contents('data/data-01.txt');

// Elves are split by a double line break, so we will break each elf group into an array
$rows = explode("\n\n", $data);

/**
 * Part One - Find the Elf carrying the most Calories, output the total amount of Calories
 */

// Set a base number of 0 to compare the "highest" total to
$highest = 0;

// For each elf...
foreach( $rows as $row ) {

	// Separate each new line into a stand alone number to put into an array
	$numbers = explode("\n", $row);

	// Add up all of the values in this array
	$value = array_sum( $numbers );

	// If this value is higher than the previous, set this as the highest
	if ( $value > $highest ) {
		$highest = $value;
	}
}

echo PHP_EOL . 'Day 1: Calorie Counting' . PHP_EOL;
echo 'Highest = ' . $highest . PHP_EOL;


/**
 * Part Two - Find the top three Elves carrying the most Calories, output the total amount of Calories from all three
 */

// Set a base number of 0 to compare the high, higher, and highest totals to
$highest = 0;
$higher  = 0;
$high    = 0;

// For each elf...
foreach( $rows as $row ) {

	// Separate each new line into a stand alone number to put into an array
	$numbers = explode("\n", $row);

	// Add up all of the values in this array
	$value = array_sum( $numbers );

	// If this value is higher than the previous, move each high to the next lower tier
	if ( $value > $highest ) {
		$high    = $higher;
		$higher  = $highest;
		$highest = $value;
	}
}


echo 'Top 3 Combined = ' . ( $highest + $higher + $high ) . PHP_EOL . PHP_EOL;


