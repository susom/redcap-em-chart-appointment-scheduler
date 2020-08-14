<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */


try {
    /**
     * check if user still logged in
     */
    if (!$module::isUserHasManagePermission()) {
        throw new \LogicException('You cant be here');
    }
    $primaryField = \REDCap::getRecordIdField();
    $data[$primaryField] = filter_var($_GET['participations_id'], FILTER_SANITIZE_STRING);
    $data['reservation_participant_status'] = filter_var($_GET['reservation_participant_status'],
        FILTER_SANITIZE_NUMBER_INT);

    $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
    if ($data[$primaryField] == '') {
        throw new \LogicException('Participation ID is missing');
    }
    if ($eventId == '') {
        throw new \LogicException('Event ID is missing');
    } else {

        if ($data['reservation_participant_status'] == AVAILABLE) {
            $data['reservation_datetime'] = false;
            $data['reservation_date'] = false;

            // no not rescheduled before then make value zero to increase it in the next schedule
            $rescheduleCounter = $module->getRecordRescheduleCounter($data[$primaryField],
                $eventId);
            if ($rescheduleCounter == '') {
                $data['reservation_reschedule_counter'] = 0;
            }
        }
        $data['redcap_event_name'] = \REDCap::getEventNames(true, true, $eventId);
        $response = \REDCap::saveData($module->getProjectId(), 'json', json_encode(array($data)), 'overwrite');

        if (empty($response['errors'])) {
            //TODO notify instructor about the cancellation
            echo json_encode(array('status' => 'ok', 'message' => 'Appointment status updated'));
        } else {
            if (is_array($response['errors'])) {
                throw new \Exception(implode(",", $response['errors']));
            } else {
                throw new \Exception($response['errors']);
            }
        }
    }
} catch (\LogicException $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}