<style type="text/css">
    body {
        margin: 0;
        padding: 0;
    }
    div {
        display: inline;
        flex: 0 0 150px;
        padding: 1rem

    }
    .container {
        display: flex;
        width: 100vw;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        box-sizing: border-box;
    }
</style>

<?php
$starttime = microtime(true);

/**
 * Day 14: Regolith Reservoir
 */

// The usual
$data = file_get_contents('data/data-14.txt');
$data = file_get_contents('data/data-14-sample.txt');

// Show grid and math?
$display = true;

$rows = explode("\n", $data);

// Set the start point of the sand funnel
$start = [500,0];

// This is where already set rocks and sand will be added
$rocks = [];
$sand_positions = [];

// Set min/max values to compare to and change size of cave
$min_x = 500;
$max_x = 500;
$max_y = 0;

/**
 * Create Rock Positions
 * 
 * This runs through the input and adds every rock to the grid
 * 
 * If floor is set, it also adds an "infinite" floor
 * To save processing time, we make the floor as wide as the rock grid +/- 2 sections
 * This way, it's all contained in a bucket and we can add the cascading triangles of the pyramid after
 */
function create_rock_positions( $floor = false ) {

    global $max_x, $max_y, $min_x, $rocks, $rows;

    foreach ($rows as $key => $row) {
        $coords = explode( ' -> ', $row );

        for ( $r = 0; $r < sizeof( $coords ) - 1; $r++ ) {

            $this_coords = explode( ',', $coords[$r] );
            $next_coords = explode( ',', $coords[$r + 1] );

            $this_x = $this_coords[0];
            $this_y = $this_coords[1];

            $next_x = $next_coords[0];
            $next_y = $next_coords[1];

            if ( $this_x === $next_x ) {
                foreach( range( $this_y, $next_y) as $y ) {
                    $rocks[] = $this_x . ',' . $y;

                    if ( $y > $max_y ) {
                        $max_y = $y;
                    }
                }
            }

            if ( $this_y === $next_y ) {
                foreach( range( $this_x, $next_x) as $x ) {
                    $rocks[] = $x . ',' . $this_y;

                    if ( $x > $max_x ) {
                        $max_x = $x;
                    }

                    if ( $x < $min_x ) {
                        $min_x = $x;
                    }
                }
            }
        }
    }

    if ( $floor ) {
        $max_y = $max_y + 2;
        $min_x = $min_x - 2;
        $max_x = $max_x + 2;

        for ( $f = $min_x; $f <= $max_x; $f++ ) {
            $rocks[] = $f . ',' . $max_y;
        }

        for ($y = 0; $y <= $max_y; $y++ ) {
            $rocks[] = $min_x . ',' . $y;
            $rocks[] = $max_x . ',' . $y;
        }
    }
}

/**
 * Build Grid
 * 
 * If $display is set to true, Line 34, this will output the location of sand after each granule has reached its resting place
 * If you are doing this for the non-sample data, I'd recommend only firing this on the last item
 */

function build_grid() {

    global $display, $max_x, $max_y, $min_x, $rocks, $sand_positions;

    // TEST: If you want to see the whole output
    // Great for the sample data...intensive for the real data
    if ( $display ) {
    
        echo '<div>';

        // Current granule of sand number
        echo 'Sand = ' . count( $sand_positions );
        echo '<pre>';

        // Create 3 rows of data in column format
        // Output the min and max as vertical text, as well as the start point
        for ( $y = 0; $y < 3; $y++ ) {

            for ( $x = $min_x - 4; $x <= $max_x; $x++ ) {
                if ( $x === $min_x ) {
                    echo str_split( $min_x )[$y];
                } else if ( $x === $max_x ) {
                    echo str_split( $max_x )[$y];
                } else if ( $x === 500 ) {
                    echo str_split( 500 )[$y];
                } else {
                    echo ' ';
                }
            }
            echo '<br>';
        }

        echo '<br>';

        // Loop through the entire grid rows, using the bounds as stopping points
        for ( $y = 0; $y <= $max_y; $y++ ) {

            // Output value of Y
            echo str_pad($y, 3, 0, STR_PAD_LEFT ) . ' ';

            // Build each row starting from minimum to maximum values
            for ( $x = $min_x; $x <= $max_x; $x++ ) {

                $this_coords = $x . ',' . $y;

                // Sand Locations
                // Most Recent is shown in brown, all previous in gray
                if ( in_array( $this_coords, $sand_positions ) ) {
                    if ( $this_coords === end( $sand_positions ) ) {
                        echo '<span style="color: brown;">O</span>';
                    } else {
                        echo '<span style="color: #bbb">O</span>';
                    }

                // Start point, show a funnel
                } else if ( $this_coords === '500,0') {
                    echo 'V';

                // Location of all rocks, including floor if Part 2
                } else if ( in_array( $this_coords, $rocks ) ) {
                    echo '#';

                // Shows the line sand would flow if there were no obstructions
                } else if ( strpos( $this_coords, '500,') === 0 ) {
                    echo '|';

                // Otherwise, if it's empty, show an open space
                } else {
                    echo '<span style="color: #ddd;">.</span>';
                }
            }
            echo '<br>';
        }

        echo '</pre></div>';
    }
}

/**
 * Let the sand flow
 * 
 * Logic checks for where the next sand block should exist
 * 
 * For Part One, this will end when a block of sand is starting outside of the grid
 * For Part Two, this will end when the sandblock reaches the funnel
 */

function let_the_sand_flow( $flow = false ) {

    global $display, $max_x, $max_y, $min_x, $rocks, $sand_positions;

    // Check three base positions
    $flow_center = $flow[0] . ',' . $flow[1];
    $flow_left = ($flow[0] - 1) . ',' . $flow[1];
    $flow_right = ($flow[0] + 1) . ',' . $flow[1];

    // If we pass specifics from above, these are the next ones to look for
    $check_center_down = [$flow[0], $flow[1] + 1];
    $check_left_down = [$flow[0] - 1, $flow[1] + 1];
    $check_right_down = [$flow[0] + 1, $flow[1] + 1];

    // If the X of this position is less or more than the starting grid bounds, or Y is below the maximum, we've reached the end
    // Stop the recursion
    if ( $flow[0] < $min_x || $flow[0] > $max_x || $flow[1] > $max_y ) {

        if ( $display ) {
            echo 'No more sand';
        }

        return;
    }

    // Is the position we're checking filled?
    // Also the left of this position, and the right?
    $center_filled = ( in_array( $flow_center, $sand_positions) || in_array( $flow_center, $rocks ) ) ? true : false;
    $left_filled = ( in_array( $flow_left, $sand_positions) || in_array( $flow_left, $rocks ) ) ? true : false;
    $right_filled = ( in_array( $flow_right, $sand_positions) || in_array( $flow_right, $rocks ) ) ? true : false;

    // If center, left, and right are filled, it's a solid surface
    // We can stack sand here
    if ( $center_filled && $left_filled && $right_filled ) {

        // Next placement position
        $flow_up = $flow[0] . ',' . ($flow[1] - 1);
        
        // Add sand to center, one higher
        $sand_positions[] = $flow_up;
        build_grid();


        // If the coordinates of this are the same as the start point, we can't add any more sand
        // Stop the recursion
        if ( $flow_up === '500,0' ) {

            if ( $display ) {
                echo 'Funnel is blocked';
            }

            return;
        }

        // If we're not blocking the funnel
        // Start a new sand block from the funnel to flow down
        $flow_from_start = [500, 0];
        let_the_sand_flow( $flow_from_start );

    // If either center, left, or right are not filled, this sand can keep moving downhill
    } else {

        // If the center block is not filled with sand or a rock, keep flowing downwards
        if ( ! in_array( $flow_center, $sand_positions) && ! in_array( $flow_center, $rocks ) ) {

            let_the_sand_flow( $check_center_down );

        // If the center is filled, check the left side
        } else if ( ! in_array( $flow_left, $sand_positions) && ! in_array( $flow_left, $rocks ) ) {

            let_the_sand_flow( $check_left_down );

        // If both center and left are filled, check right (right has to be open, or it would have hit the all filled chunk before)
        } else if ( ! in_array( $flow_right, $sand_positions) && ! in_array( $flow_right, $rocks ) ) {
            
            let_the_sand_flow( $check_right_down );

        }

    }

}



/**
 * Part One
 */

function part_one() {
    global $sand_positions;

    // Build a side scrolling container for when $display = true;
    echo '<div class="container">';

    create_rock_positions();
    build_grid();

    // Start at the funnel
    $start = [500, 0];
    let_the_sand_flow( $start );

    echo '</div>';

    echo 'Total sand granules until waterfall: ' . count( $sand_positions );
}

/**
 * Part Two
 */

function part_two() {

    global $display, $max_x, $max_y, $min_x, $sand_positions;

    // Start Sand Positions over
    $sand_positions = [];

    // New side scrolling container
    echo '<div class="container">';

    // This will add the floor / bucket
    create_rock_positions( 'Now with 100% more floor!');
    build_grid();

    // Start at the funnel
    $start = [500, 0];
    let_the_sand_flow( $start );

    echo '</div>';

    // When let_the_sand_flow is complete, it will give us sand within the bucket
    // We did this to save time and allow it to process (Sample data is two seconds this is over 10 minutes to run even without displaying )
    // There must be a way to make it more efficient, but for now...

    // Get the distance from the funnel of the min-X and max-X coordinate
    $offset_min = (500 - $min_x);
    $offset_max = ($max_x - 500);

    // These numbers will be the amount of columns needed, example if it's 4:
    //     4
    //    34
    //   234
    //  1234

    $min_col_needed = $max_y - $offset_min;
    $max_col_needed = $max_y - $offset_max;

    // Calculate how many squares we need to fill that don't exist
    // 1 + 2 + 3 + 4
    // or 1+4 + 2+3
    // or 5 * 2
    // (Smallest + Largest) * (Half the amount of columns)

    $min_total = ( ( $min_col_needed + 1 ) * ( $min_col_needed / 2 ) );
    $max_total = ( ( $max_col_needed + 1 ) * ( $max_col_needed / 2 ) );

    if ( $display ) {
        echo 'Capped total: ' . count( $sand_positions ) . '<br>';
        echo 'Min X Cap: ' . $min_x . ' - offset: ' . $offset_min . '<br>';
        echo 'Max X Cap: ' . $max_x . ' - offset: ' . $offset_max . '<br>';
        echo 'Total Y: ' . $max_y . '<br><br>';

        echo 'We need ' . $min_col_needed . ' triangles to the left (' . $min_total . ')<br>';
        echo 'We need ' . $max_col_needed . ' triangles to the right(' . $max_total . ')<br><br>';
    }

    echo 'Total sand granules on the floor: ' . ( count( $sand_positions ) + $min_total + $max_total ) . '<br><br>';
}


echo 'Day 14: Regolith Reservoir';
part_one();
part_two();
echo '<br>Total time to generate: ' . (microtime( true ) - $starttime);