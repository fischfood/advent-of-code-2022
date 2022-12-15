<?php
$starttime = microtime(true);

/**
 * Day 15:
 */

// The usual
$data = file_get_contents('data/data-15.txt');
$data = file_get_contents('data/data-15-sample.txt');

$rows = explode("\n", $data);

$sensors = [];
$beacons = [];
$sb_distance = [];

// The we are looking for in part one
$needed_row = 2000000; // Real
$needed_row = 10; // Sample

foreach ( $rows as $row ) {

    // Break row into Sensor and Beacon positions
    $sensor_beacon = explode( ':', str_replace( ['Sensor at ',' closest beacon is at ', 'x=', ' y='], '', $row ) );
    $this_sensor = explode( ',', $sensor_beacon[0] );
    $this_beacon = explode( ',', $sensor_beacon[1] );

    // Calculate the Manhattan Distance (x-x , y-y), always positive
    $distance = abs( $this_sensor[0] - $this_beacon[0] ) + abs( $this_sensor[1] - $this_beacon[1] );

    // By default we don't want to add this to the group
    $add = false;

    // If this sensor has an Y position greater (or equal to) than the needed row, and the distance will make it enter that line, we want to add it
    if ( $this_sensor[1] >= $needed_row && ($this_sensor[1] - $distance) <= $needed_row ) {
        $add = true;
    }

    // If this sensor has an Y position less than the needed row, and the distance will make it enter that line, we want to add it
    if ( $this_sensor[1] < $needed_row && ($this_sensor[1] + $distance) >= $needed_row ) {
        $add = true;
    }

    // If it matches one of the above, add it Add this to the array
    if ( $add ) {
        $sensors[] = $sensor_beacon[0];
        $beacons[] = $sensor_beacon[1];
        $sb_distance[] = $distance;
    }
}

/**
 * Part One
 */

function part_one() {

    global $beacons, $covered_locations, $needed_row, $sb_distance, $sensors;


    // For every beacon that has a distance that passes the needed row...
    foreach( $sb_distance as $key => $distance ) {

        // Set a false so we don't add it unless the conditions are met
        $in_row_needed = false;

        // Geet the coordinates of the sensor
        $sensor = explode( ',', $sensors[$key] );
        $sensor_x = $sensor[0];
        $sensor_y = $sensor[1];

        // If the sensor row is greater than the needed row...
        if ( $sensor_y > $needed_row ) {

            // Get the furthest distance past $needed
            $min_point = $sensor_y - $distance;

            /** How many rows will pass into row $needed?
             * 
             * Convert to a triangle
             * 
             *      1
             *     212
             *    32123 ---- Needed Row
             * 
             * The tip is two away from the row needed, so 2 left + 2 right
             * so 1 (the X ) + (distance away * 2)
             */

            $in_row_needed = ( 1 + ( ( $needed_row - $min_point ) * 2 ) );

        }

        // Repeat if less than...
        if ( $sensor_y < $needed_row ) {
            
            $max_point = $sensor_y + $distance;
            $in_row_needed = ( 1 + ( ( $max_point - $needed_row ) * 2 ) );

        }

        // If it's in the row, we just need the distance on both sides
        if ( $sensor_y === $needed_row ) {
            $in_row_needed = $distance * 2;
        }

        // If we have points in the row (which if this function is running, we should anyway)
        if ( $in_row_needed ) {
            $needed_left_right = floor( $in_row_needed / 2 );

            for ( $x = ($sensor_x - $needed_left_right); $x <= ($sensor_x + $needed_left_right); $x++ ) {

                $this_coords = $x . ',' . $needed_row;

                if ( ! in_array( $this_coords, $beacons ) ) {
                    //echo '<br>' . $this_coords;
                    $covered_locations[] = $this_coords;                  
                } else {
                    //echo '<br>Found a beacon!';
                }

            }
        }

    }

    echo count( $covered_locations );
    echo '<br>';

    echo count( array_unique( $covered_locations ) );

}

/**
 * Part Two
 */

function part_two() {

}


//echo 'Day 14: Regolith Reservoir';
part_one();
part_two();
echo '<br>Total time to generate: ' . (microtime( true ) - $starttime);