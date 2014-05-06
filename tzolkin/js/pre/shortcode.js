jQuery(function($) {

	FastClick.attach(document.body);

	////////////////////////////////////////////////////////////////////////////
	// Match cell heights  ////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	function matchRowHeights() {
		var i = 0;
		var last = $(".tzolkin-grid .tzolkin-row").length;
		for ( i = 0; i < last; i++ ) {

			var maxHeight = 0;
			$(".row-"+i+" .date-top").each(function() {
				if ( $(this).outerHeight() > maxHeight ) {
					maxHeight = $(this).outerHeight();
				}
			});
			$(".row-"+i+", .row-"+i+" .inner").css("height", maxHeight);
		}
	}
	$(window).on("load", matchRowHeights());

	////////////////////////////////////////////////////////////////////////////
	//  Desktop: Open the row  ////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	// Mobile check
	if ($(window).width() > 500) {

	$("body").on("click",".tzolkin-grid .tzolkin-row", function() {

		// Find the right height, and open the row.
		if ( !$(this).hasClass("open") ) {

			// Make sure that this row has events
			if ( $(this).find(".circles").length !== 0 ) {

				// Compare cell heights
				var openHeight = 0;
				$(this).find(".cell").each(function() {
					var cellHeight = $(this).find(".date-top").outerHeight(true) + $(this).find(".details").outerHeight(true);
					if ( cellHeight > openHeight ) {
						openHeight = cellHeight;
					}
				});
				$(this).css("height", openHeight).addClass("open");
			}
		}  else {
			var closedHeight = 0;
			$(this).find(".date-top").each(function() {
				if ( $(this).outerHeight(true) > closedHeight ) {
					closedHeight = $(this).outerHeight(true);
				}
			});
			$(this).css("height", closedHeight).removeClass("open");
		}
	});

	}

	////////////////////////////////////////////////////////////////////////////
	//  Mobile: Open the cell  ////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	if ($(window).width() < 500) {

	$(window).on('ready resize', function() {
		var rowWidth = parseInt($(".tzolkin-row").outerWidth() - 2);

		$(".tzolkin-grid .details").css("width", rowWidth);

		var i_width = (rowWidth) / 7;

		var i = 1;
		for (i = 1; i < 7; i++) {
			var position = $(".tzolkin-grid .weekday-"+ i).position();
			$(".tzolkin-grid .weekday-"+ i +" .details").css("left", -position.left + 1);

			//console.log(position.left);
		}
	});

	$("body").on("click",".tzolkin-grid .tzolkin-row .cell", function() {

		// Find the right height, and open the cell.
		if ( !$(this).hasClass("open") ) {

			// Make sure that this cell has events
			if ( $(this).find(".circles").length !== 0 ) {

				$(".tzolkin-grid").find(".cell").removeClass("open");
				matchRowHeights();

				// Get open height
				var openHeight = 0;
				openHeight = $(this).find(".date-top").outerHeight(true) + $(this).find(".details").outerHeight(true);
				$(this).addClass("open").parent().css("height", openHeight);
			}

		} else {

			// Get closed height
			var closedHeight = $(this).find(".date-top").outerHeight(true);
			$(this).removeClass("open").parent().css("height", closedHeight);

		}

	});

	}

	////////////////////////////////////////////////////////////////////////////
	//  Handle Format Switch  /////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	$(".format .toggle").on("click", function() {

		// Grid to List
		if ( $(this).hasClass("list") ) {

			// Switch format button class
			$(".format label").removeClass("active");
			$(this).parent("label").addClass("active");

			// Crossfade from grid to list
			$(".tzolkin-grid").animate({opacity: 0}, "fast", function() {

				// Reset cell & row heights
				$(".tzolkin-row, .cell, .details").removeAttr("style");
				$(".tzolkin-row, .tzolkin-row .cell").removeClass("open");

				// Change grid to list
				$(".tzolkin-grid").removeClass("tzolkin-grid").addClass("tzolkin-list");

			}).animate({opacity: 1}, "fast");

		// List to Grid
		} else {

			// Switch format button class
			$(".format label").removeClass("active");
			$(this).parent("label").addClass("active");

			// Crossfade from list to grid
			$(".tzolkin-list").animate({opacity: 0}, "fast", function() {

				// Change list to grid
				$(".tzolkin-list").removeClass("tzolkin-list").addClass("tzolkin-grid");

				// Reset row heights
				matchRowHeights();

			}).animate({opacity: 1}, "fast");

		}
	});

});