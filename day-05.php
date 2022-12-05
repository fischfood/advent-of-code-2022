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

	// Skip empty row and numerical row
	if ( empty( $row ) || substr( trim($row), 0, 1 ) === '1' ) {
		continue;
	}

	// If a row starts with '[', meaning there are boxes in that row...
	if ( substr( trim($row), 0, 1 ) === '[' ) {

		// Break apart the row into boxes

		// For testing, we can convert every four spaces into a box '[-]' so it won't show empty, then replace all of the remaining spaces
		// We can test this with:
		   // print_r( str_replace( ' ' , '', str_replace( '    ', '[-]', $row ) ) ); echo PHP_EOL;

		// For actual data, we'll replace every four spaces into an empty box
		// Around that, we'll remove all spaces and the left side of the box (the opening bracket);
		// Now well use those closing brackets to act as the delimiter for where we want to put this into an array
		$row_of_boxes = explode( ']', str_replace( ['[', ' '], '', str_replace( '    ', '[]', $row ) ) );

		// Now we'll move them into proper stacks, $columns(_two)
		// For each row of box...
		foreach ( $row_of_boxes as $col => $letter ) {

			// Since we set a delimiter of ']', it adds and extra item at the end since it assumed there was something before and after the last bracket
			// So we'll make sure we only get boxes that are within the total amount of columns
			if ( ( $col + 1 ) < count( $row_of_boxes ) ) {

				// If the array doesn't exist, we can't pass anything to it
				if ( ! array_key_exists( $col + 1, $columns ) ) {
					$columns[$col + 1] = [];
					$columns_two[$col + 1] = [];
				}

				// Only add the box to the column if it contains a letter
				// Since we're going from top to bottom, we need to add boxes to the beginning of the array
				if ( '' !== $letter ) {
					array_unshift( $columns[$col + 1], $letter );
					array_unshift( $columns_two[$col + 1], $letter );
				}
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



