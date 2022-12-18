<?php
$starttime = microtime(true);

/**
 * Day 18:
 */

// The usual
$data = file_get_contents('data/data-18.txt');
//$data = file_get_contents('data/data-18-sample.txt');
//$data = file_get_contents('data/data-18-sample-custom.txt');

$rows = explode("\n", $data);

$grid = [];

$max_x = 0;
$max_y = 0;
$max_z = 0;


/**
 * Part One
 */

// Add each x,y pixel into an array within another array (z)
foreach( $rows as $row ) {
    
    $pixel = explode( ',', $row );
        $x = $pixel[0];
        $y = $pixel[1];
        $z = $pixel[2];

    if ( $x > $max_x ) {
        $max_x = $x;
    }

    if ( $y> $max_y) {
        $max_y= $y;
    }

    if ( $z > $max_z ) {
        $max_z = $z;
    }

    
    $grid[ $z ][] = $x . ',' . $y;
}

$total_visible = 0;


/**
 * Part Two
 * Check for open air (could be multiple connected blocks of air surrounded)
 */
$open_air = [];
$oa_checked_global = [];

// Build a grid of all the blocks based on min/max/value
for ( $oax = -1; $oax <= $max_x + 1; $oax++ ) {
    for ( $oay = -1; $oay <= $max_y + 1; $oay++ ) {
        for ( $oaz = -1; $oaz <= $max_z + 1; $oaz++ ) {
            $open_air[$oax . ',' . $oay . ',' . $oaz] = 0;
        }
    }
}


// Both
foreach( $grid as $layer_z => $layer ) {
    foreach ( $layer as $cube ) {
        $visible = 6;
    
        $xy = explode( ',', $cube );

        // Remove this block from the open air list
        unset( $open_air[$cube . ',' . $layer_z] );

        $x = $xy[0];
        $y = $xy[1];
        $z = $layer_z;

        // Get x,y direction changes
        $xy_prev_x = ($xy[0] - 1) . ',' . $xy[1];
        $xy_next_x = ($xy[0] + 1) . ',' . $xy[1];
        $xy_prev_y = $xy[0] . ',' . ($xy[1] - 1);
        $xy_next_y = $xy[0] . ',' . ($xy[1] + 1);


        // Check X's
        if ( in_array( $xy_prev_x, $grid[$z] ) ) {
            $visible--;
        } else {
            $open_air[ $xy_prev_x . ',' . $z ] = $open_air[ $xy_prev_x . ',' . $z ] + 1; //P2
        }

        if ( in_array( $xy_next_x, $grid[$z] ) ) {
            $visible--;
        } else {
            $open_air[ $xy_next_x . ',' . $z ] = $open_air[ $xy_next_x . ',' . $z ] + 1; //P2
        }

        // Check Y's
        if ( in_array( $xy_prev_y, $grid[$z] ) ) {
            $visible--;
        } else {
            $open_air[ $xy_prev_y . ',' . $z ] = $open_air[ $xy_prev_y . ',' . $z ] + 1; //P2
        }

        if ( in_array( $xy_next_y, $grid[$z] ) ) {
            $visible--;
        } else {
            $open_air[ $xy_next_y . ',' . $z ] = $open_air[ $xy_next_y . ',' . $z ] + 1; //P2
        }

        // Check Z's

        if ( array_key_exists( $z - 1, $grid ) ) {
            if ( in_array( $cube, $grid[$z - 1 ] ) ) {
                $visible--;
            } else {
                $open_air[ $cube . ',' . ($z - 1) ] = $open_air[ $cube . ',' . ($z - 1) ] + 1; //P2
            }
        }

        if ( array_key_exists( $z + 1, $grid ) ) {
            if ( in_array( $cube, $grid[$z + 1 ] ) ) {
                $visible--;
            } else {
                $open_air[ $cube . ',' . ($z + 1) ] = $open_air[ $cube . ',' . ($z + 1) ] + 1; //P2
            }
        }

        $total_visible = $total_visible + $visible;
    }
}


$open_air_non_zero = array_filter( $open_air );
$open_air_totals = array_count_values( $open_air_non_zero );

// print_r( array_count_values( $open_air ) );

$trapped_air_blocks = array_count_values( $open_air )[6];

echo 'Total Sides Open: ' . $total_visible . '<br>';


echo 'Fully Trapped Singular Air "Blocks": ' . $trapped_air_blocks . '<br>';
//echo 'Total Sides Open (Not Trapped): ' . ( $total_visible - ( $trapped_air_blocks * 6 ) );

echo '<br>Total time to generate: ' . (microtime( true ) - $starttime);