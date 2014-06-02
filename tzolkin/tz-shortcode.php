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

	if ( isset($user_args['category_id']) && $user_args['category_id'] != -1 ) {
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
//			,array(
//				'key' => 'tz_rec_end'
//				,'value' => $nextMonth
//				,'compare' => '<='
//				,'type'    => 'DATETIME'
//
//			)
		)
	);

	if (isset($args['tax_query'])) $rArgs['tax_query'] = $args['tax_query'];
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

		if ($recType==='d') {

			for ($daynumber=1; $daynumber<=$lastDayOfMonth; $daynumber++) {

				$thisTimeStamp = strtotime($currentMonth.'/'.$daynumber.'/'.$currentYear);

				if (   $thisTimeStamp > strtotime($eventStart)
					&& $thisTimeStamp < strtotime($recEnd)
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

		} else if ($recType==='w') {

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

		} else if ($recType==='m1') {

			$eventWeekOfMonth = week_of_month(strtotime($eventStart));

			for ($daynumber=1; $daynumber<=$lastDayOfMonth; $daynumber++) {

				$thisTimeStamp = strtotime($currentMonth.'/'.$daynumber.'/'.$currentYear);
				$currentDayName = date('l', $thisTimeStamp);

				if ( $thisTimeStamp > strtotime($eventStart)
					&& $thisTimeStamp < strtotime($recEnd)
					&& $eventWeekOfMonth == week_of_month($thisTimeStamp)
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

		} else if ($recType==='m2') {

			for ($daynumber=1; $daynumber<=$lastDayOfMonth; $daynumber++) {

				$thisTimeStamp = strtotime($currentMonth.'/'.$daynumber.'/'.$currentYear);
				$currentDayName = date('l', $thisTimeStamp);

				if ( $thisTimeStamp > strtotime($eventStart)
					&& $thisTimeStamp < strtotime($recEnd)
					&& $daynumber == date('j', strtotime($eventStart))
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

		} else if ($recType==='y') {

			for ($daynumber=1; $daynumber<=$lastDayOfMonth; $daynumber++) {

				$thisTimeStamp = strtotime($currentMonth.'/'.$daynumber.'/'.$currentYear);
				$currentDayName = date('l', $thisTimeStamp);

				if ( $thisTimeStamp > strtotime($eventStart)
					&& $thisTimeStamp < strtotime($recEnd)
					&& $currentMonth == date('n', strtotime($eventStart))
					&& $daynumber == date('j', strtotime($eventStart))
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

function week_of_month($date) {
	$day_of_first = date('N', mktime(0,0,0,date('m',$date),1,date('Y',$date)));
	if ($day_of_first == 7) $day_of_first = 0;
	$day_of_month = date('j', $date);
	return floor(($day_of_first + $day_of_month) / 7) + 1;
}

////////////////////////////////////////////////////////////////////////////////
//  Shortcode Function  ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function tz_calendar_shortcode( ) {

///////////////////////////////////////////////////
// User-Controlled Options	//////////////////////
/////////////////////////////////////////////////

// Base the current month off user input, if it exists
if ( isset($_GET['user_month']) ) {
	$currentMonth = $_GET['user_month'];

} elseif ( isset($_GET['current_month'])) {
	$currentMonth = $_GET['current_month'];

} else {
	$currentMonth = date('F Y');
}

// Set the format off user input, if it exists.
if ( isset($_GET['format']) ) {
	$format = $_GET['format'];
} else {
	$format = 'grid';
}

// Use the category input unless the clear button was clicked.
if ( isset($_GET['tz_category']) && !isset($_GET['clear_category']) ) {
	$term_id = $_GET['tz_category'];
}

///////////////////////////////////////////////////
//  Markup Setup  ////////////////////////////////
/////////////////////////////////////////////////

echo '<div class="tzolkin-calendar">';

/////////////////////////////////////////
//  Header Area  ///////////////////////
///////////////////////////////////////

// Month Header
echo '<header><h2 class="tzolkin-title">'. $currentMonth .'</h2>';

echo '<form method="get" action="">';

// Categories
$c_args = array(
	'selected' => isset($term_id) ? $term_id : 0
);
echo tz_dropdown_categories($c_args);

// Format-- probably a more concise way to lay this out.
echo '<div class="format">';
if ( $format == 'grid' ) {
	echo '<label class="expand-collapse expand-all show" title="Expand Rows"><i class="icon-expand"></i></label>';
	echo '<label class="list" title="View List"><input class="toggle list" type="radio" name="format" value="list" /><i class="icon-list"></i></label>';
	echo '<label class="grid active" title="View Grid"><input class="toggle grid" type="radio" name="format" value="grid" checked /><i class="icon-grid"></i></label>';
} else {
	echo '<label class="expand-collapse expand-all" title="Expand Rows"><i class="icon-expand"></i></label>';
	echo '<label class="list active" title="View List"><input class="toggle list" type="radio" name="format" value="list" checked /><i class="icon-list"></i></label>';
	echo '<label class="grid" title="View Grid"><input class="toggle grid" type="radio" name="format" value="grid" /><i class="icon-grid"></i></label>';
}
echo '</div>';

// Month Navigation
$lastMonth = date('F Y', strtotime($currentMonth . " last month"));
$nextMonth = date('F Y', strtotime($currentMonth . " next month"));
echo
	'<div class="month-navigation">
		<button name="user_month" value="'. $lastMonth .'" type="submit" class="prev-month"><span class="arrow">&larr;</span> '. $lastMonth .'</button>
		<button name="user_month" value="'. $nextMonth .'" type="submit" class="next-month">'. $nextMonth .' <span class="arrow">&rarr;</span></button>
	</div>';

echo '<input type="hidden" name="current_month" value="'. $currentMonth .'" />';

echo '</form></header>';

////////////////////////////////////////////////////////////////////////////////
// 1. Set up Date Grid  ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Start up the grid, or the list.
echo '<div class="tzolkin-'. $format .'">';

// Weekday Headers
$days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
echo '<header class="tzolkin-row days">';
foreach($days as $day) {
	$third = substr($day, 2, 1);
	$day_w_span  = substr_replace($day, '<span class="third">'.$third.'</span><span class="full-day">', 2, 1); // su<span class="third">n</span><span class="full-day">day</span>
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
echo '<div class="tzolkin-row row-0">';

// Add an offset if it's necessary
if ($startkey != 0) {

	for ( $i = 0; $i < $startkey; $i++ ) {
		echo '<div class="cell offset offset-1">&nbsp;</div>';
	}
	//echo '<div class="cell offset offset-'. $startkey .'">&nbsp;</div>';
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
	$currentDate   = $i.' '.$currentMonth;
	$currentDay    = date('D', strtotime($currentDate) );
	$currentDayInt = date('w', strtotime($currentDate) );

	// Build the cells
	$date_cells[$i]['markup'] = '<div class="cell no-events weekday-'. $currentDayInt .' date-'. $i .' '. $p_p_or_f .'"><div class="inner"><div class="date-top"><div class="date"><div class="day">'. $currentDay .'</div><div class="number">'. $i .'</div></div></div></div></div>';
	$date_cells[$i]['event_starts'] = 0;
}

////////////////////////////////////////////////////////////////////////////////
// 2. Add Event Data  /////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

// Set up $args, get the events
$args = array('current_month' => $currentMonth);

// Get events by category if it's set.
if ( isset($term_id) )
	$args['category_id'] = $term_id;

$events = get_current_month_events($args);

if ( empty($events) )
	echo '<div class="list message">There are no events scheduled for '. $currentMonth .'.</div>';

if ( !empty($events) ) {

// Sort events by duration first.
foreach ($events as $event) {
	$e_start   = $event->tz_start;
	$e_end     = $event->tz_end;
	$start_key = date('j', strtotime($e_start));
	$end_key   = date('j', strtotime($e_end));

	if ($end_key < $start_key) {
		$end_key = date('t');
	}

	$duration  = $end_key - $start_key + 1;

	$event->duration = $duration;
}

// Get a list of sort columns and their data to pass to array_multisort
$sort = array();
foreach($events as $k=>$v) {
    $sort['tz_start'][$k] = $v->tz_start;
    $sort['duration'][$k] = $v->duration;
}
// sort by tz_start asc and then duration desc
array_multisort($sort['tz_start'], SORT_ASC, $sort['duration'], SORT_DESC,$events);

// Loop through events and add the data to the $date_cells array
foreach ($events as $event) {
	$e_id       = $event->ID;
	$e_start    = $event->tz_start;
	$e_end      = $event->tz_end;
	$e_allday   = $event->tz_all_day;
	$e_location = $event->tz_location;

	$start_key = date('j', strtotime($e_start));
	$end_key   = date('j', strtotime($e_end));

	// Set up category classes.
	$cats = get_the_terms($e_id, 'tz_category');

	if ( !empty($cats) ) {
		$cats = array_values($cats);
		$cat_classes = '';

		foreach ($cats as $cat) {
			$cat_classes .= ' ' . $cat->slug;
		}

		// Set color class
		$term_meta = get_option( 'taxonomy_term_'.$cats[0]->term_id );

		if ( isset($term_meta['color']) ) {
			$color = $term_meta['color'];
		} else {
			// If there's a category, but the category has no color, make it gray.
			$color = 'gray';
		}

	} else {
		// If there is no category, make it gray.
		$color = 'gray';
		$cat_classes = ' no-category';
	}

	// If the event goes from one month to the next, then set the end key to the
	// last day of the month.
	if ($end_key < $start_key) {
		$end_key = date('t', strtotime($currentMonth));
	}

	/////////////////////////////////////////
	//  2a. All/Multi-Day Events  //////////
	///////////////////////////////////////
	if ($e_allday == 1) {

		// Set the level based on what the order is at the beginning of the event.
		$i = 1;
		while ( isset($date_cells[$start_key]['rectangles'][$i]) ) {
			$i++;
		}
		$l_key = $i;

		// Reset the level to 0 if the event wraps to the next week.
		$start_day = strtolower( date('l', strtotime($e_start)) );
		$e_offset  = array_search($start_day, $days);

		// Add the shape to every day that the event covers.
		for ($i=$start_key; $i <= $end_key ; $i++) {

			$title = '';
			$show_title = '';
			$description = '';
			$math = ($i - $start_key + $e_offset);
			$duration  = $end_key - $start_key + 1;
			$daynumber = $i - $start_key;

			// if we've gone through the week and come around to the beginning..
			if ($math != 0 && $math % 7 == '0') {

				$int = 1;
				while ( isset($date_cells[$i]['rectangles'][$int]) ) {
					$int++;
				}
				$l_key = $int;
			}
			// Do this on every day: gets displayed once on desktop, but in every instance on tablet and handheld.
			$title = '<a class="title" href="'. get_permalink($e_id) .'">'. $event->post_title .'</a>';

			// Do this on the first day of the event.
			if ($i == $start_key) {
				// Keep track of how many events start on this day. Helpful for list output.
				$date_cells[$i]['event_starts'] = $date_cells[$i]['event_starts'] + 1;
				if ( has_excerpt( $e_id ) ) {
					$description = '<div class="description">'. $event->post_excerpt .'</div>';
				}
			}
			// Do this on the last day.
			if ( $i == $end_key ) {
				$last_day = ' last-day';
			} else {
				$last_day = '';
			}

			$date_cells[$i]['circles'][] = '<span class="circle '. $color .'"></span>';
			$date_cells[$i]['rectangles'][$l_key]  = '<div class="event level-'. $l_key .' start-'. $e_offset .' day-'. $daynumber .' duration-'. $duration . $last_day .'">';
			$date_cells[$i]['rectangles'][$l_key] .= '<div class="meta"><span class="time"><i class="icon-clock"></i>All Day</span>'. ( !empty($e_location) ? '<span class="location"><i class="icon-location"></i>'. $e_location .'</span>' : '') .'</div>';
			$date_cells[$i]['rectangles'][$l_key] .= '<div class="text rectangle '. $color . $cat_classes .'">'. $title . $description .'</div></div>';
		}
	}
	/////////////////////////////////////////
	//  2b. Single-Day Events  /////////////
	///////////////////////////////////////
	else {
		// Format the start/end times
		$time = tz_get_event_dates($e_id, 'g:ia');
		$e_time = $time['start'].' - '.$time['end'];

		$description = '';
		// Add Description
		if ( has_excerpt( $e_id ) ) {
			$description = '<div class="description">'. $event->post_excerpt .'</div>';
		}

		// Add the markup to each day that the event occurs.
		for ($i=$start_key; $i <= $end_key ; $i++) {
			$date_cells[$i]['circles'][] = '<span class="circle '. $color .'"></span>';
			$date_cells[$i]['titles'][]  = '<div class="event">';
			$date_cells[$i]['titles'][] .= '<div class="meta"><span class="time"><i class="icon-clock"></i>'. $e_time .'</span>'. ( !empty($e_location) ? '<span class="location"><i class="icon-location"></i>'. $e_location .'</span>' : '') .'</div>';
			$date_cells[$i]['titles'][] .= '<div class="text"><a class="title" href="'. get_permalink($e_id) .'">'. $event->post_title .'</a>'. $description .'</div></div>';
		}

		$date_cells[$start_key]['event_starts'] = $date_cells[$start_key]['event_starts'] + 1;
	}
}

} // if (!empty($events))

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
		$cell['markup'] = substr_replace($cell['markup'], $circles_markup, -18, -18);

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
		$cell['markup'] = substr_replace($cell['markup'], $details_markup, -12, -12);

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
		for ( $i = 0; $i < $end_offset; $i++ ) {
			echo '<div class="cell offset offset-1">&nbsp;</div>';
		}
	}
}

echo '</div>'; // last row
echo '</div>'; // tzolkin-grid
echo '</div>'; // tzolkin-calendar

}

add_shortcode( 'tz_calendar', 'tz_calendar_shortcode' );

function tz_scripts_and_styles() {
	// GOLIVE: Change this URL from /wp-content/plugins/tzolkin/ to TZ_URL.
	wp_enqueue_style( 'tzolkin_grid_styles', '/wp-content/plugins/tzolkin/css/style.css', false );
	wp_enqueue_script( 'tzolkin_grid_scripts', '/wp-content/plugins/tzolkin/js/app.min.js', array('jquery'), false, true );
}

add_action('wp_enqueue_scripts', 'tz_scripts_and_styles');

