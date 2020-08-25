var record = {};

var currentView = '';

const CAMPUS_AND_VIRTUAL = 0;
const VIRTUAL_ONLY = 1;
const CAMPUS_ONLY = 2;


const LIST_VIEW = 1;
const CALENDAR_VIEW = 2;

$body = jQuery("body");

jQuery(document).on({
    ajaxStart: function () {
        $body.addClass("loading");
    },
    ajaxStop: function () {
        $body.removeClass("loading");
    }
});
/**
 * Show Form to complete for selected time
 */
jQuery(document).on('click', '.type', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = jQuery(this).data('url');
    var key = jQuery(this).data('key');
    var month = jQuery(this).data('month');
    var year = jQuery(this).data('year');
    var view = jQuery(this).data('default-view');
    var $elem = jQuery(this)
    /**
     * init the reservation event id for selected slot.
     * @type {jQuery}
     */
    record.event_id = jQuery('#' + key + "-reservation-event-id").val();
    console.log(url)
    jQuery.ajax({
        'url': url,
        'type': 'GET',
        'beforeSend': function () {
            /**
             * remove any displayed calendar
             */
            jQuery('.slots-container').html('');
        },
        'success': function (data) {
            jQuery("#" + key + "-calendar-view").hide();
            jQuery("#" + key + "-list-view").show();
            jQuery("#" + key + "-calendar").collapse();
            $("#" + key + "-" + month + "-" + year + "-list-view" + ' .list-result').DataTable({
                dom: 'Bfrtip',
                data: data.data,
                pageLength: 50,
                "bDestroy": true,
                //"aaSorting": [[0, "asc"]],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            //show only the event for required month
            jQuery("#collapse-" + key + "-" + month + "-" + year).addClass('show');
            jQuery("#" + key + "-" + month + "-" + year + "-calendar").addClass('show');

            currentView = $elem;
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
 * Show list view
 */
jQuery(document).on('click', '.calendar-view', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = jQuery(this).data('url');
    var key = jQuery(this).data('key');
    $(".date-picker-2").datepicker("destroy");
    jQuery.ajax({
        'url': url,
        'type': 'GET',
        'success': function (data) {
            jQuery("#" + key + "-list-view").hide();
            jQuery("#" + key + "-calendar-view").show();
            jQuery("#" + key + "-calendar-view").html(data);
            currentView = $(this);
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

/**
 * Cancel appointment
 */
jQuery(document).on('click', '.cancel-appointment', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    if (confirm("Are you sure you want to cancel this appointment?")) {
        var participation_id = jQuery(this).data('participation-id');
        var event_id = jQuery(this).data('event-id');
        var url = jQuery('#cancel-appointment-url').val();
        /**
         * Get Manage modal to let user manage their saved appointments
         */
        jQuery.ajax({
            url: url + '&participations_id=' + participation_id + '&event_id=' + event_id,
            type: 'GET',
            datatype: 'json',
            success: function (data) {
                data = JSON.parse(data);
                alert(data.message);
                jQuery('.manage').trigger('click');
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    }
});

function populateMonthSummary(key, year, month) {
    setTimeout(function () {
        var url = jQuery("#summary-url").val();
        if (month == undefined) {
            month = ''
        }
        if (year == undefined) {
            year = ''
        }
        jQuery.ajax({
            'url': url + '&event_id=' + key + '&month=' + month + '&year=' + year,
            'type': 'GET',
            'success': function (response) {
                response = JSON.parse(response);
                jQuery(".ui-datepicker-calendar td").each(function (index, item) {

                    var day = jQuery(this).text();

                    if (response[day] != undefined) {
                        /**
                         * if date has open time slots
                         */
                        if (response[day].available != undefined) {
                            if (response[day].availableText != undefined) {
                                jQuery(this).find("a").attr('data-content', response[day].availableText);
                            }
                            if (response[day].REDCapAvailableText != undefined) {
                                var $a = jQuery(this).find("a");
                                jQuery(this).append(response[day].REDCapAvailableText)
                                //jQuery(this).find("a").insertAfter(response[day].REDCapAvailableText);
                            }
                        } else {
                            jQuery(this).find("a").attr('data-content', "All slots are booked for this date");
                        }
                        jQuery(this).find("a").toggleClass('changed');
                    }
                });
            },
            'error': function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });

    }, 0)
}


/**
 * Show Form to complete for selected time
 */
jQuery(document).on('click', '.time-slot', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    record.record_id = jQuery(this).data('record-id');
    record.event_id = jQuery(this).data('event-id');
    var dateText = jQuery(this).data('modal-title');

    /**
     * do we need to show attending options based on config.json
     */
    if (jQuery(this).data('show-attending-options') == "1") {
        jQuery("#attending-options").show();
    } else {
        jQuery("#attending-options").hide();
        var option = jQuery(this).data('show-attending-default');
        console.log(option)
        if (option === VIRTUAL_ONLY) {
            jQuery("#type-online").prop('checked', true);
        } else if (option === CAMPUS_ONLY) {
            jQuery("#type-campus").prop('checked', true);
        }
    }


    /**
     * do we need to location options section based on config.json
     */
    if (jQuery(this).data('show-location-options') == "1") {
        jQuery("#show-locations").show();
    } else {
        jQuery("#show-locations").hide();
    }

    /**
     * do we need to show notes and projects section based on config.json
     */
    if (jQuery(this).data('show-projects') == "1") {
        jQuery("#show-projects").show();
    } else {
        jQuery("#show-projects").hide();
    }

    if (jQuery(this).data('show-notes') == "1") {
        jQuery("#show-notes").show();
        var label = jQuery(this).data('notes-label');
        jQuery("#notes-label").text(label);
    } else {
        jQuery("#show-notes").hide();
    }

    /**
     * based on attending option change option to select from.
     */
    if (parseInt(jQuery(this).data('data-show-locations')) !== CAMPUS_AND_VIRTUAL) {
        if (parseInt(jQuery(this).data('data-show-locations')) === CAMPUS_ONLY) {
            jQuery("#type-online").hide()
            jQuery("#type-online-text").hide()
            jQuery("#type-campus").show()
            jQuery("#type-campus-text").show()
        } else {
            jQuery("#type-campus").hide()
            jQuery("#type-campus-text").hide()
            jQuery("#type-online").show()
            jQuery("#type-online-text").show()
        }
    } else {
        jQuery("#type-online").show()
        jQuery("#type-online-text").show()
        jQuery("#type-campus").show()
        jQuery("#type-campus-text").show()
    }

    if (jQuery(this).data('show-locations') == VIRTUAL_ONLY) {
        jQuery("#type-campus").hide();
        jQuery("#type-campus-text").hide();
        jQuery("#type-online").show();
        jQuery("#type-online-text").show();
        jQuery("#type-online").attr('checked', 'checked');
    } else if (jQuery(this).data('show-locations') == CAMPUS_ONLY) {
        jQuery("#type-online-text").hide();
        jQuery("#type-online").hide();
        jQuery("#type-campus").show();
        jQuery("#type-campus-text").show();
        jQuery("#type-campus").attr('checked', 'checked');
    } else {
        jQuery("#type-online-text").show();
        jQuery("#type-online").show();
        jQuery("#type-campus").show();
        jQuery("#type-campus-text").show();
    }
    /**
     * Capture start and end time for Email calendar
     */
    record.calendarStartTime = jQuery(this).data('start');
    record.calendarEndTime = jQuery(this).data('end');
    /**
     * Capture date for Email calendar
     */
    record.calendarDate = jQuery(this).data('date');
    jQuery("#selected-time").val(dateText);
    jQuery('#booking').find('.modal-title').html('Book Time Slot for ' + dateText);
    $('#booking').modal('show');
    $('#generic-modal').modal('hide');
});


/**
 * Complete booking form
 */
jQuery(document).on('click', '#submit-booking-form', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();

    /**
     * create object to submit reservation.
     * @type {*|jQuery|string|undefined}
     */
    record.email = jQuery("#email").val();
    record.name = jQuery("#name").val();
    record.mobile = jQuery("#mobile").val();
    record.employee_id = jQuery("#employee_id").val();
    record.department = jQuery("#department").val();
    record.supervisor_name = jQuery("#supervisor_name").val();
    record.notes = jQuery("#notes").val();
    record.private = jQuery("#private").val();
    record.type = jQuery("input[name='type']:checked").val();
    record.project_id = $('#project_id').find(":selected").val();
    record.date = record.calendarDate;

    var url = jQuery("#book-submit-url").val();
    jQuery.ajax({
        url: url,
        type: 'POST',
        data: record,
        datatype: 'json',
        success: function (response) {
            response = JSON.parse(response);
            if (response.status == 'ok') {
                alert(response.message);
                $('#booking').modal('hide');
                record = {};
                if (currentView != '') {
                    currentView.trigger('click');
                }

                /**
                 * when this book came from survey page lets return the reservation id back to the survey.
                 */
                var survey_record_id_field = jQuery("#survey-record-id-field").val();
                if (jQuery("input[name=" + survey_record_id_field + "]").length) {
                    completeSurveyReservation(response);
                }

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
 * load calendar view by clicking on the main button and pass the appropriate key
 */
jQuery(document).on('click', '.list-view', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var key = jQuery(this).data('key');
    jQuery('.type[data-key="' + key + '"]').get(0).click();

});


/**
 * Get Manage modal to let user manage their saved appointments
 */
jQuery(document).on('click', '.manage', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = jQuery("#manage-url").val();
    if (email != '') {
        jQuery.ajax({
            url: url,
            type: 'GET',
            datatype: 'json',
            success: function (data) {
                jQuery('#generic-modal').find('.modal-title').html('Manage My appointments');
                jQuery('#generic-modal').find('.modal-body').html(data);
                $('#generic-modal').modal('show');

                $('#myTabs a[href="#profile"]').tab('show')
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    } else {
        /**
         * user not logged in refresh to force sign in
         */
        location.reload();
    }
});

/**
 * Get Booked Slots Modal to
 */
jQuery(document).on('click', '.booked-slots', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = jQuery("#manage-booked-slots-url").val();
    if (true) {
        jQuery.ajax({
            url: url,
            type: 'GET',
            data: {event_id: jQuery(this).data('event-id')},
            datatype: 'json',
            success: function (data) {
                if (jQuery('#generic-modal').length) {
                    jQuery('#generic-modal').find('.modal-title').html('Manage Instructors Calendar');
                    jQuery('#generic-modal').find('.modal-body').html(data);
                    jQuery('#generic-modal').modal('show');
                    jQuery('#generic-modal').modal('show');
                } else {
                    jQuery('#booked-container').html(data);
                }

            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            },
            complete: function () {
                jQuery('#booked-slots').DataTable({
                    dom: '<"day-filter"><"location-filter"><lf<t>ip>',
                    pageLength: 50,
                    order: [[3, "asc"], [4, "asc"]],
                    columnDefs: [
                        {"type": "date", "targets": 3}
                    ],
                    initComplete: function () {
                        this.api().columns([4, 3]).every(function (index) {

                            var column = this;
                            if (index === 4 && column.data().any() === true) {
                                var select = $('<select id="day-options"><option value=""></option></select>')
                                    .appendTo($('.day-filter'))
                                    .on('change', function () {

                                        var val = $.fn.dataTable.util.escapeRegex(
                                            $(this).val()
                                        );

                                        // set preferred location so it will be selected next time.
                                        column
                                            .search(val ? '^' + val + '$' : '', true, false)
                                            .draw();
                                    });
                            }
                            if (index === 3 && column.data().any() === true) {
                                var select = $('<select id="location-options"><option value=""></option></select>')
                                    .appendTo($('.location-filter'))
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex(
                                            $(this).val()
                                        );

                                        // set preferred location so it will be selected next time.
                                        setCookie('preferred-location', val, 365);
                                        column
                                            .search(val ? '^' + val + '$' : '', true, false)
                                            .draw();
                                    });
                            }
                            column.data().unique().sort().each(function (d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>')
                            });

                            // if preferred location is saved then select that
                            if (getCookie('preferred-location') != null) {
                                $("#location-options").val(getCookie('preferred-location')).trigger('change');
                            }
                        });
                    }
                });
            }
        });
    } else {
        /**
         * user not logged in refresh to force sign in
         */
        location.reload();
    }
});

/**
 * Get Instance description
 */
jQuery(document).on('click', '.instance-description', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = jQuery("#manage-instance-description").val();
    if (true) {
        jQuery.ajax({
            url: url,
            type: 'GET',
            data: {event_id: jQuery(this).data('event-id')},
            datatype: 'json',
            success: function (data) {
                if (jQuery('#generic-modal').length) {
                    jQuery('#generic-modal').find('.modal-title').html('Manage Instance Description');
                    jQuery('#generic-modal').find('.modal-body').html(data);
                    jQuery('#generic-modal').modal('show');
                    jQuery('#generic-modal').modal('show');
                } else {
                    jQuery('#instance-description-container').html(data);
                }
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    } else {
        /**
         * user not logged in refresh to force sign in
         */
        location.reload();
    }
});


/**
 * Get Manage Calendar modal to let instructors manage all calendars
 */
jQuery(document).on('click', '.manage-calendars', function (e) {
    e.stopPropagation();
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = jQuery("#manage-calendar-url").val();
    if (true) {
        jQuery.ajax({
            url: url,
            type: 'GET',
            datatype: 'json',
            success: function (data) {
                if (jQuery('#generic-modal').length) {
                    jQuery('#generic-modal').find('.modal-title').html('Manage Instructors Calendar');
                    jQuery('#generic-modal').find('.modal-body').html(data);
                    jQuery('#generic-modal').modal('show');
                    jQuery('#generic-modal').modal('show');
                } else {
                    jQuery('#manager-container').html(data);
                }


                jQuery('#manage-calendars').DataTable(
                    {
                        dom: '<"day-filter-slots"><"location-filter-slots"><lf<t>ip>',
                        pageLength: 50,
                        "aaSorting": [[3, "asc"], [4, "asc"]],
                        columnDefs: [
                            {"type": "date", "targets": 3}
                        ],
                        initComplete: function () {
                            this.api().columns([1, 2]).every(function (index) {

                                var column = this;
                                if (index === 2 && column.data().any() === true) {
                                    var select = $('<select id="day-options-manager"><option value=""></option></select>')
                                        .appendTo($('.day-filter-slots'))
                                        .on('change', function () {
                                            var val = $.fn.dataTable.util.escapeRegex(
                                                $(this).val()
                                            );

                                            // set preferred location so it will be selected next time.
                                            column
                                                .search(val ? '^' + val + '$' : '', true, false)
                                                .draw();
                                        });
                                }
                                if (index === 1 && column.data().any() === true) {
                                    var select = $('<select id="location-options-manager"><option value=""></option></select>')
                                        .appendTo($('.location-filter-slots'))
                                        .on('change', function () {
                                            var val = $.fn.dataTable.util.escapeRegex(
                                                $(this).val()
                                            );

                                            // set preferred location so it will be selected next time.
                                            setCookie('preferred-location', val, 365);
                                            column
                                                .search(val ? '^' + val + '$' : '', true, false)
                                                .draw();
                                        });
                                }
                                column.data().unique().sort().each(function (d, j) {
                                    select.append('<option value="' + d + '">' + d + '</option>')
                                });

                                // if preferred location is saved then select that
                                if (getCookie('preferred-location') != null) {
                                    $("#location-options").val(getCookie('preferred-location')).trigger('change');
                                }
                            });
                        }
                    }
                );
            },
            error: function (request, error) {
                alert("Request: " + JSON.stringify(request));
            }
        });
    } else {
        /**
         * user not logged in refresh to force sign in
         */
        location.reload();
    }
});

/**
 * trigger function to load instance
 */
$(document).ready(function () {
    var instance = jQuery("#triggered-instance").val();
    var instances = jQuery("button.type");
    if (instance == '') {
        var $elem = jQuery(instances[0]);
        $elem.trigger('click');
    } else {
        var $elem = jQuery('a.type[data-name="' + instance + '"]');
        $elem.trigger('click');
    }

});

function loadDefaultView(view) {
    if (view == CALENDAR_VIEW) {
        if (jQuery(".survey-calendar-view").length > 0) {
            jQuery(".survey-calendar-view").trigger("click");
        } else {
            jQuery(".calendar-view").trigger("click");
        }
    }

    //now after loading for first time clear the value so the user can switch between list and calendar.
    jQuery("#default-view").val('')
}

//Calendar functions
function popupCal(cal_id, width) {
    window.open(app_path_webroot + 'Calendar/calendar_popup.php?pid=' + pid + '&width=' + width + '&cal_id=' + cal_id, 'myWin', 'width=' + width + ', height=250, toolbar=0, menubar=0, location=0, status=0, scrollbars=1, resizable=1');
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

