<?php

/**
 * Day 11: Cathode-Ray Tube
 */

// The usual
$data = file_get_contents('data/data-11.txt');
$data = file_get_contents('data/data-11-sample.txt');
$rows = explode("\n", $data);

$groups = array_chunk($rows, 7);

for($m = 0; $m < count($groups); $m++) {
     ${"m_$m"} = 0;
}

// Check over 20 rounds

for ( $round = 1; $round <= 20; $round++ ) {

    echo 'Round ' . $round . '<br>';

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

                $num_worry_level = floor( $num_post_inspect / 3 );
                $num_worry_level = $num_post_inspect;

                if ( 0 === $num_worry_level % $divisible_by ) {
                    $maybe_comma = ( '' === $groups[$monkey_true][1] ) ? '' : ', ';
                    $groups[$monkey_true][1] .= trim( $maybe_comma . $num_worry_level );
                } else {
                    $maybe_comma = ( '' === $groups[$monkey_false][1] ) ? '' : ', ';
                    $groups[$monkey_false][1] .= trim( $maybe_comma . $num_worry_level );
                }
            }
        }

        $groups[$i][1] = '';
        
    }

    foreach( $groups as $monkey_num => $group ) {
        echo 'Monkey ' . $monkey_num . ': ' . $group[1];
        echo '<br>';
    }

    for($m = 0; $m < count($groups); $m++) {
         echo 'Monkey ' . $m . ' looked at  ' . ${"m_$m"} . ' items. <br>';
    }

    echo '<hr>';
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
    echo 'Monkey ' . $m . ' looked at  ' . ${"m_$m"} . ' items. <br>';
}

echo $highest . ' * ' . $second . ' = ' . ( $highest * $second );
