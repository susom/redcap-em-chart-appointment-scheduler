<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */


$event_id = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
$reservationEventId = $module->getReservationEventIdViaSlotEventId($event_id);

if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = filter_var($_GET['month'], FILTER_SANITIZE_NUMBER_INT);
    $year = filter_var($_GET['year'], FILTER_SANITIZE_NUMBER_INT);
    $data = $module->getMonthSlots($event_id, $year, $month);
    if (!empty($_GET['pid']) && $module->getProjectSetting('enable-redcap-calendar')) {
        $records = $module->getProjectREDCapCalendar(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT), $year,
            $month);
    }

} else {
    $data = $module->getMonthSlots($event_id);
    if (!empty($_GET['pid']) && $this->getProjectSetting('enable-redcap-calendar')) {
        $records = $module->getProjectREDCapCalendar(filter_var($_GET['pid'], FILTER_SANITIZE_NUMBER_INT));
    }
}
$days = array();
//process scheduler calendar
foreach ($data as $slot) {
    $slot = array_pop($slot);

    /**
     * if the record id has different name just use whatever is provided.
     */
    if (!isset($slot['record_id'])) {
        $slot['record_id'] = array_pop(array_reverse($slot));
    }

    $counter = $module->getParticipant()->getSlotActualCountReservedSpots($slot['record_id'],
        $module->getReservationEvents(), '', $module->getProjectId());
    /**
     * group by day
     */
    $day = (int)date('d', strtotime($slot['slot_start']));

    $days[$day]['available'] += (int)($slot['number_of_participants'] - $counter['counter']);

    $days[$day]['booked'] += (int)($counter['counter']);

    $days[$day]['availableText'] = 'Available slots: ' . $days[$day]['available'];

    /**
     *
     */
}

//if enabled get REDCap Calendar.
if (!empty($_GET['pid']) && $module->getProjectSetting('enable-redcap-calendar')) {

    while ($row = db_fetch_assoc($records)) {
        $day = (int)date('d', strtotime($row['event_date']));
        //show as many REDCap calendar records as there is.
        if (!isset($days[$day])) {
            $days[$day]['available'] = 1;
        }
        $days[$day]['REDCapAvailableText'] .= '<a class="ui-state-default redcap-default" style="min-height: unset" href="javascript:;" onclick="popupCal(' . $row['cal_id'] . ', 600)">' . $row['event_time'] . ' ' . $row['notes'] . '</a>';
    }
}
echo \GuzzleHttp\json_encode($days);