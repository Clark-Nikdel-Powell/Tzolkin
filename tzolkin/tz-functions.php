<?php

if (!function_exists('tz_get_event_dates')) {
	/**
	 * Returns the dates associated with an event as an associative array
	 * containing 'start' and 'end' as the available keys. If $format is not
	 * provided, DateTime objects are returned, otherwise the date is returned as
	 * a formatted string. Formats follow PHPs date() format.
	 *
	 * @access public
	 * @param  int    $post_id The post ID of the event
	 * @param  string $format  PHP style date format string. Leave blank for DateTime objects.
	 * @return array           Associative array of event dates
	 */
	function tz_get_event_dates($post_id, $format = '') {
		return TZ_Event::get_event_dates($post_id, $format);
	}
}

if (!function_exists('tz_get_the_dates')) {
	function tz_get_the_dates($format = '') {
		return TZ_Event::get_the_dates($post_id, $format);
	}
}

if (!function_exists('tz_get_event_start_date')) {
	function tz_get_event_start_date($post_id, $format = '') {
		return TZ_Event::get_event_start_date($post_id, $format);
	}
}

if (!function_exists('tz_get_event_end_date')) {
	function tz_get_event_end_date($post_id, $format = '') {
		return TZ_Event::get_event_end_date($post_id, $format);
	}
}

if (!function_exists('tz_get_the_start_date')) {
	function tz_get_the_start_date($format = '') {
		return TZ_Event::get_the_start_date($format);
	}
}

if (!function_exists('tz_get_the_end_date')) {
	function tz_get_the_end_date($format = '') {
		return TZ_Event::get_the_end_date($format);
	}
}

if (!function_exists('tz_the_start_date')) {
	function tz_the_start_date($format = '') {
		return TZ_Event::the_start_date($format);
	}
}

if (!function_exists('tz_the_end_date')) {
	function tz_the_end_date($format = '') {
		return TZ_Event::the_end_date($format);
	}
}

if (!function_exists('tz_event_is_all_day')) {
	function tz_event_is_all_day($post_id) {
		return TZ_Event::event_is_all_day($post_id);
	}
}

if (!function_exists('tz_is_all_day')) {
	function tz_is_all_day() {
		return TZ_Event::is_all_day();
	}
}

if (!function_exists('tz_event_is_same_day')) {
	function tz_event_is_same_day($post_id) {
		return TZ_Event::event_is_same_day($post_id);
	}
}

if (!function_exists('tz_is_same_day')) {
	function tz_is_same_day() {
		return TZ_Event::is_same_day();
	}
}

if (!function_exists('is_tzolkin')) {
	function is_tzolkin() {
		TZ_Event::is_tzolkin();
	}
}

if (!function_exists('tz_get_current_month_events')) {
	function tz_get_current_month_events($user_args) {
		return TZ_Event::get_current_month_events($user_args);
	}
}


///////////////////////////////////////////////////////////////
//  LOCATION FUNCTIONS  ///////////////////////////////////////
///////////////////////////////////////////////////////////////

if (!function_exists('tz_get_event_location')) {
	/**
	 * Returns the location name associated with an event as a string
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_get_event_location($post_id) {
		return TZ_Event::get_event_location($post_id);
	}
}

if (!function_exists('tz_get_the_location')) {
	/**
	 * Returns the location name associated with an event as a string
	 * and automatically takes the current post ID
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_get_the_location() {
		return TZ_Event::get_the_location();
	}
}

if (!function_exists('tz_the_location')) {
	/**
	 * Prints the location name associated with an event
	 * and automatically takes the current post ID.
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_the_location() {
		return TZ_Event::the_location();
	}
}


///////////////////////////////////////////////////////////////
//  ADDRESS FUNCTIONS  ////////////////////////////////////////
///////////////////////////////////////////////////////////////

if (!function_exists('tz_get_full_event_address')) {
	/**
	 * Returns the full address associated with an event as a string
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_get_full_event_address($post_id) {
		return TZ_Event::get_full_event_address($post_id);
	}
}

if (!function_exists('tz_get_full_address')) {
	/**
	 * Returns the full address associated with an event as a string
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_get_full_address() {
		return TZ_Event::get_full_address();
	}
}

if (!function_exists('tz_the_full_address')) {
	/**
	 * Returns the full address associated with an event as a string
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_the_full_address() {
		return TZ_Event::the_full_address();
	}
}

if (!function_exists('tz_get_event_address')) {
	/**
	 * Returns the street address associated with an event as a string
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_get_event_address($post_id) {
		return TZ_Event::get_event_address($post_id);
	}
}

if (!function_exists('tz_get_the_address')) {
	/**
	 * Returns the street address associated with an event as a string
	 * and automatically takes the current post ID
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_get_the_address() {
		return TZ_Event::get_the_address();
	}
}

if (!function_exists('tz_the_address')) {
	/**
	 * Prints the street address associated with an event
	 * and automatically takes the current post ID.
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_the_address() {
		return TZ_Event::the_address();
	}
}


///////////////////////////////////////////////////////////////
//  CITY FUNCTIONS  ///////////////////////////////////////////
///////////////////////////////////////////////////////////////

if (!function_exists('tz_get_event_city')) {
	/**
	 * Returns the city associated with an event as a string
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_get_event_city($post_id) {
		return TZ_Event::get_event_city($post_id);
	}
}

if (!function_exists('tz_get_the_city')) {
	/**
	 * Returns the city associated with an event as a string
	 * and automatically takes the current post ID
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_get_the_city() {
		return TZ_Event::get_the_city();
	}
}

if (!function_exists('tz_the_city')) {
	/**
	 * Prints the city associated with an event
	 * and automatically takes the current post ID.
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_the_city() {
		return TZ_Event::the_city();
	}
}


///////////////////////////////////////////////////////////////
//  STATE FUNCTIONS  //////////////////////////////////////////
///////////////////////////////////////////////////////////////

if (!function_exists('tz_get_event_state')) {
	/**
	 * Returns the state associated with an event as a string
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_get_event_state($post_id) {
		return TZ_Event::get_event_state($post_id);
	}
}

if (!function_exists('tz_get_the_state')) {
	/**
	 * Returns the state associated with an event as a string
	 * and automatically takes the current post ID
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_get_the_state() {
		return TZ_Event::get_the_state();
	}
}

if (!function_exists('tz_the_state')) {
	/**
	 * Prints the state associated with an event
	 * and automatically takes the current post ID.
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_the_state() {
		return TZ_Event::the_state();
	}
}


///////////////////////////////////////////////////////////////
//  ZIP CODE FUNCTIONS  ///////////////////////////////////////
///////////////////////////////////////////////////////////////

if (!function_exists('tz_get_event_zip')) {
	/**
	 * Returns the zip code associated with an event as a string
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_get_event_zip($post_id) {
		return TZ_Event::get_event_zip($post_id);
	}
}

if (!function_exists('tz_get_the_zip')) {
	/**
	 * Returns the zip code associated with an event as a string
	 * and automatically takes the current post ID
	 *
	 * @access public
	 * @return string            The location name
	 */
	function tz_get_the_zip() {
		return TZ_Event::get_the_zip();
	}
}

if (!function_exists('tz_the_zip')) {
	/**
	 * Prints the zip code associated with an event
	 * and automatically takes the current post ID.
	 *
	 * @access public
	 * @param  int     $post_id  The post ID of the event
	 * @return string            The location name
	 */
	function tz_the_zip() {
		return TZ_Event::the_zip();
	}
}


///////////////////////////////////////////////////////////////
//  CATEGORY FUNCTIONS  ///////////////////////////////////////
///////////////////////////////////////////////////////////////

if (!function_exists('tz_get_categories')) {

	function tz_get_categories() {
		return TZ_Event::get_categories();
	}
}

if (!function_exists('tz_dropdown_categories')) {

	function tz_dropdown_categories($args) {
		return TZ_Event::dropdown_categories($args);
	}
}
