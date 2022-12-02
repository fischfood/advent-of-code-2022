<?php

/**
 * Day 2: Rock Paper Scissors
 */

$data = file_get_contents('data/data-02.txt');
$rows = explode("\n", $data);
$points = [];

// Create an array of all the letters of the alphabet assigned to numbers. 
// Why do this in six lines when you can do it in one and offset later?
$alphabet = range('A', 'Z');


/**
 * Part One - Calculate your points throughout a game of RPS, where your hand and your opponents hand are defined
 */

// A for Rock, B for Paper, and C for Scissors
// X for Rock, Y for Paper, and Z for Scissors
// 0 Lost, 3 Tie, 6 Win

// For each round...
foreach ($rows as $game) {

	// Break the row into two hands
	$results = explode(' ', $game );

	// Set opponent hand to first value, set my hand to second. 
	// Add 1 to offset the alphabet array starting at 0
	$opponents_hand = array_search( $results[0], $alphabet ) + 1;

	// Subtract 23 to bring my 24, 25, 26 in line with 1, 2, 3 for comparisons, plus the offset of 1
	$my_hand = array_search( $results[1], $alphabet ) - 22;

	// You could also compare with a modulo...
	// $outcome = (( $my_hand + 3 ) - $opponents_hand) % 3;
	// But for easier reading, we'll write everything out

	// If I lose...
	// When my hand is one less than $opponents or two higher (in the event of $opponents_hand - 1 being 0)...
	if ( $my_hand === $opponents_hand - 1 || $my_hand === $opponents_hand + 2 ) {
		$points[] = $my_hand + 0;
	}

	// If I Tie...
	// When my hand and opponents hand are the same...
	if ( $opponents_hand === $my_hand ) {
		$points[] = $my_hand + 3;
	}

	// If I Win
	// When my hand is one more than $opponents or two lower (in the event of $opponents_hand + 1 being 4)...
	if ( $my_hand === $opponents_hand + 1 || $my_hand === $opponents_hand - 2 ) {
		$points[] = $my_hand + 6;
	}

}

// Total up all the points from the games
$total = array_sum( $points );

echo PHP_EOL . 'Day 2: Rock Paper Scissors' . PHP_EOL;
echo 'Defined Hand total: ' . $total . PHP_EOL;

/**
 * Part Two - Calculate your points throughout a game of RPS, where your hand and your opponents hand are defined
 */

// A for Rock, B for Paper, and C for Scissors
// X for Lose, Y for Tie, and Z for Win
// 0 Lost, 3 Tie, 6 Win

$points_two = [];

// Same setup as before, but now my hand value doesn't matter points wise
// For each round...
foreach ($rows as $game_two) {

	// Break the row into two hands
	$results_two = explode(' ', $game_two );

	// Set opponent hand to first value, set my hand to second. 
	// Add 1 to offset the alphabet array starting at 0
	$opponents_hand_two = array_search( $results_two[0], $alphabet ) + 1;

	// If I should lose
	if ( 'X' === $results_two[1] ) {
		// Set my hand score to one lower than opponents
		// In the event my hand is now a 0, set my hand value to 3
		$my_hand = ( 0 === ( $opponents_hand_two - 1 ) ) ? 3 : $opponents_hand_two - 1;
		$points_two[] = $my_hand + 0;
	}

	// If I should tie
	if ( 'Y' === $results_two[1] ) {
		// Set my hand value to the same as $opponents
		// Give me three extra points for the tie
		$my_hand = $opponents_hand_two;
		$points_two[] = $opponents_hand_two + 3;
	}

	// If I should win
	if ( 'Z' === $results_two[1] ) {
		// Set my hand score to one higher than opponents
		// In the event my hand is now a 4, set my hand value to 1
		// Give me an additional six points for the win
		$my_hand = ( 4 === ( $opponents_hand_two + 1 ) ) ? 1 : $opponents_hand_two + 1;
		$points_two[] = $my_hand + 6;
	}

}

$total_two = array_sum( $points_two );
echo 'Defined Outcome Total: ' . $total_two . PHP_EOL . PHP_EOL;