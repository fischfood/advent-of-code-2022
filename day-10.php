<?php

/**
 * Day 10: Cathode-Ray Tube
 */

// The usual
$data = file_get_contents('data/data-10.txt');
//$data = file_get_contents('data/data-10-sample.txt');

$rows = explode("\n", $data);

// Array to record 20th, 60th, 100th, etc
$cycles = [];

$c = 1;
$value = 1;

// Part 2, begin with an array of ### followed by enough periods to fill 240 spaces.
$crt_build = array_merge( ['#','#','#'], array_fill( 0, 237, '.' ) );

// This will be our output that we break into lines
$display = '';


foreach( $rows as $row ) {

    // For Part two, we draw pixel one, draw pixel two, then shift

    // Add in the pixel for the first line (both noop or addx)
    // Every 40 pixels on the screen starts the line over
    // We will use the remainder (modulo) of 40 to calculate where in the array we will look
    $display .= $crt_build[ ($c - 1)%40 ];

    /** 
     * For Display
     */
    // echo $c . ': ' . $value . ' (' . $row . ')<br>';
    // echo implode('', $crt_build ) . '<br>';
    // echo $display . '<br><br>';

    // if the cycle is a multiple of 40, plus 20, add it to the log
    // Value multiplied by the row
    if ( ($c-20)%40 === 0 ) {
        $cycles[$c] = $value * $c;
    }

    // Increase the count
    $c++;

    // Now if this is a addx line, we have to do the second cycle
    if ( substr( trim($row), 0, 4 ) === 'addx' ) {

        // Draw the next pixel since we are at the beginning of the second cycle, same modulo logic
        $display .= $crt_build[ ($c - 1)%40 ];

        // In case the 40*n + 20 cycle happens here, we'll add it to the array
        if ( ($c-20)%40 === 0 ) {
            $cycles[$c] = $value * $c;
        }

        // Set the new value to current + whatever addx tells us to move ($row[0] = addx, $row[1] = value )
        $value = $value + explode(' ', $row)[1];

        // For Part Two
        // Since the location of the sprite is based on the middle, it's possible for the middle to start at 0, where the first pixel is off the screen
        // If this is the case, value < 1, we only need to have two pixels on screen
        // Fill the first two pixels, and make the rest blank (.)
        if ( $value < 1 ) {
            $crt_build = array_merge( ['#','#'], array_fill( 0, 238, '.' ) );

        // Otherwise, fill the array with however many empty pixels we need up until the '###' starts
        // Then fill the rest of the array with blank pixels
        } else {
            $crt_build = array_merge( array_fill( 0, $value - 1, '.'), ['#','#','#'], array_fill( 0, 237, '.' ) );
        }    

        /** 
         * For Display
         */
        // echo $c . ': ' . $value . '<br>';
        // echo implode('', $crt_build ) . '<br>';
        // echo $display . '<br><br>';

        // Increase the row again
        $c++;
    }

    //echo '<br>';
    
}

echo PHP_EOL . 'Day 10: Cathode-Ray Tube' . PHP_EOL;
echo 'Cycle Sum: ' . array_sum( $cycles ) . PHP_EOL . PHP_EOL;
echo 'Handheld Communication System: ' . PHP_EOL . PHP_EOL;

// Output the CRT Display, breaking to a new line every 40 pixels
$crt_lines = str_split( $display, 40 );

echo str_repeat(' ', 10) . 'O' . PHP_EOL;
echo str_repeat( str_repeat(' ', 10) . '|' . PHP_EOL, 2);
echo str_repeat(' ', 10) . '|' . str_repeat(' ', 21) . '_____' . PHP_EOL;
echo str_repeat(' ', 10) . '|' . str_repeat(' ', 20) . '| | | |' . PHP_EOL;
echo ' ' . str_repeat('-', 42) . PHP_EOL;
echo '| ' . str_repeat(' ', 40) . ' |' . PHP_EOL;
foreach ( $crt_lines as $output ) {
    echo '| ' . $output . ' |' . PHP_EOL;
}
echo ' ' . str_repeat('-', 42) . PHP_EOL;
echo PHP_EOL;

