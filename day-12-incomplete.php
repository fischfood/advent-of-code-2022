<?php

/**
 * Day 11: Monkey in the Middle
 */

// The usual
$data = file_get_contents('data/data-12.txt');
//$data = file_get_contents('data/data-12-sample.txt');

$rows = explode("\n", $data);

$display = true;

$alphabet = array_merge( ['S'], range('a', 'z'), ['E'] );
$start = [];
$pos_of_letters = [];
$possible_paths = [];
$change_directions = [ [1,0],[0,1],[-1,0],[0,-1] ];
$visited = [];
$exclude = [];

$shortest_path = count( str_split( $data ) );

function find($char) {

    global $rows;
    foreach ( $rows as $y => $row ) {

        if ( false !== strpos($row, $char) ){
            return [ strpos($row, $char), $y ];
        }
    }
}

foreach ( $rows as $y => $row ) {
    $x = 0;

    $individual_letters = str_split( $row );
    foreach ( $individual_letters as $letter ) {
        $letter_to_num = array_search( $letter, $alphabet );

        $pos_of_letters[$letter_to_num][] = $x . ',' . $y;
        $x++;        
    }
}

ksort( $pos_of_letters );

$checked = [];

function continue_trek( $check_these, $visited ) {
    global $alphabet, $change_directions, $display, $exclude, $possible_paths, $rows, $shortest_path;
    $i = 0;

    // if ( 2000 < count($visited) ) {
    //     return;
    // }

    foreach ( $check_these as $this_coords_string ) {

        $this_coords = explode(',', $this_coords_string );
        $next_to_check = [];

        $this_letter = array_search( $rows[$this_coords[1]][$this_coords[0]], $alphabet );
        $indent = count( $visited ) * 0 . 'px';

        if ( $display ) {
            echo "<div style='padding-left: $indent'><h2>$this_coords_string ( $alphabet[$this_letter] )</h2>";
        }

        $viable = 0;

        if ( count( $visited ) >= $shortest_path ) {
            if ( $display ) {
                echo "Too Big</div>";
            }
            continue;
        }


        foreach ( $change_directions as $direction ) {

            $finished = false;
        
            $new_x = $this_coords[0] + $direction[0];
            $new_y = $this_coords[1] + $direction[1];

            $new_coords = $new_x . ',' . $new_y;

            if ( in_array( $new_coords, $exclude ) ) {
                continue;
            }

            if ( $new_x < 0 || $new_x > count( str_split( $rows[0] ) ) - 1 ) {
                continue;
            }

            if ( $new_y < 0 || $new_y > count( $rows ) - 1 ) {
                continue;
            }

            $next_letter = array_search( $rows[$new_y][$new_x], $alphabet );

            if ( $this_letter <= $next_letter ) {

                $viable++;

                $i++;

                if ( $next_letter === 27 ) {
                    echo 'Found Path - ' . sizeof( $visited ) . '<br>';
                    $possible_paths[] = $visited;

                    if ( sizeof( $visited ) < $shortest_path ) {
                        $shortest_path = sizeof( $visited );
                    }

                    $finished = true;
                    break;

                } else {

                    if ( ! in_array( $new_coords, $visited ) ) {
                        $next_to_check[ $alphabet[$next_letter] . str_pad($i, 4, '0', STR_PAD_LEFT) ] = $new_coords;
                        $new_visited = array_merge( $visited, [$new_coords] );
                    }
                }

            }
        }

        if ( $display ) {
            if ( ! empty( $next_to_check ) ) {
                echo 'Checking: ' . implode(' | ', $next_to_check );
                echo ' (' . count( $visited ) . ')';
            } else {
                if ( ! $finished ) {
                    $exclude[] = $this_coords_string;
                    echo 'Dead End ' . $this_coords_string;
                }
            }

            echo '</div>';
        }

        if ( ! empty( $next_to_check ) ) {
            ksort( $next_to_check );
            //print_r( $next_to_check );
            continue_trek( $next_to_check, $new_visited );
        }

    }
}
    


function start_trek() {
    global $alphabet, $change_directions, $display, $pos_of_letters, $rows, $visited;

    $start_pos = $pos_of_letters[0][0];
    $start_coords = explode( ',', $start_pos );
    $next_to_check = [];

    if ( $display ) {
        echo '<h1 style="margin: 0;">Start</h1>';
        echo "<small>$start_pos</small><br><br>";
    }

    $a_or_b = 1;

    // Check each direction from 'S'
    foreach ( $change_directions as $direction ) {

        $new_x = $start_coords[0] + $direction[0];
        $new_y = $start_coords[1] + $direction[1];

        // If out of bounds, skip
        if ( $new_x < 0 || $new_x > count( str_split( $rows[0] ) ) - 1 ) { continue;}
        if ( $new_y < 0 || $new_y > count( $rows ) - 1 ) { continue; }

        $next_coords = $new_x . ',' . $new_y;

        // Get the numberical value of the letter at these coordinates
        $maybe_b = array_search( $rows[$new_y][$new_x], $alphabet );

        // If this is a B, skip A
        if ( $maybe_b < 3 ) {
            $next_to_check[] = $next_coords;
        }
    }

    $visited[] = $start_pos;

    if ( $display ) {
        echo 'Checking: ' . implode(' | ', $next_to_check );
    }

    continue_trek( $next_to_check, $visited );
}

start_trek();

echo '<br><br>';

// Add in the starting
$total_path = $shortest_path + 1;

echo "The shortest path takes $total_path steps";
        

// print_r( $path );

// echo array_sum( $path );

/**
function check_neighbors( $this_coords, $letter_num, $visited, $coordinates, $count ) {
    global $rows, $path, $pos_of_letters, $alphabet, $checked, $change_directions;


    if ( !in_array( $coordinates, $checked ) ) {

        if ( $letter_num === 0 ) {
            $next = 2;
        } else {
            $next = $letter_num + 1;
        }

        if ( $letter_num === 27 ) {
            return;
        }

        $checked[] = $this_coords;

        foreach ( $change_directions as $direction ) {

            $new_x = $this_coords[0] + $direction[0];
            $new_y = $this_coords[1] + $direction[1];

            $next_coords = $new_x . ',' . $new_y;

            if ( $new_x < 0 || $new_x > count( str_split( $rows[0] ) ) - 1 ) {
                continue;
            }

            if ( $new_y < 0 || $new_y > count( $rows ) - 1 ) {
                continue;
            }

            if ( ! in_array( $next_coords, $visited ) ) {

                //echo $new_x . ' - ' . $new_y;

                $next_letter_num = array_search( $rows[$new_y][$new_x], $alphabet );

                if ( $letter_num > $next || $letter_num < $next - 1 ) {
                    continue;
                }


                echo '<br>';
                $visited[] = $next_coords;
                
                if ( in_array( $next_coords, $pos_of_letters[$next] ) ) {
                    echo 'Next ' . $alphabet[$next] . ' is ' . $next_coords . ' (From ' . $alphabet[$letter_num] . ' at ' . $coordinates . ')';
                    // echo $count;
                    echo '<br>';
                    print_r( $visited );
                    echo '<br>';

                    if ( array_key_exists( $alphabet[$next], $path ) ) {
                        $old_count = $path[ $alphabet[$next] ];
                        if ( $old_count > $count ) {
                            $path[$alphabet[$next]] = $count;
                        }
                    } else {
                        $path[$alphabet[$next]] = $count;
                    }


                } else {
                    //echo $next_coords . '<br>';
                    $count++;
                    check_neighbors( $next_coords, $letter_num, $visited, $this_coords, $count );
                }
            }
        }
    }
}
*/