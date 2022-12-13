<?php

/**
 * Day 13: Distress Signal
 */

// The usual
$data = file_get_contents('data/data-13.txt');
//$data = file_get_contents('data/data-13-sample.txt');

$rows = explode("\n", $data);
$display = false;

/**
 * Part One
 */

// Each set is two lines, plus a blank row (3)
$groups = array_chunk( $rows, 3 );
$decoded_group = [];

// Add lines one an two to a group for comparison
foreach( $groups as $group_num => $group ) {

    // Decode this so PHP can evaluate the string as actual arrays
    $set_a = json_decode( $group[0] );
    $set_b = json_decode( $group[1] );

    $decoded_group[$group_num] = [$set_a, $set_b];
}

// Well pass groups that are in the correct order to this array to sum later
$correct = [];

function part_one() {

    global $decoded_group;
    
    // For each group to compare...
    foreach( $decoded_group as $group_num => $decoded ) {

        // Compare the first chunk vs the second chunk
        // $result will return whichever side is lower
        $result = evaluate_group( $decoded[0], $decoded[1] );

        // If the first is lower, it is in the correct order
        // Add it to the 'correct' group
        if ( 'first' === $result ) {
            $correct[] = $group_num + 1;
        }
        
    }

    echo 'Sum of correct groups: ' . array_sum( $correct ) . PHP_EOL;
}

/**
 * Part Two
 */

// Create an array with the two new rows
$unordered = [[[2]],[[6]]];

// This time we need to check every row together
foreach ( $rows as $row ) {

    // If it isn't a blank line, add it to the list to sort through
    if ( '' !== $row ) {
        $unordered[] = json_decode( $row );
    }
}

function part_two() {

    // Get our arrays for:
    // - $ordered: the group we will add proper positions to
    // - $unordered: the original group, rows plus [[2]] and [[6]]

    global $ordered, $unordered;

    // Parse through the $unordered set of data
    // As it runs, items in their correct position will be added to $ordered;
    split_group_for_eval( $unordered );


    ksort( $ordered );

    // TEST: Visual output of final order
    // ------
    // echo '<h2>Final Order</h2>';
    // foreach ( $ordered as $pos => $string ) {
    //     echo str_pad($pos, 4, 0, STR_PAD_LEFT) . ' => ' . $string . '<br>';
    // }

    // Get the position for [[2]] and [[6]]
    $two = array_search( '[[2]]', $ordered );
    $six = array_search( '[[6]]', $ordered );

    // Multiply for the result
    echo 'Two and Six Locations: ' . ($two*$six) . ' (' . $two . '*' . $six . ')' . PHP_EOL . PHP_EOL;

}

/**
 * evaluate_group
 * 
 * This will compare the two inputs to see which is larger
 * If they are the same size, it will take the next array/string in the group and run it through again
 * If not, it will return whichever side is smaller, first or second
 */
function evaluate_group( $first, $second ) {

    // If they are not arrays...
    if ( ! is_array( $first ) && ! is_array( $second ) ) {

        // Return which side is smaller
        // If they are tied, return false and continue through the "while"
        // We will check if a result exists there

        if ( $second > $first ) {
            return 'first';
        } else if ( $first > $second ) {
            return 'second';
        }

    // If they are both arrays...
    } else if ( is_array( $first ) && is_array( $second ) ) {

        // Start at 0 to loop through all items in array
        $start = 0;

        // Get the size of the largest array (could be equal)
        $size = ( count( $first ) > count( $second ) ) ? count( $first ) : count( $second );

        // While items in the array exist...
        while ( $start < $size ) {

            // If [$first] doesn't have an nth value, but [$second] does, first is smaller
            if ( ! array_key_exists( $start, $first ) ) {
                return 'first';
            }

            // If [$second] doesn't have an nth value, but [$first] does, second is smaller
            if ( ! array_key_exists( $start, $second ) ) {
                return 'second';
            }


            // If they both exist, test the next two
            $next_first = $first[$start];
            $next_second = $second[$start];

            $result = evaluate_group($next_first, $next_second );

            // If we get a non-false result, we have an answer
            if ( ! empty( $result ) ) {
                return $result;
            }

            // Otherwise, go to the next one
            $start++;
        }

    // If one is an array and one is not
    } else {

        // Convert one to array and try again

        $new_first = $first;
        $new_second = $second;

        if ( ! is_array ( $new_first ) ) {
            $new_first = [$new_first];
        }

        if ( ! is_array ( $new_second ) ) {
            $new_second = [$new_second];
        }

        // Evaluate the new arrays
        $result = evaluate_group($new_first, $new_second );

        // If we know an answer, give it to us
        if ( ! empty( $result ) ) {
            return $result;
        }

    }

}

/**
 * split_group_for_eval
 * 
 * This will take the first item in an array and compare it to everything after it
 * - $unordered: An array of items that are not in $ordered
 * - $start: The key that the lowest item should start with
 */
function split_group_for_eval( $unordered, $start = 1 ) {

    // Get existing ordered
    global $ordered;

    // Start with blank arrays for before items and after items
    $before = [];
    $after = [];

    // If there is only one item, it can only be in one location
    // Add it to $ordered[] with a key of the starting point, $start
    if ( count( $unordered ) === 1 ) {

        $ordered[ $start ] = json_encode( $unordered[0] );

        // TEST: Visual output of single item being added
        // ------
        // echo '<h3>One Remains</h3>';
        // echo 'Adding ' . json_encode( $unordered[0] ) . ' as ' . $start . '<br><hr>';

    // If there are two items, they only need to be compared to each other
    } else if ( count( $unordered ) === 2 ) {

        $first = $unordered[0];
        $second = $unordered[1];

        // Compare the two
        $result = evaluate_group( $first, $second );

        if ( $result === 'first' ) {

            // Add first set as the lowest possible key
            // Add the second set as lpk + 1
            $ordered[ $start ] = json_encode( $first );
            $ordered[ $start + 1 ] = json_encode( $second );

            // TEST: Visual output of final two items in this array being added
            // ------
            // echo '<h3>Two Remain</h3>';
            // echo 'Adding ' . json_encode( $first ) . ' as ' . $start . '<br>';
            // echo 'Adding ' . json_encode( $second ) . ' as ' . ($start + 1) . '<br>';
            // echo '<hr>';

        } else {

            // Add second set as the lowest possible key
            // Add the first set as lpk + 1
            $ordered[ $start ] = json_encode( $second );
            $ordered[ $start + 1 ] = json_encode( $first );

            // TEST: Visual output of final two items in this array being added
            // ------
            // echo '<h3>Two Remain</h3>';
            // echo 'Adding ' . json_encode( $second ) . ' as ' . $start . '<br>';
            // echo 'Adding ' . json_encode( $first ) . ' as ' . ($start + 1) . '<br>';
            // echo '<hr>';
        }

    // If it's a set of items larger than 2...
    } else {
    
        // We're going to loop through all items after the first, comparing them to the first item in this array
        for ( $i = 1; $i < count( $unordered ); $i++ ) {

            // Always first vs this row in the array
            $first = $unordered[0];
            $second = $unordered[$i];

            // Compare
            $result = evaluate_group( $first, $second );

            // If the first item in the array is smaller than the one we are comparing...
            // The second item must go after this item
            if ( $result === 'first' ) {
                $after[] = $second;

            // If not, it goes before
            } else {
                $before[] = $second;
            }

        }

        // TEST: This is the list of items that go before $unordered[0] that we will need to compare amongst themselves next
        // ------
        // echo '<h3>Need to Sort Before</h3>';
        // echo 'Starting from ' . $start . ': ' . count( $before ) . ' before<br><br>';
        // foreach ( $before as $pos => $string ) {
        //     echo str_pad($pos + 1, 4, 0, STR_PAD_LEFT) . ' => ' . json_encode($string) . '<br>';
        // }

        // TEST: This is the list of items that go after $unordered[0] that we will need to compare amongst themselves next
        // ------
        // echo '<h3>Need to Sort After</h3>';
        // echo 'Starting from ' . ($start + count( $before ) + 1 ) . ': ' . count( $after ) . ' after <br><br>';
        // foreach ( $after as $pos => $string ) {
        //     echo str_pad($pos + count($before) + 2, 4, 0, STR_PAD_LEFT) . ' => ' . json_encode($string) . '<br>';
        // }

        // We've now figured out how many items are before and after our first item
        // This can be added to the completed list with a key of:
        // (how many items exist in before) + (the starting key of this set)
        $ordered[ count($before) + $start ] = json_encode( $unordered[0] );

        // TEST: This shows what position within $ordered this item will be injected
        // json_encode() brings this back into a string for easy output
        // ------
        //echo  '<br>Adding ' . json_encode($unordered[0]) . ' to completed with a key of ' . ( count($before) + $start ) . '<br><br>';
        //echo '<hr>';

        // If we have a group of "before" items that exist, we'll repeat this process
        // $start = current start point of this group (since before items exist, we know we don't have anything at the $start value)
        if ( count( $before ) >= 1 ) {
            split_group_for_eval( $before, $start );
        }

        // If we have a group of "after" items that exist, we'll repeat this process
        // We will set the start point as the minimum key ($start), plus the size of the "before" group, plus 1 (for the current item we compared to)
        if ( count( $after ) >= 1 ) {
            split_group_for_eval( $after, ( $start + count( $before ) + 1 ) );
        }
    }


}

echo PHP_EOL . 'Day 13: Distress Signal' . PHP_EOL;
part_one();
part_two();
