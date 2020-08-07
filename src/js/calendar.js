var dateToday = new Date();
/**
 * Show list of all available time slots
 */
jQuery(".date-picker-2").datepicker({
    //numberOfMonths: [4,3],
    changeYear: false,
    duration: 'fast',
    beforeShowDay: $.datepicker.noWeekends,
    onSelect: function (dateText, elem) {
        var url = jQuery("#slots-url").val();
        var event_id = jQuery("#event-id").val();
        console.log(dateText)
        jQuery.ajax({
            'url': url + "&date=" + dateText + "&event_id=" + event_id,
            'type': 'GET',
            'success': function (data) {
                jQuery("#selected-date").val(dateText);
                jQuery('#generic-modal').find('.modal-title').html('Available Time Slots for ' + dateText);
                jQuery('#generic-modal').find('.modal-body').html(data);
                $('#generic-modal').modal('show');
            },
            'error': function (request, error) {
                alert("Request: " + JSON.stringify(request));
            },
            'complete': function () {
                populateMonthSummary(event_id);
            }
        });
    },
    onChangeMonthYear: function (year, month) {
        var event_id = jQuery("#event-id").val();
        populateMonthSummary(event_id, year, month);
    },
    minDate: dateToday,
});