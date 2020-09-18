<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */


try {
    $primary = $module->getPrimaryRecordFieldName();
    $data[$primary] = $_GET[$primary];
    $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
    if ($data[$primary] == '') {
        throw new \LogicException('Participation ID is missing');
    } else {
        $data['reservation_slot_id'] = false;
        $data['reservation_participant_id'] = false;
        $data['reservation_datetime'] = false;
        $data['reservation_date'] = false;
        $data['reservation_participant_location'] = false;
        $data['reservation_participant_status'] = false;
        $data['visit_status'] = false;

        $data['summary_notes'] = $module->getRecordSummaryNotes($data[$primary],
                $eventId) . '\n[' . date('Y-m-d H:i:s') . ']: Appointment was canceled';

        $data['redcap_event_name'] = $module->getUniqueEventName($eventId);

        $rescheduleCounter = $module->getRecordRescheduleCounter($data[$module->getPrimaryRecordFieldName()], $eventId);
        if ($rescheduleCounter == '') {
            $data['reservation_reschedule_counter'] = 0;
        }

        $response = \REDCap::saveData($module->getProjectId(), 'json', json_encode(array($data)), 'overwrite');

        if (empty($response['errors'])) {
            //TODO notify instructor about the cancellation
            echo json_encode(array('status' => 'ok', 'message' => 'Appointment canceled successfully!'));
        } else {
            throw new \LogicException(implode(",", $response['errors']));
        }

    }
} catch (\LogicException $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}