<?php

/**
 * Day 9: Rope Bridge
 */

// The usual
$data = file_get_contents('data/data-09.txt');
$rows = explode("\n", $data);

// Set starting points for the head and tail, add the initial location to the "Visited" array
// coords = [x,y];
$head_coords = [0,0];
$tail_coords = [0,0];
$tail_visited = ['0x0'];

foreach( $rows as $row ) {
    $movement = explode( ' ', $row );
    // $movement[0] = Direction to move
    // $movement[1] = How many spaces to move

    $dir = $movement[0];
    
    // For each movement in this direction...
    for ( $m = 1; $m <= $movement[1]; $m++ ) {

        // Movement based on coordinates
        // Up and Down change y coord, Left and Right change X coords
        // Up and Right are positive, Down and Left are negative
        if ( 'U' === $dir ) {
            $head_coords[1] = $head_coords[1] + 1;
        } else if ( 'D' === $dir ) {
            $head_coords[1] = $head_coords[1] - 1;
        } else if ( 'L' === $dir ) {
            $head_coords[0] = $head_coords[0] - 1;
        } else if ( 'R' === $dir ) {
            $head_coords[0] = $head_coords[0] + 1;
        }
        
        // If the difference in X position is greater than one... (using absolute so both 2 and -2 work )
        if ( abs( $head_coords[0] - $tail_coords[0] ) > 1 ) {
            $tail_coords[0] = ( ( $head_coords[0] + $tail_coords[0] ) / 2 );

            // Adjust Vertical
            // If it has to move, the second direction will always match the first
            $tail_coords[1] = $head_coords[1];

        // Repeat for Y (Vertical)
        } else if ( abs( $head_coords[1] - $tail_coords[1] ) > 1 ) {

            $tail_coords[1] = ( ( $head_coords[1] + $tail_coords[1] ) / 2 );

            // Adjust Horizontal
            $tail_coords[0] = $head_coords[0];
        }

        // Add the new tail position to the visited array
        $tail_visited[] = $tail_coords[0] . 'x' . $tail_coords[1];
    }
}

echo PHP_EOL . 'Day 9: Rope Bridge' . PHP_EOL;
echo 'Total Tail Positions: ' . count( array_unique( $tail_visited ) ) . PHP_EOL;

/** Part 2 */


// Create coordinate array variables for each row, 0 (Head), 1 through 9
for($r = 0; $r <= 9; $r++) {
     ${"r_$r"} = [0,0];
}

// New starting "visited"
$long_tail_visited = ['0x0'];

foreach( $rows as $key => $row ) {

    $movement = explode( ' ', $row );
    // $movement[0] = Direction to move
    // $movement[1] = How many spaces to move

    $dir = $movement[0];
    
    // For each movement in this direction...
    for ( $m = 1; $m <= $movement[1]; $m++ ) {

        // Axis Logic
        if ( 'U' === $dir ) {
            $r_0[1] = $r_0[1] + 1;
        } else if ( 'D' === $dir ) {
            $r_0[1] = $r_0[1] - 1;
        } else if ( 'L' === $dir ) {
            $r_0[0] = $r_0[0] - 1;
        } else if ( 'R' === $dir ) {
            $r_0[0] = $r_0[0] + 1;
        }
        
        // For every segment of rope...
        for($rope_pos = 1; $rope_pos <= 9; $rope_pos++) {

            // Grab the previous position for comparison (this will be based off of all the movements that happened in this step for previous items)
            $prev = $rope_pos - 1;

            // If Current segment is more than one away from previous segment horizonally
            if ( abs( ${"r_$prev"}[0] - ${"r_$rope_pos"}[0] ) > 1 ) {

                // Current segment is the average of the old position and the new position
                // They will always be two spots apart, so the division works easily
                // This method makes it so it works for both positive and negative integers
                ${"r_$rope_pos"}[0] = ( ( ${"r_$prev"}[0] + ${"r_$rope_pos"}[0] ) / 2 );

                // Adjust Vertical
                // If the Y coordinate positions do not match...
                if ( ${"r_$prev"}[1] !== ${"r_$rope_pos"}[1] ) {
                    
                    // If it's a positive number...
                    if ( ${"r_$prev"}[1] - ${"r_$rope_pos"}[1] > 0 ) {

                        // Move this segment to the previous segments position, plus one
                        // It was always be one since the previous rope will never move more than two spots away with this logic
                        ${"r_$rope_pos"}[1] = ${"r_$rope_pos"}[1] + 1;

                    // Otherwise, if it's a negative number...
                    } else {

                        // Move this segment to the previous segments position, minus one
                        ${"r_$rope_pos"}[1] = ${"r_$rope_pos"}[1] - 1;

                    }
                }


            // Repeat for vertical movements, with horizontal adjustments as well
            // We do this as an else so if the current item was two away, and already moved one, it doesn't use this to move even closer 
            } else if ( abs( ${"r_$prev"}[1] - ${"r_$rope_pos"}[1] ) > 1 ) {

                // Current segment is the average of the old position and the new position
                ${"r_$rope_pos"}[1] = ( ( ${"r_$prev"}[1] + ${"r_$rope_pos"}[1] ) / 2 );

                // Adjust Horizontal
                // If the X coordinate positions do not match...
                if ( ${"r_$prev"}[0] !== ${"r_$rope_pos"}[0] ) {

                    // If it's a positive number...
                    if ( ${"r_$prev"}[0] - ${"r_$rope_pos"}[0] > 0 ) {

                        // Move this segment to the previous segments position, plus one
                        ${"r_$rope_pos"}[0] = ${"r_$rope_pos"}[0] + 1;

                    // If it's a negative number...
                    } else {

                        // Move this segment to the previous segments position, minus one
                        ${"r_$rope_pos"}[0] = ${"r_$rope_pos"}[0] - 1;
                    }
                }

            }

            // For the last item in the rope, section 9, we'll add the current coordinates to the visited array
            $long_tail_visited[] = $r_9[0] . 'x' . $r_9[1];
        }

    }

}

echo 'Long Rope Tail Positions: ' . count( array_unique( $long_tail_visited ) ) . PHP_EOL . PHP_EOL;
