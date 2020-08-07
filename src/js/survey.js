var record = {};
const reserved_event_id = "reserved_event_id";

/**
 * trigger function to load instance
 */
$(document).ready(function () {
    var survey_record_id_field = jQuery("#survey-record-id-field").val();
    if (jQuery("input[name=" + survey_record_id_field + "]").length) {
        if (jQuery("input[name=" + survey_record_id_field + "]").val() == '') {
            var $elem = jQuery("input[name=" + survey_record_id_field + "]").parent();
            jQuery("input[name=" + survey_record_id_field + "]").attr('type', 'hidden');
            var url = jQuery("#survey-scheduler-url").val();
            var key = jQuery("#slots-events-id").val();
            var surveyRecordId = jQuery("#survey-record-id").val();
            $elem.append('<div data-url="' + url + '" id="survey-controller" data-survey-record-id="' + surveyRecordId + '" data-key="' + key + '" class="survey-type btn btn-block btn-info">Schedule Appointment</div>')
            //append this to show loader when ajax is fired
            $elem.append('<div class="loader"><!-- Place at bottom of page --></div>')
        }
    }
});
/**
 * list view in modal
 */
jQuery(document).on("click", ".survey-type", function () {
    var url = jQuery("#list-view-url").val();
    var key = jQuery(this).data('key');
    var view = jQuery(this).data('default-view');
    /**
     * init the reservation event id for selected slot.
     * @type {jQuery}
     */
    record.event_id = jQuery('#' + key + "-reservation-event-id").val();
    record.survey_record_id = jQuery(this).data('survey-record-id');
    ;
    jQuery.ajax({
        'url': url + "&event_id=" + jQuery("#slots-events-id").val(),
        'type': 'GET',
        'beforeSend': function () {
            /**
             * remove any displayed calendar
             */
            jQuery('.slots-container').html('');
        },
        'success': function (data) {
            jQuery('#generic-modal').find('.modal-title').html(jQuery("#survey-scheduler-header").val());
            jQuery('#generic-modal').find('.modal-body').html(data);
            $('#generic-modal').modal('show');

            //change calendar view event to be displayed in modal
            jQuery(".calendar-view").removeClass('calendar-view').addClass('survey-calendar-view');
        },
        'error': function (request, error) {
            alert("Request: " + JSON.stringify(request));
        },
        'complete': function () {
            loadDefaultView(view)
        }
    });
});

/**
 * Show calendar view in modal
 */
jQuery(document).on('click', '.survey-calendar-view', function () {
    var url = jQuery(this).data('url');
    var key = jQuery(this).data('key');
    $(".date-picker-2").datepicker("destroy");
    jQuery.ajax({
        'url': url,
        'type': 'GET',
        'success': function (data) {
            jQuery('#generic-modal').find('.modal-title').html(jQuery("#survey-scheduler-header").val());
            jQuery('#generic-modal').find('.modal-body').html(data);
            $('#generic-modal').modal('show');
            jQuery(".list-view").removeClass('list-view').addClass('survey-type');
            setTimeout(function () {
                $("#event-id").val(key)
                populateMonthSummary(key);
            }, 100);
        },
        'error': function (request, error) {
            alert("Request: " + JSON.stringify(request));
        }
    });
});


function completeSurveyReservation(response) {
    var survey_record_id_field = jQuery("#survey-record-id-field").val();
    jQuery("input[name=" + survey_record_id_field + "]").val(response.id);
    jQuery("#reserved-email").val(response.email);
    jQuery("#survey-controller").text("Reservation Completed");
    jQuery("#survey-controller").removeClass("survey-type survey-calendar-view");
    jQuery("#survey-controller").addClass("manage");
}

$(document).on({
    ajaxStart: function () {
        $body.addClass("loading");
    },
    ajaxStop: function () {
        $body.removeClass("loading");
    }
});