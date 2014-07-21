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

	$("body").on("click",".expand-collapse", function() {

		if ($(this).hasClass("expand-all")) {

			$(this).removeClass("expand-all").addClass("collapse-all").find("i").attr("class", "icon-collapse");

			$(".tzolkin-grid .tzolkin-row").each(function() {

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
			});

		} else {

			$(this).removeClass("collapse-all").addClass("expand-all").find("i").attr("class", "icon-expand");

			$(".tzolkin-grid .tzolkin-row").each(function() {
				var closedHeight = 0;
				$(this).find(".date-top").each(function() {
					if ( $(this).outerHeight(true) > closedHeight ) {
						closedHeight = $(this).outerHeight(true);
					}
				});
				$(this).css("height", closedHeight).removeClass("open");
			});

		}

	});

	}

	////////////////////////////////////////////////////////////////////////////
	//  Mobile: Open the cell  ////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	if ($(window).width() < 500) {

	function matchCellWidths() {
		var rowWidth = parseInt($(".tzolkin-row").outerWidth() - 2);

		$(".tzolkin-grid .details").css("width", rowWidth);

		var i_width = (rowWidth) / 7;

		var i = 1;
		for (i = 1; i < 7; i++) {
			var position = $(".tzolkin-grid .weekday-"+ i).position();

			if (typeof position != 'undefined') {
				$(".tzolkin-grid .weekday-"+ i +" .details").css("left", -position.left + 1);
			}
		}
	}
	$(window).on("load resize", matchCellWidths());

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

			// Hide expand/collapse button
			$(".expand-collapse").removeClass("show");

			// Crossfade from grid to list
			$(".tzolkin-grid").animate({opacity: 0}, "fast", function() {

				// Reset cell & row heights
				$(".tzolkin-row, .cell, .inner, .details").removeAttr("style");
				$(".tzolkin-row, .tzolkin-row .cell").removeClass("open");

				// Change grid to list
				$(".tzolkin-grid").removeClass("tzolkin-grid").addClass("tzolkin-list");

			}).animate({opacity: 1}, "fast");

		// List to Grid
		} else {

			// Switch format button class
			$(".format label").removeClass("active");
			$(this).parent("label").addClass("active");

			// Show expand/collapse button
			$(".expand-collapse").addClass("show");

			// Crossfade from list to grid
			$(".tzolkin-list").animate({opacity: 0}, "fast", function() {

				// Change list to grid
				$(".tzolkin-list").removeClass("tzolkin-list").addClass("tzolkin-grid");

				// Reset row heights
				matchRowHeights();

				if ($(window).width() < 500) {
					matchCellWidths();
				}

			}).animate({opacity: 1}, "fast");

		}
	});

	////////////////////////////////////////////////////////////////////////////
	//  Stop Propagation  /////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////

	$(".tzolkin-calendar a.title").on("click", function(event) {
	  event.stopPropagation();
	});

});