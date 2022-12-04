<?php

/**
 * Day 4: Camp Cleanup
 */

$data = file_get_contents('data/data-04.txt');
$rows = explode("\n", $data);

// Set totals since we know well need it
$total = 0;
$total_two = 0;

/**
 * Part 1: Find groups where one elf's set of rooms is fully contained within the others set of rooms
 */

// For each group of elves...
foreach ( $rows as $row ) {

	// Break them into their own groups
	$elves = explode(',', $row );

	// Set an array for each elf's range of rooms
	$elf_1 = explode( '-', $elves[0] );
	$elf_2 = explode( '-', $elves[1] );


	// If Elf 1 starts lower or equal to Elf 2 (E1 Start = 5, E2 Start = 7 )
	// And Elf 1 ends higher or equal than Elf 2: (E1 End = 36, E2 End = 20 )
	// Elf 2's rooms are fully container in Elf 1's
	// -- Increase total

	if ( $elf_1[0] <= $elf_2[0] && $elf_1[1] >= $elf_2[1] ) {
		//echo 'A Contains B';
		$total++;

	// Check the reverse
	// It must be an else just in case Elf 1 and Elf 2 match it would make a duplicate
	} else if ( $elf_1[0] >= $elf_2[0] && $elf_1[1] <= $elf_2[1] ) {
		//echo 'B Contains A';
		$total++;
	}

}

echo PHP_EOL . 'Day 4: Camp Cleanup' . PHP_EOL;
echo 'Total of complete overlap: ' . $total . PHP_EOL;

/**
 * Part 2: Find groups where Elf 1 and Elf 2 have any overlap at all
 */

// For each group of elves...
foreach ( $rows as $row ) {

	// Break them into their own groups
	$elves = explode(',', $row );

	// Set an array for each elf's range of rooms
	$elf_1 = explode( '-', $elves[0] );
	$elf_2 = explode( '-', $elves[1] );

	
	// If Elf 1 starts at a room lower than, or equal to, Elf 2 starts...
	if ( $elf_1[0] <= $elf_2[0] ) {

		// And Elf 1 ends at a room lower than, or equal to, Elf 2 starts
		// (meaning Elf 2's starting room is somewhere within the range of Elf 1's rooms )
		// -- Increase total

		if ( $elf_1[1] >= $elf_2[0]  ) {
			$total_two++;
		}	

	// Check the reverse
	// Again, else if to prevent duplicates

	// If Elf 2 starts at a room lower than, or equal to, Elf 2 starts...
	} else if ( $elf_2[0] <= $elf_1[0] ) {

		// And Elf 2 ends at a room lower than, or equal to, Elf 1 starts
		// (meaning Elf 1's starting room is somewhere within the range of Elf 2's rooms )
		// -- Increase total

		if ( $elf_2[1] >= $elf_1[0] ) {
			$total_two++;
		}		
	}
}

echo 'Total of any overlap: ' . $total_two . PHP_EOL . PHP_EOL;