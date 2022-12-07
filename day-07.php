<?php

/**
 * Day 7: No Space Left On Device
 */

// The usual
$data = file_get_contents('data/data-07.txt');
$rows = explode("\n", $data);
$directory_sizes = [];
$pwd = [];
$depth = 0;
$r = 0;
$visible_testing = false;

foreach ( $rows as $row ) {

    // Skip '$ ls' rows
    if ( substr( trim($row), 0, 4 ) === '$ ls' ) {
        continue;
    }

    /**
     * Visible Testing!
     * 
     * I used this to keep my data small at first
     * It's good to see if we're on the right track with a smaller set
     * 
     * Also look for "Visible Results!", and uncomment the three lines below
     */

    // if ( $r > 40 ) {
    //     continue;
    // }

    // Build out our current directory 
    // We add to / remove from the array later based on CD commands
    $current_dir_string = implode('/', $pwd);

    // Can't add to an array if it doesn't exist!
    if ( '' !== $current_dir_string &&  ! array_key_exists( $current_dir_string, $directory_sizes ) ) {
        $directory_sizes[ $current_dir_string ] = 0;
    }

    // If not a $, or a dir, it's a file
    if ( substr( trim($row), 0, 1 ) !== '$' && substr( trim($row), 0, 3 ) !== 'dir' ) {

        // For files, it's always number, space, filename. We only need the first
        $file_size = explode( ' ', $row )[0];

        // Start a fresh path to know what totals we are adding to
        $build_dir = '';

        // For each directory in our current path...
        foreach ( $pwd as $dir ) {

            // If it's the beginning, we don't need to add another slash
            $maybe_slash = ( '' === $build_dir ) ? '' : '/';

            // The directory we're adding to is our previous path, plus the new (current) folder
            // This lets us add the total file size to each directory
            // root, root/abc, root/abc/def, etc.
            $build_dir = $build_dir . $maybe_slash . $dir;

            // The size of this folder will be its current size, plus the new file
            $directory_sizes[ $build_dir ] = $directory_sizes[ $build_dir ] + $file_size;
        }
    }

    // If it starts with $ cd, we're either going up or down a folder
    if ( substr( trim($row), 0, 4 ) === '$ cd' ) {

        // If we need to go back a folder, remove the current directory from the "working directory" array
        if ( substr( trim($row), 0, 7 ) === '$ cd ..' ) {

            array_pop($pwd);

        } else {

            // Otherwise, get the new folder's name, and add it to our path
            $pwd[] = str_replace('$ cd ', '', str_replace( '$ cd /', 'root', $row ) );
        }
    }

    /**
     * Visible Testing!
     * 
     * If you'd like to make sure your 'cd' commands are going the right direction, lines 91 through 99 will output each directory with proper indentation
     */

    // echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth) . $row . '(' . $current_dir_string . ')<br>';

    // if ( substr( trim($row), 0, 4 ) === '$ cd' ) {
    //     if ( substr( trim($row), 0, 7 ) === '$ cd ..' ) {
    //         $depth--;
    //     } else {
    //         $depth++;
    //     }
    // }

    $r++;
}

/**
 * Visible Results!
 */

// foreach ( $directory_sizes as $key => $dir_size ) {
//     echo $key . ' = ' . $dir_size . PHP_EOL;
// }

/**
 * Part One - Combined total of all directories under 100,000 bytes
 */

$sub_100000_total = 0;

// For each directory...
foreach ( $directory_sizes as $pwd => $dir_size ) {
    // Only add the total in if it is 100,000 bytes or less
    if ( $dir_size <= 100000 && '' !== $dir_size ) {
        $sub_100000_total = $sub_100000_total + $dir_size;
    }
}

echo PHP_EOL . 'Day 7: No Space Left On Device' . PHP_EOL . PHP_EOL;
echo '/* Part One */' . PHP_EOL;
echo 'Combined size of all directories under 100,000 bytes: ' . $sub_100000_total . PHP_EOL . PHP_EOL;


/**
 * Part 2 - Getting the smallest directory that is still large enough to free up the amount of space needed to install the update
 * Let's have fun with it!
 * 
 * Note: This is just text, nothing is actually running rf -rf on your computer!
 */

$max_space = 70000000;
$space_needed = 30000000;
$used_space = $directory_sizes['root'];
$free_space = $max_space - $used_space;
$amount_to_free = $space_needed - $free_space;


echo 'Hard Drive Size: ' . number_format($max_space) . PHP_EOL;
echo 'Used Space: ' . number_format($used_space) . PHP_EOL;
echo 'Free Space: ' . number_format($max_space - $used_space) . PHP_EOL . PHP_EOL;

echo 'Space Needed for Update: ' . number_format($space_needed) . PHP_EOL;
echo 'You must delete at least ' . number_format($amount_to_free) . ' bytes to install this update' . PHP_EOL . PHP_EOL;

$delete_size = $space_needed;
$delete_dir = '';

// For each directory...
foreach ( $directory_sizes as $pwd => $dir_size ) {

    // Only get directories that are larger than what we need to free, but smaller than the last saved directory
    if ( $dir_size >= $amount_to_free && $dir_size < $delete_size ) {
        $delete_size = $dir_size;
        $delete_dir = $pwd;
    }
}

echo 'Fischers-MBP:~ fischfood$ rm -rf ' . $pwd . PHP_EOL;
echo 'This will permanently delete the directory. Are you sure? [Y/n] y' . PHP_EOL . PHP_EOL;
echo '/* Part Two */' . PHP_EOL;
echo 'Deleting ' . $pwd . ' (' . $delete_size . ' bytes)' . PHP_EOL . PHP_EOL;
echo '.' . PHP_EOL;
echo '..' . PHP_EOL;
echo '...' . PHP_EOL . PHP_EOL;
echo 'Available Space: ' . ( $used_space - $delete_size) . ' bytes' . PHP_EOL ;