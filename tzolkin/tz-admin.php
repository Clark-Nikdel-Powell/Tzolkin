<?php

final class TZ_Admin {

	public static function add_columns($cols) {
		if (!TZ_Event::is_tzolkin()) return $cols;
		$output = array();
		foreach($cols as $key => $col) {
			$output[$key] = 'date' === $key ? 'Publish Date' : $col;
			if ('title' === $key) {
				$output['tz_start'] = 'Start Date';
				$output['tz_end'] = 'End Date';
			}
		}
		return $output;
	}

	public static function manage_posts_sortable_columns($cols) {
		$cols['tz_start'] = 'tz_start';
		$cols['tz_end'] = 'tz_end';
		return $cols;
	}

	public static function manage_posts_custom_column($col, $post_id) {

		$all_day_format = 'Y/m/d';
		$format = "$all_day_format g:i A";
		$all_day = tz_event_is_all_day($post_id);

		switch($col) {
			case 'tz_start':
				echo tz_get_event_start_date($post_id, $all_day ? $all_day_format : $format);
				break;
			case 'tz_end':
				echo tz_get_event_end_date($post_id, $all_day ? $all_day_format : $format);
				break;
			default:
				//do nothing!
		}
	}

	public static function restrict_manage_posts() {
		global $wpdb, $wp_locale;
		if (!TZ_Event::is_tzolkin()) return;
		$mysql_format = "'Y-m-d H:i:s'";

		$months = $wpdb->get_results(
			"SELECT DISTINCT YEAR(meta_value) as year, MONTH(meta_value) as month
			FROM $wpdb->postmeta
			WHERE meta_key IN ('tz_start', 'tz_end')
			ORDER BY year DESC, month DESC
			"
		);

		$month_count = count( $months );
		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) return;
		$m = isset( $_GET['tz_date'] ) ? $_GET['tz_date'] : '';

		?>
				<select name='tz_date'>
					<option <?php if ($m === '') echo 'selected="selected"'; else echo ''; ?> value=''>Show all event dates</option>
		<?php
				foreach ( $months as $arc_row ) {
					if ( 0 == $arc_row->year )
						continue;

					$month = $arc_row->month;
					$year = $arc_row->year;
					$val = sprintf('%d-%d', $year, $month);

					printf( "<option %s value='%s'>%s</option>\n",
						$val === $m ? 'selected="selected"' : '',
						$val,
						sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
					);
				}
		?>
				</select>
		<?php
	}

	public static function enqueue_scripts() {
		wp_enqueue_script( 'tz_admin_scripts', TZ_URL.'js/pre/admin.js', array('jquery'), false, true);
	}

	public static function initialize() {
		add_filter( 'manage_edit-tz_event_columns', array(__CLASS__, 'add_columns') );
		add_filter( 'manage_edit-tz_event_sortable_columns', array(__CLASS__, 'manage_posts_sortable_columns') );
		add_filter( 'manage_posts_custom_column', array(__CLASS__, 'manage_posts_custom_column'), 10, 2);
		add_action( 'restrict_manage_posts', array(__CLASS__, 'restrict_manage_posts') );
		add_action( 'admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts') );
	}

}
