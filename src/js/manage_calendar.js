var slot = {};
var recordId = null;

/**
 * Cancel Time Slot
 */
jQuery(document).on('click', '.cancel-slot', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    if (confirm("Are you sure you want to cancel this Time Slot?")) {
        var record_id = jQuery(this).data('record-id');
        var event_id = jQuery(this).data('event-id');
        var url = jQuery('#cancel-slot-url').val();
        /**
         * Get Manage modal to let user manage their saved appointments
         */
        jQuery.ajax({
            url: url + '&record_id=' + record_id + '&event_id=' + event_id,
            type: 'GET',
            datatype: 'json',
            success: function (response) {
                var response = JSON.parse(response);
                alert(response.message)
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
});

/**
 * call reschedule form and populate with data
 */
jQuery(document).on('click', '.reschedule-slot', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    slot.date = jQuery(this).data('date');
    slot.start = jQuery(this).data('start');
    slot.end = jQuery(this).data('end');
    slot.instructor = jQuery(this).data('instructor');
    slot.location = jQuery(this).data('location');
    slot.event_id = jQuery(this).data('event-id');
    slot.record_id = jQuery(this).data('record-id');

    fillSlotForm(slot, function () {
        $("#start").datetimepicker();
        $("#end").datetimepicker();
        $('#reschedule').modal('show');
        $('#generic-modal').modal('hide');
    })
});

/**
 *
 * @param array data
 * @param method callback
 */
function fillSlotForm(data, callback) {

    jQuery('#start').val(data.date + ' ' + data.start);
    jQuery('#end').val(data.date + ' ' + data.end);
    jQuery('#instructor').val(data.instructor);
    jQuery('#location').val(data.location);
    callback();
}

/**
 * Submit Reschedule
 */
jQuery(document).on('click', '#submit-reschedule-form', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    slot.start = jQuery("#start").val();
    slot.end = jQuery("#end").val();
    slot.instructor = jQuery("#instructor").val();
    slot.notes = jQuery("#reschedule-notes").val();
    slot.location = jQuery("#location").val();

    var url = jQuery("#reschedule-submit-url").val();
    jQuery.ajax({
        url: url,
        type: 'POST',
        data: slot,
        datatype: 'json',
        success: function (response) {
            var response = JSON.parse(response);
            if (response.status == 'ok') {
                alert(response.message);
                $('#booking').modal('hide');
            } else {
                alert(response.message);
            }
        },
        error: function (request, error) {
            alert("Request: " + JSON.stringify(request));
        }
    });
});


/**
 * Participants List
 */
jQuery(document).on('click', '.participants-list', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var record_id = recordId = jQuery(this).data('record-id');
    var event_id = jQuery(this).data('event-id');
    var title = jQuery(this).data('modal-title');
    var url = jQuery("#participants-list-url").val();
    jQuery.ajax({
        url: url + "&record_id=" + record_id + "&event_id=" + event_id,
        type: 'GET',
        data: slot,
        datatype: 'json',
        success: function (response) {
            if (jQuery('#generic-modal').length) {
                jQuery('#generic-modal').find('.modal-title').html('Participants list for ' + title);
                jQuery('#generic-modal').find('.modal-body').html(response);
                jQuery('#generic-modal').modal('show');
            } else {
                jQuery('#generic-manager-modal').find('.modal-title').html('Participants list for ' + title);
                jQuery('#generic-manager-modal').find('.modal-body').html(response);
                jQuery('#generic-manager-modal').modal('show');
            }


            jQuery('#participants-datatable').DataTable();
        },
        error: function (request, error) {
            alert("Request: " + JSON.stringify(request));
        }
    });
});

/**
 * No Show appointment
 */
jQuery(document).on('change', '.participants-no-show', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var participation_id = jQuery(this).data('participant-id');
    var event_id = jQuery(this).data('event-id');
    var url = jQuery('#participants-no-show-url').val();
    var status = jQuery(this).find(":selected").val();
    if (confirm("Are you sure you want to update the status of this reservation")) {

        /**
         * Get Manage modal to let user manage their saved appointments
         */
        jQuery.ajax({
            url: url + '&participations_id=' + participation_id + "&event_id=" + event_id + "&reservation_participant_status=" + status,
            type: 'GET',
            datatype: 'json',
            success: function (data) {
                data = JSON.parse(data);
                alert(data.message);
                console.log(jQuery('.participants-list[data-record-id=' + recordId + ']'));
                jQuery('.participants-list[data-record-id=' + recordId + ']').trigger('click');
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
});


/**
 * No Show appointment
 */
jQuery(document).on('click', '#submit-instance-description', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var description = jQuery("#instance-description").val();
    var event_id = jQuery(this).data('event-id');
    var url = jQuery('#instance-description-update-url').val();

    if (confirm("Are you sure you want to update Instance Description")) {

        /**
         * Get Manage modal to let user manage their saved appointments
         */
        jQuery.ajax({
            url: url,
            type: 'POST',
            data: {description: description, event_id: event_id},
            datatype: 'json',
            success: function (data) {
                data = JSON.parse(data);
                alert(data.message);
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
});