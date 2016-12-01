jQuery(document).ready(function($) {


	if ( $("#tz_no_end_time").attr("checked") ) {
		no_end_time(true);
	} else {
		no_end_time(false);
	}

	$("body").on("change", "#tz_no_end_time", function() {
		if ( $(this).attr("checked") ) {
			no_end_time(true);
		} else {
			no_end_time(false);
		}
	})

	function no_end_time(newval) {
		var $no_end_time = newval;

		if ( $no_end_time == true ) {
			$(".end-date-time").hide();
		} else {
			$(".end-date-time").show();
		}
	}
});