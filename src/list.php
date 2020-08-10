<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

$suffix = $module->getSuffix();
$eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
//$month = filter_var($_GET['month'], FILTER_SANITIZE_NUMBER_INT);
//$year = filter_var($_GET['year'], FILTER_SANITIZE_NUMBER_INT);
$baseline = filter_var($_GET['baseline'], FILTER_SANITIZE_STRING);
$offset = filter_var($_GET['offset'], FILTER_VALIDATE_INT);
$data = $module->getMonthSlots($eventId, null, null, $baseline, $offset);
$result = array();
$result['data'] = array();
if (!empty($data)) {
    $reservationEventId = $module->getReservationEventIdViaSlotEventId($eventId);
    /**
     * prepare data
     */
    foreach ($data as $record_id => $slot) {
        $slot = array_pop($slot);

        /**
         * group by day
         */
        $day = date('d', strtotime($slot['slot_start' . $suffix]));

        /**
         * skip past slots.
         */
        if ($module->isSlotInPast($slot, $suffix)) {
            continue;
        }

        /**
         * if the record id has different name just use whatever is provided.
         */
        if (!isset($slot['record_id'])) {
            $slot['record_id'] = array_pop(array_reverse($slot));
        }


        $counter = $module->getParticipant()->getSlotActualCountReservedSpots($slot['record_id'],
            $reservationEventId, $suffix, $module->getProjectId());

        $available = (int)($slot['number_of_participants' . $suffix] - $counter['counter']);;

        if ($available <= 0) {
            continue;
        }

        $cancelButton = '';
        if ($counter['userBookThisSlot']) {
            $reservation = end($counter['userBookThisSlot']);
            $cancelButton = '<button type="button"
                                                                      data-participation-id="' . $reservation[$module->getPrimaryRecordFieldName()] . '"
                                                                      data-event-id="' . $reservationEventId . '"
                                                                      class="cancel-appointment btn btn-block btn-danger">Cancel
                            </button>';
        } else {
            $bookButton = '<button type="button"
                                        data-record-id="' . $slot['record_id'] . '"
                                        data-event-id="' . $eventId . '"
                                        data-notes-label="' . $module->getNoteLabel() . '"
                                        data-show-projects="' . $module->showProjectIds() . '"
                                        data-show-attending-options="' . $module->showAttendingOptions() . '"
                                        data-show-location-options="' . $module->showLocationOptions() . '"
                                        data-show-attending-default="' . $module->getDefaultAttendingOption() . '"
                                        data-show-locations="' . (empty($slot['attending_options' . $suffix]) ? CAMPUS_AND_VIRTUAL : $slot['attending_options' . $suffix]) . '"
                                        data-show-notes="' . $module->showNotes() . '"
                                        data-date="' . date('Ymd', strtotime($slot['slot_start' . $suffix])) . '"
                                        data-start="' . date('Hi', strtotime($slot['slot_start' . $suffix])) . '"
                                        data-end="' . date('Hi', strtotime($slot['slot_end' . $suffix])) . '"
                                        data-modal-title="' . date('M/d/Y',
                    strtotime($slot['slot_start' . $suffix])) . ' ' . date('h:i A',
                    strtotime($slot['slot_start' . $suffix])) . ' - ' . date('h:i A',
                    strtotime($slot['slot_end' . $suffix])) . '"
                                        class="time-slot btn btn-block btn-success">Book
                                </button>';
        }

        $row = array();
        $row[] = date('m/d/Y', strtotime($slot['slot_start' . $suffix]));
        $row[] = $module->getLocationLabel($slot['location' . $suffix]);;
        $row[] = date('h:i A', strtotime($slot['slot_start' . $suffix])) . ' - ' . date('h:i A',
                strtotime($slot['slot_end' . $suffix]));;
        $row[] = '<h5 class="text-center">' . $available . '</h5>';;
        $row[] = $bookButton . $cancelButton;;

        $result['data'][] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($result);
}