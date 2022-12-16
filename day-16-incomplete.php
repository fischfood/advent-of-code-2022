<?php
$starttime = microtime(true);

/**
 * Day 15:
 */

// The usual
$data = file_get_contents('data/data-16.txt');
//$data = file_get_contents('data/data-16-sample.txt');

$rows = explode("\n", $data);

$valves = [];
$useful_valves = [];
$useful_valve_keys = [];

$paths = [];

$most_pressure = 0;

$flows = [];

$best_flow = 0;
$best_path = '';

function build_valves() {
    global $paths, $rows, $useful_valve_keys, $useful_valves, $valves;

    foreach ( $rows as $row ) {

        $valve_info = explode( ' ', str_replace(', ', ',', $row ) );
        // 1 - This Key
        // 4 - rate=X
        // 9 - Path to other valves
        $valve_id = $valve_info[1];
        $valve_flow = str_replace(['rate=',';'], '', $valve_info[4] );
        $valve_paths = explode( ',', $valve_info[9] );

        $valves[ $valve_id ] = [ $valve_flow, $valve_paths ];


        if ( $valve_flow > 0 || $valve_id === 'AA' ) {
            $useful_valves[ $valve_id ] = [ $valve_flow, $valve_paths ];
            $useful_valve_keys[] = $valve_id;

            $paths[ $valve_id ]['connections'] = [ $valve_id ];
        }

    }
}

function build_paths( $valves_set = false, $given_parent_key = false, $given_distance = false ) {

    global $paths, $useful_valve_keys, $valves;

    if ( ! $valves_set ) {
        $valves_set = $useful_valve_keys;
    }

    foreach ( $valves_set as $valve ) {

        $distance = ( $given_distance ) ? $given_distance : 1;
        $parent_key = ( $given_parent_key ) ? $given_parent_key : $valve;

        // Pull data from the main $valves array
        $connected_valves = $valves[$valve][1];
        $cv_size = count( $connected_valves );
        $next_check = [];

        foreach( $connected_valves as $cv ) {

            if ( ! in_array( $cv, $paths[$parent_key]['connections'] ) ) {
                $paths[$parent_key][$cv] = $distance . ',' . $valves[$cv][0];
                $paths[$parent_key]['connections'] = array_merge( $paths[$parent_key]['connections'], [$cv] );
                $next_check[] = $cv;
            }

            if ( in_array( $cv, $paths[$parent_key]['connections'] ) ) {
                if ( array_key_exists( $cv, $paths[$parent_key] ) ) {
                    if ( $distance < explode( ',', $paths[$parent_key][$cv] )[0] ) {
                        $paths[$parent_key][$cv] = $distance . ',' . $valves[$cv][0];
                    }
                }
            }
        }

        $distance++;

        if ( ! empty( $next_check ) ) {
            build_paths( $next_check, $parent_key, $distance );
        }        

    }

}

function add_pressure( $plv, $time_remaining ) {
    $path_pressure = explode(',', $plv );

    $time_to_valve = $path_pressure[0];
    $releasing = $path_pressure[1];

    // Remaining time, minus how long it takes to get to the next valve, minus one minute to turn the valve
    $time_remaining = $time_remaining - ( $time_to_valve ) - 1;

    // Multiple the pressure of this valve and the timing remaining
    $total_pressure_of_valve = $releasing * $time_remaining;
    
    return [$time_remaining, $total_pressure_of_valve];
}

function path_permutation( $items, $perms = array()  ) {
    global $flows, $most_pressure, $paths;

    if ( empty( $items ) ) { 

        $last = 'AA';

        $time = 30;
        $total_release = 0;

        foreach( $perms as $valve ) {

            if ( substr( implode('', $perms), 0, 8 ) === "TUUKEKGWJTCA" ) {

                //echo "$last -> $valve - ";
                $new_time_pressure = add_pressure( $paths[$last][$valve], $time );

                //echo "$new_time_pressure[0] - ($new_time_pressure[1]) <br>";

                $time = $new_time_pressure[0];
                $total_release = $total_release + $new_time_pressure[1];

                $last = $valve;
            }

        }

        if ( $total_release > 0 ) {
            $flows[ implode('', $perms) ] = $total_release;

            $total_time = 30 - $time;

            if ( $total_time > 30 ) {
                //echo 'TOOK TOO LONG. BOOM';
            } else {
                //echo "$total_release in $total_time seconds";
                //echo '<br><br>';

                if  ( $total_release > $most_pressure ) {
                    $most_pressure = $total_release;
                }
            }
        }

    } else {
        for ($i = count($items) - 1; $i >= 0; --$i) {
            $newitems = $items;
            $newperms = $perms;
            list($foo) = array_splice($newitems, $i, 1);
            array_unshift($newperms, $foo);
            path_permutation($newitems, $newperms);
        }
    }
}

$i = 1;

function save_the_elephants( $this_key = 'AA', $visited = false, $time_to_flow = 30, $total_flow = 0 ) {
    global $i, $best_flow, $best_path, $paths, $useful_valve_keys, $valves;

    if ( ! $visited ) {
        $visited = ['AA'];
    }

    $old_visited = $visited;

    foreach( $paths[$this_key] as $key => $path ) {

        if ( $key !== 'connections' && ! in_array( $key, $visited ) ) {

            $distance_flow = explode( ',', $path );
            $remaining_time = $time_to_flow - $distance_flow[0] - 1;

            $this_valve_til_finish = ($remaining_time * $distance_flow[1]);
            $new_total_flow = $total_flow + ( $remaining_time * $distance_flow[1] );

            // If there is time no more time remaining to get there (distance), open (1min) and, add pressure (1min)
            if ( $remaining_time <= $paths[$this_key][$key][0] ) {

                // echo '<strong>' . $i . ' - Reached End  |  Total Pressure: ' . $new_total_flow . '</strong><br>';
                // echo 'Remaining Time: ' . $remaining_time . 'sec (need ' . ($paths[$this_key][$key][0] + 3) . ' sec for change)<br><br>';
                $i++;

                $dead_end_total = 0;
                foreach( $visited as $time => $v ) {
                    $this_time = ( $v !== 'AA' ) ? explode( ',', $time )[1] : 0;
                    $this_flow = $valves[$v][0];
                    $this_total = $this_flow * $this_time;

                    //echo "Added $v ( $this_flow ) for $this_time seconds ( $this_total )<br>";
                    $dead_end_total = $dead_end_total + $this_total;
                }

                if ( $dead_end_total > $best_flow ) {
                    echo $dead_end_total . '<br>';
                    print_r( $visited );
                    echo '<br>';
                    $best_flow = $dead_end_total;
                    $best_path = implode( ',', $visited ) . ' - ' . $remaining_time;
                }

                //echo '<br>';

            } else {

                $visited[ $key . ',' . $remaining_time ] = $key;

                if ( array_key_exists( $key, $paths ) ) {

                    if ( count( $visited ) <= count( $useful_valve_keys ) ) {

                        save_the_elephants( $key, $visited, $remaining_time, $new_total_flow);
                    } else {
                        //echo '<h4 style="margin-bottom: 0;">' . $i . ' - Total Pressure: ' . $new_total_flow . ' - Time left: ' . $remaining_time . '</h4><br>';

                        foreach( $visited as $time => $v ) {
                            $this_time = ( $v !== 'AA' ) ? explode( ',', $time )[1] : 0;
                            $this_flow = $valves[$v][0];
                            $this_total = $this_flow * $this_time;

                            //echo "Added $v ( $this_flow ) for $this_time seconds ( $this_total )<br>";
                        }
                        //echo '<br><br>';
                        $i++;

                        if ( $new_total_flow > $best_flow ) {
                            $best_flow = $new_total_flow;
                            $best_path = implode( ',', $visited ) . ' - ' . $remaining_time;
                        }
                    }
                }
                
            }

        }

        $visited = $old_visited;
    }
}

/**
 * Part One
 */

function part_one() {
    global $best_flow, $best_path, $flows, $most_pressure, $paths, $useful_valve_keys, $valves;

    build_valves();
    build_paths(); 

    // Remove AA
    array_shift( $useful_valve_keys );

    foreach( $paths as $key => $path ) {
        $remove_connections = array_shift( $paths[$key] );
    }

    natsort( $paths['AA'] );

    foreach( $paths['AA'] as $val => $pathsaa ) {
        echo $val . ' - ' ;
        print_r( $pathsaa );
        echo '<br>';
    }


    save_the_elephants();

    echo "Best Pressure is $best_flow ( $best_path );";

    //print_r( $paths );


    // path_permutation( $useful_valve_keys );
    // echo "<br><br>The most pressure released is $most_pressure";

    // asort( $flows );

    // foreach ($flows as $path => $pressure) {
    //     echo '<br>' . $pressure . '  >  ' . $path;
    // }
}

/**
 * Part Two
 */

function part_two() {

}

part_one();
//part_two();
echo '<br>Total time to generate: ' . (microtime( true ) - $starttime);