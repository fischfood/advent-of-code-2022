<?php

/**
 * Day 3: Rucksack Reorganization
 * 
 * Values:
 * a through z = 1 through 26
 * A through Z = 27 through 52
 */

$data = file_get_contents('data/data-03.txt');
$rows = explode("\n", $data);

// Set arrays for all letters to assign values to later
$lower_og = range('a', 'z');
$upper_og = range('A', 'Z');

// Flip the keys and values for easier counting later on
$lower = array_flip( $lower_og );
$upper = array_flip( $upper_og );

// Create Arrays for totals to go into
$priorities = [];
$priorities_two = [];


/**
 * Part 1: Individual Priorities based on "item" (letter) appearing in both halves of the string
 */

// For each Elf...
foreach ( $rows as $row ) {

	// Get the size of their rucksack
	$size = strlen($row);

	// Split the items into to groups at the midpoint, $size / 2
	$split = str_split($row, $size / 2);

	// Now we need to find what letter exists in both
	// array_unique trims the values so only one remains if there are duplicates
	// - array_intersect gives us an array of values that are present in both arrays
	// - - We need to break up the strings ($split[0], and $split[1] ), so we str_split the string by the default size of 1. It's like an explode without a delimiter
	$match = array_unique( array_intersect( str_split($split[0]), str_split($split[1]) ) );

	// Now we know only one letter exists between the two, so we can just get the value of that lone letter
	$first = array_pop( $match );

	// To assign a priority, we need to determine if the letter is uppercase or lowercase by searching if that letter exists as a key in $lower. 
	// If not then it must be within $upper
	if ( array_key_exists( $first, $lower ) ) {

		// $lower starts at 0, but we need a to be 1, so we'll get the value in the array and add 1
		$priorities[] = $lower[$first] + 1;

	} else {

		// $upper starts at 0, but we need A to be 27 since lower is 1-26. 
		// We'll get the value in the array and add 1
		$priorities[] = $upper[$first] + 27;
	}
}

// Add up all the numbers in the array, and give us the total
$total = array_sum( $priorities );

echo PHP_EOL . 'Day 3: Rucksack Reorganization' . PHP_EOL;
echo 'Individual Total: ' . $total . PHP_EOL;


/**
 * Part 2: Grouped Priorities based on "item" (letter) appearing in all three Elf's sacks
 */


// We need to move the rows into groups of three, so well set starting variables for $r and $g (row and group)
// We'll also make a new array to add each elf into a group
$r = 0;
$g = 0;
$groups = [];

// For each elf...
foreach ( $rows as $row ) {

	// Add the rucksack values to the $groups array, within a $group
	$groups[$g][] = $row;

	// Increase the row number;
	$r++;

	// If this was the third elf in the group...
	if ( 3 === $r ) {

		// Increase the $g (group) number by one, and set the row back to zero so we can count to three again
		$g++;
		$r = 0;
	}
}

// For each group of three elves...
foreach ( $groups as $group ) {

	// Similar to part one, we will split each rucksack into an array of all of the characters
	// This will then pass into array_intersect to get the characters that appear in all three groups
	// This gets sent to array_unique to only get one value
	$match = array_unique( array_intersect( str_split( $group[0] ), str_split( $group[1] ), str_split( $group[2] ) ) );

	// Again, get the value of that lone letter
	$first = array_pop( $match );

	// Apply the same logic to get the value of that letter, a-z = 1-26, A-Z = 27-25
	if ( array_key_exists( $first, $lower ) ) {
		$priorities_two[] = $lower[$first] + 1;
	} else {
		$priorities_two[] = $upper[$first] + 27;
	}
}

$total_two = array_sum( $priorities_two );
echo 'Grouped Total: ' . $total_two . PHP_EOL . PHP_EOL;