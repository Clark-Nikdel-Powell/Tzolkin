<?php

final class TZ_Event {

	private static $name          = 'tz_event';
	private static $slug          = 'events';

	private static $nonce         = 'tz-nonce';
	private static $all_day       = 'tz-all-day';
	private static $start_date    = 'tz-start-date';
	private static $start_time    = 'tz-start-time';
	private static $end_date      = 'tz-end-date';
	private static $end_time      = 'tz-end-time';

	private static $start_meta    = 'tz_start';
	private static $end_meta      = 'tz_end';
	private static $all_day_meta  = 'tz_all_day';
	private static $mysql_format  = 'Y-m-d H:i:s';

	private static $year_tag      = 'tz_year';
	private static $month_tag     = 'tz_month';
	private static $day_tag       = 'tz_day';
	private static $date_tag      = 'tz_date';
	private static $archive_tag   = 'tz_archive';

	private static $location_meta = 'tz_location';
	private static $address_meta  = 'tz_address';
	private static $city_meta     = 'tz_city';
	private static $state_meta    = 'tz_state';
	private static $zip_meta      = 'tz_zip';

	private static $register_args = array(
	  'public'             => true,
	  'publicly_queryable' => true,
	  'show_ui'            => true,
	  'show_in_menu'       => true,
	  'query_var'          => true,
	  'capability_type'    => 'post',
	  'has_archive'        => true,
	  'hierarchical'       => false,
	  'menu_position'      => 23,
	  'rewrite'            => array('with_front' => false),

	  'labels' => array(
			'name'               => 'Events',
			'singular_name'      => 'Event',
			'add_new'            => 'Add New',
			'all_items'          => 'All Events',
			'add_new_item'       => 'Add New Event',
			'edit_item'          => 'Edit Event',
			'new_item'           => 'New Event',
			'view_item'          => 'View Event',
			'search_items'       => 'Search Events',
			'not_found'          => 'No events found',
			'not_found_in_trash' => 'No events found in Trash',
			'parent_item_colon'  => 'Parent Event',
			'menu_name'          => 'Events'
	  ),

	  'supports' => array(
	  	'title',
	  	'editor',
	  	'thumbnail',
	  	'excerpt',
	  	'revisions'
	  )
	);

	/////////////////////////////////////////////////////////////////////////////
	// THEME HELPER METHODS
	/////////////////////////////////////////////////////////////////////////////

	private static function get_event_date($post_id, $key, $format = '') {
		$format = trim($format);
		$date_str = get_post_meta($post_id, $key, true);
		if (!$date_str) return $format ? '' : null;
		$date = new DateTime($date_str);
		return $format ? $date->format($format) : $date;
	}

	private static function get_the_date($key, $format = '') {
		global $post;
		return self::get_event_date($post->ID, $key, $format);
	}

	private static function the_date($key, $format = '') {
		$format = trim($format);
		if (!$format) $format = get_option('date_format');
		echo self::get_the_date($key, $format);
	}

	public static function get_event_dates($post_id, $format = '') {
		return array(
			'start' => self::get_event_date($post_id, self::$start_meta, $format),
			'end'   => self::get_event_date($post_id, self::$end_meta, $format)
		);
	}

	public static function get_the_dates($format = '') {
		global $post;
		return self::get_event_dates($post->ID, $format);
	}

	public static function get_event_start_date($post_id, $format = '') {
		return self::get_event_date($post_id, self::$start_meta, $format);
	}

	public static function get_event_end_date($post_id, $format = '') {
		return self::get_event_date($post_id, self::$end_meta, $format);
	}

	public static function get_the_start_date($format = '') {
		return self::get_the_date(self::$start_meta, $format);
	}

	public static function get_the_end_date($format = '') {
		return self::get_the_date(self::$end_meta, $format);
	}

	public static function the_start_date($format = '') {
		return self::the_date(self::$start_meta, $format);
	}

	public static function the_end_date($format = '') {
		return self::the_date(self::$end_meta, $format);
	}

	public static function event_is_all_day($post_id) {
		return (bool)get_post_meta($post_id, self::$all_day_meta, true);
	}

	public static function is_all_day() {
		global $post;
		return self::event_is_all_day($post->ID);
	}

	public static function event_is_same_day($post_id) {
		$dates = self::get_event_dates($post_id);
		$start = $dates['start'];
		$end = $dates['end'];
		$format = 'Ymd';
		return $start->format($format) === $end->format($format);
	}

	public static function is_same_day() {
		global $post;
		return self::event_is_same_day($post->ID);
	}

	////////////////////////////////////////////////////////////////
	//  LOCATION FUNCTIONS  ////////////////////////////////////////
	////////////////////////////////////////////////////////////////


	public static function get_event_location($post_id) {
		return get_post_meta($post_id, self::$location_meta, true);
	}

	public static function get_the_location() {
		global $post;
		return get_post_meta($post->ID, self::$location_meta, true);
	}

	public static function the_location() {
		global $post;
		echo get_post_meta($post->ID, self::$location_meta, true);
	}

	////////////////////////////////////////////////////////////////
	//  ADDRESS FUNCTIONS  /////////////////////////////////////////
	////////////////////////////////////////////////////////////////

	public static function gather_full_address($post_id) {
		$full_address =  get_post_meta($post_id, self::$address_meta, true);
		$full_address .= '<br />'.get_post_meta($post_id, self::$city_meta, true);
		$full_address .= ', '.get_post_meta($post_id, self::$state_meta, true);
		$full_address .= ' '.get_post_meta($post_id, self::$zip_meta, true);
		return $full_address;
	}

	public static function get_full_event_address($post_id) {
		$full_address = self::gather_full_address($post_id);
		return $full_address;
	}

	public static function get_full_address() {
		global $post;
		$full_address = self::gather_full_address($post->ID);
		return $full_address;
	}

	public static function the_full_address() {
		global $post;
		$full_address = self::gather_full_address($post->ID);
		echo $full_address;
	}

	public static function get_event_address($post_id) {
		return get_post_meta($post_id, self::$address_meta, true);
	}

	public static function get_the_address() {
		global $post;
		return get_post_meta($post->ID, self::$address_meta, true);
	}

	public static function the_address() {
		global $post;
		echo get_post_meta($post->ID, self::$address_meta, true);
	}

	////////////////////////////////////////////////////////////////
	//  CITY FUNCTIONS  ////////////////////////////////////////////
	////////////////////////////////////////////////////////////////


	public static function get_event_city($post_id) {
		return get_post_meta($post_id, self::$city_meta, true);
	}

	public static function get_the_city() {
		global $post;
		return get_post_meta($post->ID, self::$city_meta, true);
	}

	public static function the_city() {
		global $post;
		echo get_post_meta($post->ID, self::$city_meta, true);
	}

	////////////////////////////////////////////////////////////////
	//  STATE FUNCTIONS  ///////////////////////////////////////////
	////////////////////////////////////////////////////////////////


	public static function get_event_state($post_id) {
		return get_post_meta($post_id, self::$state_meta, true);
	}

	public static function get_the_state() {
		global $post;
		return get_post_meta($post->ID, self::$state_meta, true);
	}

	public static function the_state() {
		global $post;
		echo get_post_meta($post->ID, self::$state_meta, true);
	}

	////////////////////////////////////////////////////////////////
	//  ZIP CODE FUNCTIONS  ////////////////////////////////////////
	////////////////////////////////////////////////////////////////


	public static function get_event_zip($post_id) {
		return get_post_meta($post_id, self::$zip_meta, true);
	}

	public static function get_the_zip() {
		global $post;
		return get_post_meta($post->ID, self::$zip_meta, true);
	}

	public static function the_zip() {
		global $post;
		echo get_post_meta($post->ID, self::$zip_meta, true);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// POST META MAGIC!
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function is_tzolkin() {
	  $post_type = is_admin() ? null : get_post_type();
	  if( isset($_GET['post_type']) ) $post_type = $_GET['post_type'];
		elseif( isset($_GET['post']) && isset($_GET['action']) &&
		$_GET['action'] === 'edit') $post_type = get_post_type($_GET['post']);

		return self::$name === $post_type;
	}

	public static function enqueue_scripts() {
		if (!self::is_tzolkin()) return;

		wp_enqueue_script(
			'tz-jquery-ui-datetime',
			TZ_URL.'resources/jquery-ui-timepicker.js',
			array('jquery-ui-datepicker', 'jquery-ui-slider')
		);

		wp_enqueue_script(
			'tz-event',
			TZ_URL.'resources/tz-event.js',
			array('tz-jquery-ui-datetime')
		);
	}

	public static function enqueue_styles() {
		if (!self::is_tzolkin()) return;
		wp_enqueue_style('tz-jquery-ui', TZ_URL.'resources/jquery-ui-smoothness.css');
		wp_enqueue_style('tz-styles', TZ_URL.'resources/jquery-ui-timepicker.css');
	}

	public static function add_meta_box() {
		add_meta_box(
			'tz-event-meta',
			'Event Details',
			array(__CLASS__, 'event_meta_box'),
			self::$name,
			'side',
			'default'
		);
	}

	public static function event_meta_box($event, $box) {

		$all_day = (bool)get_post_meta($event->ID, self::$all_day_meta, true);

		$start_str = get_post_meta($event->ID, self::$start_meta, true);
		$end_str = get_post_meta($event->ID, self::$end_meta, true);

		$start = new DateTime($start_str ? $start_str : current_time('mysql'));
		$end = new DateTime($end_str ? $end_str : current_time('mysql'));

		$location         = get_post_meta($event->ID, self::$location_meta, true);
		$street_address   = get_post_meta($event->ID, self::$address_meta, true);
		$city             = get_post_meta($event->ID, self::$city_meta, true);
		$state            = get_post_meta($event->ID, self::$state_meta, true);
		$zip_code         = get_post_meta($event->ID, self::$zip_meta, true);

		wp_nonce_field(self::$name, self::$nonce, false);
		?>
			<h4>Date/Time</h4>
			<p>
				<label for="<?php echo self::$start_date; ?>">Start Date/Time</label><br/>
				<input type="text" name="<?php echo self::$start_date; ?>" id="<?php echo self::$start_date; ?>"
				 class="tz-input tz-date" value="<?php esc_attr_e($start->format('m/d/Y h:i A')); ?>" />
				<input type="text" name="<?php echo self::$start_time; ?>" id="<?php echo self::$start_time; ?>"
				 class="tz-input tz-time" value="<?php esc_attr_e($start->format('h:i A')); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$end_date; ?>">End Date/Time</label><br/>
				<input type="text" name="<?php echo self::$end_date; ?>" id="<?php echo self::$end_date; ?>"
				 class="tz-input tz-date" value="<?php esc_attr_e($end->format('m/d/Y h:i A')); ?>" />
				<input type="text" name="<?php echo self::$end_time; ?>" id="<?php echo self::$end_time; ?>"
				 class="tz-input tz-time" value="<?php esc_attr_e($end->format('h:i A')); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$all_day; ?>">
					<input type="checkbox" name="<?php echo self::$all_day; ?>" id="<?php echo self::$all_day; ?>"
					 value="<?php echo self::$all_day; ?>" <?php echo $all_day ? 'checked="checked" ' : ''; ?>/>
					All Day Event
				</label>
			</p>
			<h4>Location / Admission</h4>
			<p>
				<label for="<?php echo self::$location_meta; ?>">Location Name</label><br />
					<input type="text" name="<?php echo self::$location_meta; ?>" id="<?php echo self::$location_meta; ?>"
					 class="widefat tz-input tz-location" value="<?php echo ($location); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$address_meta; ?>">Street Address</label><br />
					<input type="text" name="<?php echo self::$address_meta; ?>" id="<?php echo self::$address_meta; ?>"
					 class="widefat tz-input tz-street" value="<?php esc_attr_e($street_address); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$city_meta; ?>">City</label><br />
					<input type="text" name="<?php echo self::$city_meta; ?>" id="<?php echo self::$city_meta; ?>"
					 class="widefat tz-input tz-city" value="<?php esc_attr_e($city); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$state_meta; ?>">State</label><br />
					<input type="text" name="<?php echo self::$state_meta; ?>" id="<?php echo self::$state_meta; ?>"
					 class="widefat tz-input tz-state" value="<?php esc_attr_e($state); ?>" />
			</p>
			<p>
				<label for="<?php echo self::$zip_meta; ?>">Zip Code</label><br />
					<input type="text" name="<?php echo self::$zip_meta; ?>" id="<?php echo self::$zip_meta; ?>"
					 class="widefat tz-input tz-zip" value="<?php esc_attr_e($zip_code); ?>" />
			</p>
		<?php
	}

	public static function save_post($post_id, $post) {
		if (!isset($_POST[self::$nonce])) return $post_id;
		$all_day = isset($_POST[self::$all_day]);
		$start   = new DateTime(sprintf('%s %s', $_POST[self::$start_date], $_POST[self::$start_time]));
		$end     = new DateTime(sprintf('%s %s', $_POST[self::$end_date], $_POST[self::$end_time]));

		$location       = isset($_POST[self::$location_meta]) ? $_POST[self::$location_meta] : '';
		$street_address = isset($_POST[self::$address_meta]) ? $_POST[self::$address_meta] : '';
		$city           = isset($_POST[self::$city_meta]) ? $_POST[self::$city_meta] : '';
		$state          = isset($_POST[self::$state_meta]) ? $_POST[self::$state_meta] : '';
		$zip_code       = isset($_POST[self::$zip_meta]) ? $_POST[self::$zip_meta] : '';

		if ($start > $end)
			$end = clone $start;

		if ($all_day) {
			$start->setTime(0,0,0);
			$end->setTime(23,59,59);
		}

		update_post_meta($post_id, self::$all_day_meta, $all_day);
		update_post_meta($post_id, self::$start_meta, $start->format(self::$mysql_format));
		update_post_meta($post_id, self::$end_meta, $end->format(self::$mysql_format));

		update_post_meta($post_id, self::$location_meta, $location);
		update_post_meta($post_id, self::$address_meta, $street_address);
		update_post_meta($post_id, self::$city_meta, $city);
		update_post_meta($post_id, self::$state_meta, $state);
		update_post_meta($post_id, self::$zip_meta, $zip_code);

		return $post_id;
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// REWRITE LOGIC
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function tags() {
		return array(
			self::$year_tag,
			self::$month_tag,
			self::$day_tag
		);
	}

	public static function add_rewrite_tags() {
		$tags = self::tags();
		$tags[] = self::$date_tag;
		$tags[] = self::$archive_tag;

		foreach(self::tags() as $tag)
			add_rewrite_tag(sprintf('%%%s%%', $tag), '([^&]+)');
	}

	public static function add_rewrite_rules() {
		global $wp_rewrite;

		$rules = array();
		$regex = self::$slug;
		$redirect = 'index.php?post_type='.self::$name;

		$rules[sprintf('%s/archive/?$', $regex)] = sprintf('%s&%s=1', $redirect, self::$archive_tag);
		$rules[sprintf('%s/archive/page/?(\d+)/?$', $regex)] = sprintf('%s&%s=1&paged=$1', $redirect, self::$archive_tag);

		foreach(self::tags() as $index => $tag) {
			$i = $index;
			$regex = sprintf('%s/(\d+)', $regex);
			$redirect = sprintf('%s&%s=$%d', $redirect, $tag, ++$i);

			$rules[sprintf('%s/?$', $regex)] = $redirect;
			$rules[sprintf('%s/page/?(\d+)/?$', $regex)] = sprintf('%s&paged=$%d', $redirect, ++$i);
		}

		$rules = array_reverse($rules);
		foreach($rules as $regex => $redirect) {
			if (0 < preg_match_all('@\$([0-9])@', $redirect, $matches))
				for ($i = 0; $i < count($matches[0]); ++$i)
					$redirect = str_replace($matches[0][$i], '$matches['.$matches[1][$i].']', $redirect);

			$wp_rewrite->add_rule($regex, $redirect, 'top');
		}
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// TITLE INTERCEPTION
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function wp_title($title, $sep, $seplocation) {
		global $wp_query;
		if (!is_archive()) return $title;
		if (!is_array($wp_query->query)) return $title;
		if (!array_key_exists('post_type', $wp_query->query)) return $title;
		if (self::$name !== $wp_query->query['post_type']) return $title;

		$title_format = "%s %s %s";
		$year_format = "Y";
		$month_format = "F Y";
		$day_format = "F j, Y";

		extract(self::parse_date($wp_query));
		$date = new DateTime();
		$date->setDate
		(
			$year > 0 ? $year : 1,
			$month > 0 && $month < 13 ? $month : 1,
			$day > 0 && $day < 32 ? $day : 1
		);

		if ($year <= 0) return $title;
		elseif ($month <= 0 || $month > 12) return sprintf($title_format, $date->format($year_format), $sep, $title);
		elseif ($day <= 0 || $day > 31) return sprintf($title_format, $date->format($month_format), $sep, $title);
		else return sprintf($title_format, $date->format($day_format), $sep, $title);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// WP QUERY ACTION INTERCEPTIONS
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function parse_date($query) {
		$default = array(
			'year' => 0,
			'month' => 0,
			'day' => 0,
			'is_archive' => false
		);

		$output = $default;

		if (
			array_key_exists(self::$year_tag, $query->query)
			&& is_numeric($query->query[self::$year_tag])
		)
			$output['year'] = (int)$query->query[self::$year_tag];

		if (
			array_key_exists(self::$month_tag, $query->query)
			&& is_numeric($query->query[self::$month_tag])
		)
			$output['month'] = (int)$query->query[self::$month_tag];

		if (
			array_key_exists(self::$day_tag, $query->query)
			&& is_numeric($query->query[self::$day_tag])
		)
			$output['day'] = (int)$query->query[self::$day_tag];


		if (array_key_exists(self::$date_tag, $_REQUEST)) {

			$parts = explode('-', $_REQUEST[self::$date_tag]);
			if (count($parts) === 0 || count($parts) > 3) return $output;
			foreach($parts as $part) if (!is_numeric($part)) return $output;

			$output = $default;
			if (count($parts) > 0) $output['year'] = (int)$parts[0];
			if (count($parts) > 1) $output['month'] = (int)$parts[1];
			if (count($parts) > 2) $output['day'] = (int)$parts[2];
		}

		if (array_key_exists(self::$archive_tag, $_REQUEST)) {
			$output['is_archive'] = (bool)$_REQUEST[self::$archive_tag];
		}

		return $output;
	}

	public static function pre_get_posts($query) {
		if (!is_archive()) return;
		if (!is_array($query->query)) return;
		if (!array_key_exists('post_type', $query->query)) return;
		if (isset($query->query['meta_query'])) return;
		if (self::$name !== $query->query['post_type']) return;

		self::filter_admin($query);
		self::order_by_event_date($query);
		self::add_date_filter($query);
	}

	public static function order_by_event_date(&$query) {
		if (isset($_REQUEST['orderby'])) return;
		$query->set('meta_key', self::$start_meta);
		$query->set('orderby', self::$start_meta);
		$query->set('order', is_admin() ? 'DESC' : 'ASC');
	}

	public static function add_date_filter(&$query) {
		$start = null;
		$end   = null;

		$year_format  = '%d-01-01 00:00:00';
		$month_format = '%d-%d-01 00:00:00';
		$day_format   = '%d-%d-%d 00:00:00';
		$mysql_format = 'Y-m-d H:i:s';

		extract(self::parse_date($query));

		if ($year <= 0) {
			if (!is_admin()) self::exclude_old_events($query);
			return;
		}
		elseif ($month <= 0 || $month > 12) { //year archive
			$start = new DateTime(sprintf($year_format, $year));
			$end = clone $start;
			$end->modify('+1 year');
			$end->modify('-1 second');
		}
		elseif ($day <= 0 || $day > 31) { // month archive
			$start = new DateTime(sprintf($month_format, $year, $month));
			$end = clone $start;
			$end->modify('+1 month');
			$end->modify('-1 second');
		}
		else { //day archive
			$start = new DateTime(sprintf($day_format, $year, $month, $day));
			$end = clone $start;
			$end->modify('+1 day');
			$end->modify('-1 second');
		}

		//add meta query arrays

		$meta_query = $query->meta_query;
		if (!is_array($meta_query)) $meta_query = array();
		$meta_query['relation'] = 'AND';

		//start date must be before search end date
		$meta_query[] = array(
			'key'     => self::$end_meta,
			'value'   => $start->format($mysql_format),
			'compare' => '>=',
			'type'    => 'DATETIME'
		);

		//end date must be before search start date
		$meta_query[] = array(
			'key'     => self::$start_meta,
			'value'   => $end->format($mysql_format),
			'compare' => '<=',
			'type'    => 'DATETIME'
		);

		$query->set('meta_query', $meta_query);
	}

	public static function exclude_old_events(&$query) {
		$meta_query = $query->meta_query;
		if (!is_array($meta_query)) $meta_query = array();


		$now = new DateTime();
		$meta_query[] = array(
			'key'     => self::$end_meta,
			'value'   => $now->format('Y-m-d H:i:s'),
			'compare' => '>=',
			'type'    => 'DATETIME'
		);

		$query->set('meta_query', $meta_query);
	}

	public static function filter_admin(&$query) {
		//do nothing for now...
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// REGISTRATION & INITIALIZATION
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public static function register() {
		$args = self::$register_args;
		$args['rewrite']['slug'] = self::$slug;
		$args = apply_filters('tz_register_args', $args);

		register_post_type(self::$name, $args);
	}

	public static function initialize() {
		add_action('init', array(__CLASS__, 'add_rewrite_tags'));
		add_action('delete_option_rewrite_rules', array(__CLASS__, 'add_rewrite_rules'));
		add_action('init', array(__CLASS__, 'register'));
		add_action('pre_get_posts', array(__CLASS__, 'pre_get_posts'));
		add_action('add_meta_boxes', array(__CLASS__, 'add_meta_box'));
		add_action('admin_init', array(__CLASS__, 'enqueue_scripts'));
		add_action('admin_init', array(__CLASS__, 'enqueue_styles'));
		add_filter('wp_title', array(__CLASS__, 'wp_title'), 10, 3);
		add_action('save_post', array(__CLASS__, 'save_post'), 10, 2);
	}

}
