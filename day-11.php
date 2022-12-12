<?php

/**
 * Day 11: Monkey in the Middle
 */

// The usual
$data = file_get_contents('data/data-11.txt');
//$data = file_get_contents('data/data-11-sample.txt');
$rows = explode("\n", $data);

// Check over 20 rounds

function monkey_business( $tot_rounds, $worry_div, $rows ) {

    $groups = array_chunk($rows, 7);
    $div_by_total = 1;

    for($m = 0; $m < count($groups); $m++) {
         ${"m_$m"} = 0;
         $div_by_total = $div_by_total * substr($groups[$m][3], strpos( $groups[$m][3], "by") + 2);
    }

    for ( $round = 1; $round <= $tot_rounds; $round++ ) {

        for ( $i = 0; $i < count($groups); $i++ ) {
            // 0 => Monkey Num
            // 1 => Items
            // 2 => Operation
            // 3 => Test / 
            // 4 => True Opp
            // 5 => False Opp

            if ( ! empty( $groups[$i][1] ) ) {

                $items = explode( ',', str_replace( ['Starting items:', ' '], '', $groups[$i][1] ) );

                $operation = substr($groups[$i][2], strpos( $groups[$i][2], "=") + 2);
                $divisible_by = substr($groups[$i][3], strpos( $groups[$i][3], "by") + 2);
                $monkey_true = substr( $groups[$i][4], -1);
                $monkey_false = substr( $groups[$i][5], -1);

                foreach ($items as $key => $item) {
                    ${"m_$i"}++;

                    $old = $item;
                    $math = explode(' ', trim( str_replace('old', $item, $operation ) ) );

                    if ( '+' === $math[1] ) {
                        $num_post_inspect = $math[0] + $math[2];
                    } else if ( '-' === $math[1] ) {
                        $num_post_inspect = $math[0] - $math[2];
                    } else if ( '*' === $math[1] ) {
                        $num_post_inspect = $math[0] * $math[2];
                    } else {
                        $num_post_inspect = $math[0] / $math[2];
                    }

                    $num_worry_level = floor( $num_post_inspect / $worry_div );
                    //$num_worry_level = $num_post_inspect;
                    

                    if ( 0 === $num_worry_level % $divisible_by ) {
                        $maybe_comma = ( '' === $groups[$monkey_true][1] ) ? '' : ', ';
                        $groups[$monkey_true][1] .= trim( $maybe_comma . ( $num_worry_level % $div_by_total ) );
                    } else {
                        $maybe_comma = ( '' === $groups[$monkey_false][1] ) ? '' : ', ';
                        $groups[$monkey_false][1] .= trim( $maybe_comma . ( $num_worry_level % $div_by_total ) );
                    }
                }
            }

            $groups[$i][1] = '';
            
        }

        // if ( in_array($round, [1,20,1000,2000,3000,4000,5000,6000,7000,8000,9000,10000] ) ) {

        //     echo 'Round ' . $round . '<br>';

        //     foreach( $groups as $monkey_num => $group ) {
        //         echo 'Monkey ' . $monkey_num . ': ' . $group[1];
        //         echo '<br>';
        //     }

        //     for($m = 0; $m < count($groups); $m++) {
        //          echo 'Monkey ' . $m . ' looked at  ' . ${"m_$m"} . ' items. <br>';
        //     }

        //     echo '<hr>';
        // }
    }

    $highest = 0;
    $second = 0;

    for($m = 0; $m < count($groups); $m++) {
        if ( ${"m_$m"} > $second ) {
            $second = ${"m_$m"};
        }

        if ( ${"m_$m"} > $highest ) {
            $second = $highest;
            $highest = ${"m_$m"};
        }
        //echo 'Monkey ' . $m . ' looked at  ' . ${"m_$m"} . ' items. <br>';
    }

    return $highest . ' * ' . $second . ' = ' . ( $highest * $second );
}

echo PHP_EOL . 'Day 11: Monkey in the Middle' . PHP_EOL;
echo 'Some Worry: ' . monkey_business( 20, 3, $rows) . PHP_EOL;
echo 'Much Worry: ' . monkey_business( 10000, 1, $rows ) . PHP_EOL . PHP_EOL;
