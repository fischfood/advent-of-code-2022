<?php

/**
 * Day 5: Supply Stacks
 */

$data = file_get_contents('data/data-05.txt');
$rows = explode("\n", $data);

// Create arrays for both parts
$columns = [];
$columns_two = [];

// For each row...
foreach( $rows as $row ) {

	// If a row starts with '[', meaning there are boxes in that row...
	if ( substr( trim($row), 0, 1 ) === '[' ) {

		// Break apart the row into boxes

		// First, we'll convert every four spaces into a non-character box '[-]', then remove all remaining spaces
		$row_of_boxes = str_replace(' ', '', str_replace( '    ', '[-]', $row ) );

		// If you'd like to see this for testing, uncomment out the line below

		   // print_r( $row_of_boxes ); echo PHP_EOL;

		// After that, we'll remove all brackets, then break this apart per character
		// We'll keep the dashes in for structure, but exclude the dashes from going into columns later

		$row_of_box_contents = str_split( str_replace( ['[', ']'], '', $row_of_boxes ) );
		
		// Now we'll move them into proper stacks, $columns(_two)
		// For each row of box...
		foreach ( $row_of_box_contents as $col => $letter ) {

			// If the array doesn't exist, we can't pass anything to it
			if ( ! array_key_exists( $col + 1, $columns ) ) {
				$columns[$col + 1] = [];
				$columns_two[$col + 1] = [];
			}

			// Only add the box to the column if it contains a letter
			// Since we're going from top to bottom, we need to add boxes to the beginning of the array
			if ( '-' !== $letter ) {
				array_unshift( $columns[$col + 1], $letter );
				array_unshift( $columns_two[$col + 1], $letter );
			}
		}

	}


	// Do the moving...
	if ( substr( trim($row), 0, 4 ) === 'move' ) {

		// Break the instructions into data
		$to_do = explode( ' ', $row );

		// Move, [this many], from, [this column], to, [this column]
		$move = $to_do[1];
		$from = $to_do[3];
		$to = $to_do[5];

		// For Part two, we need to move multiple boxes at a time;
		$multi_move = [];

		// While there still are moves to do...
		while ( $move > 0 ) {

			/**
			 * Part One Movements
			 */

			// Get the last (top) box in the column
			$top_box = end( $columns[$from] );

			// Remove it from this column
			array_pop( $columns[$from] );

			// Add it to the end of the new column
			$columns[$to][] = $top_box;


			/**
			 * Part Two Movements
			 */

			// Get the last (top) box in the column
			$top_box_two = end( $columns_two[$from] );
		
			// Remove it from this column
			array_pop( $columns_two[$from] );

			// Now we'll need to add it to a temporary array so we can move boxes in the order they were placed
			// Move each subsequent box to the bottom of the stack
			array_unshift( $multi_move, $top_box_two );

			/**
			 * Both Parts
			 */

			// Decrease the moves so we eventually stop. 
			// I Forgot this at one point and wondered why my browser kept loading forever...
			$move--;

		}

		/**
		 * Part Two Movements
		 */

		// Now that our multi_move group has all of the boxes we need to move at once
		// We'll add them one by one to the end of the new column
		foreach ( $multi_move as $box_move ) {
			$columns_two[$to][] = $box_move;
		}
	}
}

echo PHP_EOL . 'Day 5: Supply Stacks' . PHP_EOL;

echo 'Single Move Results: ';
foreach( $columns as $col_num => $final_col ) {
	echo end( $final_col );
}
echo PHP_EOL;

// Uncomment to see stacks visually

// foreach( $columns as $col_num => $final_col ) {
// 	echo '[' . end( $final_col ) . ']';
// }
// echo PHP_EOL;
// foreach( $columns as $col_num => $final_col ) {
// 	echo ' ' . $col_num . ' ';
// }
// echo PHP_EOL . PHP_EOL;


echo 'Multiple Move Results: ';
foreach( $columns_two as $col_num => $final_col ) {
	echo end( $final_col );
}
echo PHP_EOL;

// Uncomment to see stacks visually
// echo PHP_EOL;
// foreach( $columns_two as $col_num => $final_col ) {
// 	echo '[' . end( $final_col ) . ']';
// }
// echo PHP_EOL;
// foreach( $columns_two as $col_num => $final_col ) {
// 	echo ' ' . $col_num . ' ';
// }

echo PHP_EOL . PHP_EOL;



