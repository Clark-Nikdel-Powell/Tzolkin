<?php

////////////////////////////////////////////////////////////////////////////////
//  Shortcode Function  ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

function tz_calendar_shortcode($options) {

	///////////////////////////////////////////////////
	// Shortcode Options	////////////////////////////
	/////////////////////////////////////////////////

	$args = shortcode_atts(array(
		'format' => 'grid'
	,	'view'   => 'collapsed'
	), $options);

	///////////////////////////////////////////////////
	// URL Query String Options	//////////////////////
	/////////////////////////////////////////////////

	// Base the current month off user input, if it exists
	if (isset($_GET['user_month'])) $currentMonth = $_GET['user_month'];
	elseif (isset($_GET['current_month'])) $currentMonth = $_GET['current_month'];
	else $currentMonth = date('F Y');


	// Set the format off user input, if it exists.
	if (isset($_GET['format'])) $format = $_GET['format'];
	else $format = $args['format'];

	if (isset($_GET['view'])) $view = $_GET['view'];
	else $view = $args['view'];

	?><!-- <? print_r($args); ?> --><?

	// Use the category input unless the clear button was clicked.
	if (isset($_GET['tz_category']) && !isset($_GET['clear_category'])) $term_id = $_GET['tz_category'];


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
	$c_args = array('selected' => isset($term_id) ? $term_id : 0);

	// Get categories to display
	echo tz_dropdown_categories($c_args);

	// Format-- probably a more concise way to lay this out.
	echo '<div class="format">';
	if ( $format == 'grid' ) {
		echo '<label class="expand-collapse expand-all show" title="Expand Rows"><i class="icon-expand"></i></label>';
		echo '<label class="list" title="View List"><input class="toggle list" type="radio" name="format" value="list" /><i class="icon-list"></i></label>';
		echo '<label class="grid active" title="View Grid"><input class="toggle grid" type="radio" name="format" value="grid" checked /><i class="icon-grid"></i></label>';
	}
	else {
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
	echo '<div class="tzolkin-'. $format .' '. $view .'">';

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
	if ($startkey != 0) for ($i=0; $i<$startkey; $i++) echo '<div class="cell offset offset-1">&nbsp;</div>';

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
	if (isset($term_id)) $args['category_id'] = $term_id;

	// get events using this function
	$events = tz_get_current_month_events($args);

	?><!-- <? print_r($events); ?> --><?

	if (empty($events)) echo '<div class="list message">There are no events scheduled for '. $currentMonth .'.</div>';
	if (!empty($events)) {

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

			if ( $event->tz_exclude_from_calendar == 1 ) {
				continue;
			}

			$e_id       = $event->ID;
			$e_start    = $event->tz_start;
			$e_end      = $event->tz_end;
			$e_no_end   = $event->tz_no_end_time;
			$e_allday   = $event->tz_all_day;
			$e_location = $event->tz_location;
			( !empty($event->tz_calendar_title) ? $e_title = $event->tz_calendar_title : $e_title = $event->post_title);

			$start_key = date('j', strtotime($e_start));
			$end_key   = date('j', strtotime($e_end));

			// Set up category classes.
			$cats = get_the_terms($e_id, 'tz_category');

			if ( !empty($cats) ) {
				$cats = array_values($cats);
				$cat_classes = '';

				foreach ($cats as $cat) $cat_classes .= ' ' . $cat->slug;

				// Set color class
				$term_meta = get_option( 'taxonomy_term_'.$cats[0]->term_id );
				if (isset($term_meta['color'])) $color = $term_meta['color'];
				else $color = 'gray';


			} else {
				// If there is no category, make it gray.
				$color = 'gray';
				$cat_classes = ' no-category';
			}

			// If the event goes from one month to the next, then set the end key to the
			// last day of the month.
			if ($end_key < $start_key) $end_key = date('t', strtotime($currentMonth));

			/////////////////////////////////////////
			//  2a. All/Multi-Day Events  //////////
			///////////////////////////////////////
			if ($e_allday == 1) {

				// Set the level based on what the order is at the beginning of the event.
				$i = 1;
				while (isset($date_cells[$start_key]['rectangles'][$i])) $i++;
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
						while (isset($date_cells[$i]['rectangles'][$int])) $int++;
						$l_key = $int;
					}
					// Do this on every day: gets displayed once on desktop, but in every instance on tablet and handheld.
					$title = '<a class="title" href="'. get_permalink($e_id) .'">'. $e_title .'</a>';

					// Do this on the first day of the event.
					if ($i == $start_key) {
						// Keep track of how many events start on this day. Helpful for list output.
						$date_cells[$i]['event_starts'] = $date_cells[$i]['event_starts'] + 1;
						if ( has_excerpt( $e_id ) ) {
							$description = '<div class="description">'. $event->post_excerpt .'</div>';
						}
					}
					// Do this on the last day.
					if ($i==$end_key) $last_day = ' last-day';
					else $last_day = '';

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

				if ( $e_no_end == 1 ) {
					$e_time = $time['start'];
				}

				$description = '';
				// Add Description
				if (has_excerpt($e_id)) $description = '<div class="description">'. $event->post_excerpt .'</div>';

				// Add the markup to each day that the event occurs.
				for ($i=$start_key; $i <= $end_key ; $i++) {
					$date_cells[$i]['circles'][] = '<span class="circle '. $color .'"></span>';
					$date_cells[$i]['titles'][]  = '<div class="event">';
					$date_cells[$i]['titles'][] .= '<div class="meta"><span class="time"><i class="icon-clock"></i>'. $e_time .'</span>'. ( !empty($e_location) ? '<span class="location"><i class="icon-location"></i>'. $e_location .'</span>' : '') .'</div>';
					$date_cells[$i]['titles'][] .= '<div class="text"><a class="title" href="'. get_permalink($e_id) .'">'. $e_title .'</a>'. $description .'</div></div>';
				}
				$date_cells[$start_key]['event_starts'] = $date_cells[$start_key]['event_starts'] + 1;
			}
		}

	} // if (!empty($events))

	////////////////////////////////////////////////////////////////////////////////
	// 3. Output Completed Cells  /////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////

	foreach ($date_cells as $key=>$cell) {

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
		if ( ($key + $startkey) % 7 == 0 && isset($date_cells[$key+1]) ) {echo '</div><div class="tzolkin-row row-'. (($startkey + $key) / 7 ) .'">';}
	}

	////////////////////////////////////////////////////////////////////////////////
	// Finishing Touches  /////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////

	// Add an end-offset for cosmetic purposes.
	$end_offset = (7-(($startkey + $dates) % 7));
	if ($end_offset!=0) {
		if ($end_offset!=7) {
			for ($i=0; $i<$end_offset; $i++) {
				echo '<div class="cell offset offset-1">&nbsp;</div>';
			}
		}
	}
	echo '</div>'; // last row
	echo '</div>'; // tzolkin-grid
	echo '</div>'; // tzolkin-calendar
}

