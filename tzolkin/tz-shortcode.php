<?php

////////////////////////////////////////////////////////////////////////////////
//  Get Current Month Events  /////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function get_current_month_events($user_args) {

	$currentMonth = $user_args['current_month'];
	$currentMonthFormatted = date('Y-m-d H:i:s', strtotime($currentMonth . " this month"));
	$nextMonth             = date('Y-m-d H:i:s', strtotime($currentMonth . " next month"));

	$args = array(
		'post_type'        => 'tz_event'
	,	'suppress_filters' => true
	,	'meta_query'       => array(
				'relation' => 'AND'
			,	array(
					'key'     => 'tz_start'
				,	'value'   => $currentMonthFormatted
				,	'compare' => '>='
				,	'type'    => 'DATETIME'
				)
			,	array(
					'key'     => 'tz_start'
				,	'value'   => $nextMonth
				,	'compare' => '<='
				,	'type'    => 'DATETIME'
				)
			)
	,	'orderby'          => 'meta_value'
	,	'meta_key'         => 'tz_start'
	,	'order'            => 'ASC'
	,	'numberposts'      => -1
	);

	if ( isset($user_args['category_id']) ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'tz_category'
		,	'terms'    => $user_args['category_id']
		);
	}
	$events = get_posts($args);
	foreach ($events as $id=>$event) {
		$get = array('tz_start','tz_end','tz_all_day');
		foreach ($get as $key) {
			$events[$id]->$key = get_post_meta($event->ID, $key, true);
		}
	}


	$rArgs = array(
		'post_type' => 'tz_event'
		,'suppress_filters' => true
		,'meta_query'       => array(
			'relation' => 'AND'
			,array(
				'key' => 'tz_rec_frequency'
				,'value' => ''
				,'compare' => '!='

			)
			,array(
				'key' => 'tz_rec_frequency'
				,'compare' => 'EXISTS'

			)
			,array(
				'key' => 'tz_rec_end'
				,'value' => $nextMonth
				,'compare' => '<='
				,'type'    => 'DATETIME'

			)
		)
	);
	$reocurringEvents = get_posts($rArgs);

	$lastDayOfMonth = date('d',strtotime($nextMonth)-1);
	$currentYear = date('Y',strtotime($nextMonth)-1);
	$currentMonth = date('m',strtotime($nextMonth)-1);

	foreach ($reocurringEvents as $recEvent) {

		$recType		= get_post_meta($recEvent->ID, 'tz_rec_frequency', true);
		$recEnd 		= get_post_meta($recEvent->ID, 'tz_rec_end', true);
		$eventStart 	= get_post_meta($recEvent->ID, 'tz_start', true);
		$eventEnd 		= get_post_meta($recEvent->ID, 'tz_end', true);
		$eventLength 	= floor((strtotime($eventEnd) - strtotime($eventStart))/(60*60*24));
		$allDay 		= get_post_meta($recEvent->ID, 'tz_all_day', true);

		$startDayName = date('l', strtotime($eventStart));

		if ($recType==='w') {

			for ($daynumber=1; $daynumber<=$lastDayOfMonth; $daynumber++) {

				$thisTimeStamp = strtotime($currentMonth.'/'.$daynumber.'/'.$currentYear);
				$currentDayName = date('l', $thisTimeStamp);
		

				if (   $thisTimeStamp > strtotime($eventStart) 
					&& $thisTimeStamp < strtotime($recEnd) 
					&& $currentDayName == $startDayName
					) {

					$newStart 	= date('Y-m-d H:i:s', $thisTimeStamp);
					$newEnd 	= date('Y-m-d H:i:s', strtotime('+'.$eventLength.' day', $thisTimeStamp));

					$eventCopy 				= clone $recEvent;
					$eventCopy->tz_start 	= $newStart;
					$eventCopy->tz_end 		= $newEnd;
					$eventCopy->tz_all_day 	= $allDay;

					array_push($events, $eventCopy);
				}
			}
		}
	}

	return $events;
}

////////////////////////////////////////////////////////////////////////////////
//  Shortcode Function  ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function tz_calendar_shortcode( ) {

///////////////////////////////////////////////////
// User-Controlled Options	//////////////////////
/////////////////////////////////////////////////

// Base the current month off user input, if it exists
if ( isset($_POST['month']) ) {
	$currentMonth = $_POST['month'];
} else {
	$currentMonth = date('F Y');
}

// Set the format off user input, if it exists.
if ( isset($_POST['format']) ) {
	$format = $_POST['format'];
} else {
	$format = 'grid';
}

// Use the category input unless the clear button was clicked.
if ( isset($_POST['tz_category']) && !isset($_POST['clear_category']) ) {
	$term_id = $_POST['tz_category'];
}

///////////////////////////////////////////////////
//  Markup Setup  ////////////////////////////////
/////////////////////////////////////////////////

echo '<div class="tzolkin-calendar">';

/////////////////////////////////////////
//  Header Area  ///////////////////////
///////////////////////////////////////

// Month Header
echo '<header class="month current">'. $currentMonth .'</header>';

echo '<form method="post" action="">';

// Categories
$c_args = array(
	'selected' => isset($term_id) ? $term_id : 0
);
echo tz_dropdown_categories($c_args);

// Format
if ( $format == 'grid' ) {
	echo
	'<div class="format">
		<label><input class="toggle list" type="radio" name="format" value="list" />List</label>
		<label class="active"><input class="toggle grid" type="radio" name="format" value="grid" checked />Grid</label>
	</div>';
} else {
	echo
	'<div class="format">
		<label class="active"><input class="toggle list" type="radio" name="format" value="list" checked />List</label>
		<label><input class="toggle grid" type="radio" name="format" value="grid" />Grid</label>
	</div>';
}

// Month Navigation
$lastMonth = date('F Y', strtotime($currentMonth . " last month"));
$nextMonth = date('F Y', strtotime($currentMonth . " next month"));
echo
	'<div class="month-navigation">
		<button name="month" value="'. $lastMonth .'" type="submit" class="prev-month">&larr; '. $lastMonth .'</button>
		<button name="month" value="'. $nextMonth .'" type="submit" class="next-month">'. $nextMonth .' &rarr;</button>
	</div>';

echo '</form>';

////////////////////////////////////////////////////////////////////////////////
// 1. Set up Date Grid  ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Start up the grid, or the list.
echo '<div class="tzolkin-'. $format .'">';

// Weekday Headers
$days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
echo '<header class="tzolkin-row days">';
foreach($days as $day) {
	$day_w_span  = substr_replace($day, '<span class="full-day">', 2, 0);
	$day_w_span .= '</span>';
	echo '<header class="cell '. $day .'">'. ucfirst($day_w_span) .'</header>';
}
echo '</header>';

// Get the number of days in this month.
$dates = date("t", strtotime($currentMonth));

// Get the first weekday
$month = date("m" , strtotime($currentMonth));
$year = date("Y" , strtotime($currentMonth));
$startdate = getdate(mktime(null, null, null, $month, 1, $year));
$startdate = strtolower($startdate['weekday']);
$startkey  = array_search($startdate, $days);

// Add the first row
echo '<div class="tzolkin-dates">';
echo '<div class="tzolkin-row row-0">';

// Add an offset if it's necessary
if ($startkey != 0) {
	echo '<div class="cell offset offset-'. $startkey .'">&nbsp;</div>';
}

// Set the present as today's date number.
$present = date('j');

// Create the date cells, store in an array
$date_cells = array();

for ($i=1; $i <= $dates; $i++) {

	// Do the time warp! But only if we're on the current month.
	$p_p_or_f = '';
	if ( $currentMonth == date('F Y') ) {
		$p_p_or_f = 'present';
		if ($i < $present) {$p_p_or_f = 'past';}
		elseif ($i > $present) {$p_p_or_f = 'future';}
	}

	// Get weekday based off current month and current $i value
	$currentDate = $i.' '.$currentMonth;
	$currentDay  = date('D', strtotime($currentDate) );

	// Build the cells
	$date_cells[$i]['markup'] = '<div class="cell no-events date-'. $i .' '. $p_p_or_f .'"><div class="date-top"><div class="date"><div class="day">'. $currentDay .'</div><div class="number">'. $i .'</div></div></div></div>';
	$date_cells[$i]['event_starts'] = 0;
}

////////////////////////////////////////////////////////////////////////////////
// 2. Add Event Data  /////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Set up $args, get the events
$args = array('current_month' => $currentMonth);

// Get events by category if it's set. (line 73)
if ( isset($term_id) )
	$args['category_id'] = $term_id;

$events = get_current_month_events($args);

// Remove this soon
$colors = array('blue', 'red', 'green', 'yellow');

// Loop through events and add the data to the $date_cells array
foreach ($events as $event) {
	$e_id      = $event->ID;
	$e_start   = $event->tz_start;
	$e_end     = $event->tz_end;
	$e_allday  = $event->tz_all_day;

	$start_key = date('j', strtotime($e_start));
	$end_key   = date('j', strtotime($e_end));

	// If the event goes from one month to the next, then set the end key to the
	// last day of the month.
	if ($end_key < $start_key) {
		$end_key = date('t');
	}

	/////////////////////////////////////////
	//  2a. All/Multi-Day Events  //////////
	///////////////////////////////////////
	if ($e_allday == 1) {
		$color_key = array_rand($colors);

		// Set the level based on what the order is at the beginning of the event.
		if ( isset($date_cells[$start_key]['rectangles'][0]) ) {
			$l_key = count($date_cells[$start_key]['rectangles']);
		}
		else {
			$l_key = 0;
		}

		// Reset the level to 0 if the event wraps to the next week.
		$start_day = strtolower( date('l', strtotime($e_start)) );
		$e_offset  = array_search($start_day, $days);

		// Add the shape to every day that the event covers.
		for ($i=$start_key; $i <= $end_key ; $i++) {

			$title = '';
			$show_title = '';
			$description = '';
			$math = ($i - $start_key + $e_offset);
			// if we've gone through the week and come around to the beginning..
			if ($math != 0 && $math % 7 == '0') {
				$l_key = 0;
			}
			// Do this on the first day, or if we've gone through the week.
			if ($i == $start_key || ($math != 0 && $math % 7 == '0') ) {
				$show_title = 'show-title';
			}
			// Do this on the first day of the event.
			if ($i == $start_key) {
				// Keep track of how many events start on this day. Helpful for list output.
				$date_cells[$i]['event_starts'] = $date_cells[$i]['event_starts'] + 1;
				if ( has_excerpt( $e_id ) ) {
					$description = '<div class="description">'. $event->post_excerpt .'</div>';
				}
			}
			$duration  = $end_key - $start_key + 1;
			$daynumber = $i - $start_key;

			$title = '<a class="title" href="'. get_permalink($e_id) .'">'. $event->post_title .'</a>';
			$date_cells[$i]['circles'][] = '<span class="circle"></span>';
			$date_cells[$i]['rectangles'][$l_key] = '<div class="event day-'. $daynumber .' '. $show_title .'"><div class="time">All Day</div><div class="text rectangle '. $colors[$color_key] .' level-'. $l_key .' duration-'. $duration .'">'. $title . $description .'</div></div>';
		}
	}
	/////////////////////////////////////////
	//  2b. Single-Day Events  /////////////
	///////////////////////////////////////
	else {
		// Format the start/end times
		$time = tz_get_event_dates($e_id, 'g:i a');
		$e_time = $time['start'].'-'.$time['end'];

		$description = '';
		// Add Description
		if ( has_excerpt( $e_id ) ) {
			$description = '<div class="description">'. $event->post_excerpt .'</div>';
		}

		// Add the markup
		$date_cells[$start_key]['circles'][] = '<span class="circle"></span>';
		$date_cells[$start_key]['titles'][] = '<div class="event"><div class="time">'. $e_time .'</div><div class="text"><a class="title" href="'. get_permalink($e_id) .'">'. $event->post_title .'</a>'. $description .'</div></div>';

		// See line 248.
		$date_cells[$start_key]['event_starts'] = $date_cells[$start_key]['event_starts'] + 1;
	}
}

////////////////////////////////////////////////////////////////////////////////
// 3. Output Completed Cells  /////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

foreach ($date_cells as $key => $cell) {
	// Add in any circles or titles
	if ( isset($cell['circles']) ) {

		// Add all circles
		$circles_markup  = '<div class="circles">';
		foreach ($cell['circles'] as $shape) {
			$circles_markup .= $shape;
		}
		$circles_markup .= '</div>'; // circles

		// Insert the new markup for circles into the existing cell markup.
		$cell['markup'] = substr_replace($cell['markup'], $circles_markup, -12, -12);

		// Add all rectangles and/or titles
		$details_markup = '<div class="details">';

		// Add Rectangles from all or multi-day events
		if ( isset($cell['rectangles']) ) {
			// Fetch me my sorting hat.
			ksort($cell['rectangles']);
			$details_markup .= '<div class="rectangles">';
			foreach ($cell['rectangles'] as $shape) {
				$details_markup .= $shape;
			}
			$details_markup .= '</div>'; // rectangles
		}

		if ( isset($cell['titles']) ) {
			$details_markup .= '<div class="titles">';
			foreach ($cell['titles'] as $title) {
				$details_markup .= $title;
			}
			$details_markup .= '</div>'; // titles
		}
		$details_markup .= '</div>'; // details

		// Insert the new markup for shapes and details into the existing cell markup.
		$cell['markup'] = substr_replace($cell['markup'], $details_markup, -6, -6);

		// Throw in some more classes.
		$newclasses = 'cell has-events count-'. count($cell['circles']) .' starts-'. $cell['event_starts'];
		$cell['markup'] = str_replace('cell no-events', $newclasses, $cell['markup']);
	}

	// Output the completed cell.
	echo $cell['markup'];

	// Keeps the cells in neat rows.
	if ( ($key + $startkey) % 7 == 0 && isset($date_cells[$key+1]) ) {echo '</div><div class="tzolkin-row row-'. ( ($startkey + $key) / 7 ) .'">';}
}

////////////////////////////////////////////////////////////////////////////////
// Finishing Touches  /////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Add an end-offset for cosmetic purposes.
$end_offset = ( 7-(($startkey + $dates) % 7) );
if ($end_offset != 0 ) {
	if ($end_offset != 7) {
		echo '<div class="cell offset offset-'. $end_offset .'"">&nbsp;</div>';
	}
}

echo '</div>'; // last row
echo '</div>'; // tzolkin-dates
echo '</div>'; // tzolkin-grid
echo '</div>'; // tzolkin-calendar

}

add_shortcode( 'tz_calendar', 'tz_calendar_shortcode' );

function tz_scripts_and_styles() {
	// GOLIVE: Change this URL
	wp_enqueue_style( 'tzolkin_grid_styles', '/wp-content/plugins/tzolkin/css/style.css', false );
	wp_enqueue_script( 'tzolkin_grid_scripts', '/wp-content/plugins/tzolkin/js/min/shortcode-ck.js', array('jquery'), false, true );
}

add_action('wp_enqueue_scripts', 'tz_scripts_and_styles');

