<?php

/**
 * Day 8: Treetop Tree House
 */

// The usual
$data = file_get_contents('data/data-08.txt');
$data_rows = explode("\n", $data);

$tree_rows = [];

// Each row => list of trees
foreach( $data_rows as $data_row_key => $trees_in_row ) {

    // The trees in this row are:
    $tree_rows[$data_row_key] = str_split( $trees_in_row );
}

// Set the width and the height of the forest
$forest_width = count($data_rows);
$forest_height = count($tree_rows[0]);

// Perimeter is always visible. Height x 2, Width x 2, remove the duplicated corners
$total_visible_perimeter = ($forest_width * 2) + ($forest_height * 2) - 4;
$inner_visible = 0;

$r = 0;
$scenic_score = 0;

// For each split row of trees
foreach( $tree_rows as $row_num => $tree_row ) {

    // Skip first and last row
    if ( $row_num > 0 && $row_num < (  $forest_height - 1 ) ) {
        
        foreach( $tree_row as $column => $height ) {

            // Skip first and last column
            if ( $column > 0 && $column < ( $forest_width - 1 ) ) {

                /* Part 1 */

                // Check Left
                // Set visible of this direction to true;
                $left = true;
                for ( $l = 0; $l < $column; $l++ ) {
                    // If any tree to the left is equal height, or taller, this tree isn't visible
                    // Break out of loop since it will never become visible again
                    if ( $tree_row[$l] >= $height ) {
                        $left = false;
                        break;
                    }
                }

                // Repeat Logic...

                // Check Right
                $right = true;
                for ( $r = ($forest_width - 1); $r > $column; $r-- ) {
                    if ( $tree_row[$r] >= $height ) {
                        $right = false;
                        break;
                    }
                }

                // Check Top
                $top = true;
                for ( $t = 0; $t < $row_num; $t++ ) {
                    if ( $tree_rows[$t][$column] >= $height ) {
                        $top = false;
                        break;
                    }
                }

                // Check bottom
                $bottom = true;
                for ( $b = ($forest_height - 1); $b > $row_num; $b-- ) {
                    if ( $tree_rows[$b][$column] >= $height ) {
                        $bottom = false;
                        break;
                    }
                }
            
                // If it's visible from any direction, this tree is "visible". Add to total
                if ( $left || $right || $top || $bottom ) {
                    $inner_visible++;
                }

                /** Part 2: Highest Scenic Score */

                // Check Left
                $scenic_left = [];
                $new_tall = 0;
                for ( $l = $column - 1; $l >= 0; $l-- ) {

                    // If we hit a tree equal to, or taller than, the current tree, add then stop
                    if ( $tree_row[$l] >= $height ) {
                        $scenic_left[] = $tree_row[$l];
                        break;

                    // Otherwise add and keep going
                    // Note: All trees shorter than current are counted.
                    // Say we're in a 7.
                    // If the trees to the right are 1 6 3 2 8 4, the total is 5, not 3.
                    // Taller trees don't make shorter trees hidden somehow...
                    // Darn Elves and their magic eyesight.
                    // This took me hours to figure out.

                    } else {
                        $scenic_left[] = $tree_row[$l];
                    }
                    
                }

                // Repeat Logic...

                // Check Right
                $scenic_right = [];
                $new_tall = 0;
                for ( $r = $column + 1; $r <= ($forest_width - 1); $r++ ) {
                    //echo $tree_row[$r] . ' >= ' . $height . '<br>';

                    if ( $tree_row[$r] >= $height ) {
                        $scenic_right[] = $tree_row[$r];
                        break;
                    } else {
                        $scenic_right[] = $tree_row[$r];
                    }
                    
                }

                // Check Top
                $scenic_top = [];
                $new_tall = 0;
                for ( $t = $row_num - 1; $t >= 0; $t-- ) {

                    if ( $tree_rows[$t][$column] >= $height ) {
                        $scenic_top[] = $tree_rows[$t][$column];
                        break;
                    } else {
                        $scenic_top[] = $tree_rows[$t][$column];
                    }
                    
                }

                // Check Bottom
                $scenic_bottom = [];
                $new_tall = 0;
                for ( $b = $row_num + 1; $b <= ($forest_height - 1); $b++ ) {

                    if ( $tree_rows[$b][$column] >= $height ) {
                        $scenic_bottom[] = $tree_rows[$b][$column];
                        break;
                    } else {
                        $scenic_bottom[] = $tree_rows[$b][$column];
                    }
                    
                }

                $sl = count($scenic_left);
                $sr = count($scenic_right);
                $st = count($scenic_top);
                $sb = count($scenic_bottom);
                $score = ($sl*$sr*$st*$sb);

                if ( $score > $scenic_score ) {
                    $scenic_score = $score;
                }

            }
            
        }

    }

    $r++;
}

echo PHP_EOL . 'Day 8: Treetop Tree House' . PHP_EOL;
echo 'Total Visible: ' . $total_visible_perimeter . ' (Perimeter) + ' . $inner_visible . ' (Inner) = ' . ($total_visible_perimeter + $inner_visible) . PHP_EOL;
echo 'Highest Scenic Score: ' . $scenic_score . PHP_EOL . PHP_EOL;