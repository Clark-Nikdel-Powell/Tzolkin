jQuery(function($) {

	var allDay = $('#tz-all-day');
	var startDateTextBox = $('#tz-start-date');
	var startTimeBox = $('#tz-start-time');
	var endDateTextBox = $('#tz-end-date');
	var endTimeBox = $('#tz-end-time');
	var recEndDateTextBox = '#tz-rec-end-date';
	var recFrequency = '#tz-rec-frequency';
	var timeFormat = 'h:mm TT';
	var shared = { 
		timeFormat: timeFormat
	};

	var makeOnClose = function(isStart) {
		var target = isStart ? endDateTextBox : startDateTextBox;
		return function(dateText, inst) {
			if (target.val()) {

				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testStartTime = $.datepicker.parseTime(timeFormat, startTimeBox.val());
				testStartDate.setHours(testStartTime.hour, testStartTime.minute);

	      var testEndDate = endDateTextBox.datetimepicker('getDate');
	      var testEndTime = $.datepicker.parseTime(timeFormat, endTimeBox.val());
	      testEndDate.setHours(testEndTime.hour, testEndTime.minute);

	      var limiter = isStart ? testStartDate : testEndDate;
	      if (testStartDate > testEndDate)
	      	target.datetimepicker('setDate', limiter);
			} else {
				target.val(dateText);
			}
		}
	}

	var makeOnSelect = function(isStart) {
		var target = isStart ? endDateTextBox : startDateTextBox;
		var other = isStart ? startDateTextBox : endDateTextBox;
		var option = isStart ? 'minDate' : 'maxDate';
		return function(selectedDateTime) {
			target.datetimepicker('option', option, other.datetimepicker('getDate'));
		}
	}

	var setAllDay = function() {
		var checked = allDay.is(':checked');
		var options = { 'showTimepicker': !checked };

		startDateTextBox.datetimepicker('option', options);
		endDateTextBox.datetimepicker('option', options);
		
		startTimeBox.toggle(!checked);
		endTimeBox.toggle(!checked);
	}

	startDateTextBox.datetimepicker($.extend({ 
	  onClose: makeOnClose(true),
	  onSelect: makeOnSelect(true),
	  altField: '#tz-start-time'
	}, shared));

	endDateTextBox.datetimepicker($.extend({ 
	  onClose: makeOnClose(false),
	  onSelect: makeOnSelect(false),
	  altField: '#tz-end-time'
	}, shared));

	function showEndDate() {
		if (!$(recFrequency + " option:selected").val())
			$("#freqp").css("display","none");
		else
			$("#freqp").css("display","block");
	}
	$(recEndDateTextBox).datepicker();
	$(recFrequency).on("change", function() { showEndDate(); });
	showEndDate();


	allDay.on('change', setAllDay);
	setAllDay();
});
