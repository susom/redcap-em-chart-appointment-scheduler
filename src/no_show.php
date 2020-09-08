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

        if ($data['reservation_participant_status'] == SKIPPED) {
            $data['reservation_participant_id'] = filter_var($_GET['participations_id'], FILTER_SANITIZE_STRING);
            $data['reservation_datetime'] = date('Y-m-d H:i:s');
            $data['reservation_date'] = date('Y-m-d');
            $data['reservation_created_at'] = date('Y-m-d H:i:s');
            $data['summary_notes'] = filter_var($_GET['notes'], FILTER_SANITIZE_STRING);
        }
        if ($data['reservation_participant_status'] == AVAILABLE) {
            $data['reservation_datetime'] = false;
            $data['reservation_date'] = false;
            $data['reservation_slot_id'] = false;

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

            //notify user when canceled.
            if ($data['reservation_participant_status'] == CANCELED) {
                $instance = $module->getEventInstance();
                $user = $module->getParticipant()->getUserInfo($data[$primaryField], $module->getFirstEventId());
                $reservation = $module::getSlot(filter_var($data[$primaryField], FILTER_SANITIZE_STRING), $eventId,
                    $module->getProjectId(), $primaryField);
                $slot = $module::getSlot(filter_var($reservation['reservation_slot_id'], FILTER_SANITIZE_STRING),
                    $module->getSlotEventIdFromReservationEventId($eventId),
                    $module->getProjectId(), $primaryField);
                $module->sendEmail($user['email'],
                    ($instance['sender_email'] != '' ? $instance['sender_email'] : DEFAULT_EMAIL),
                    ($instance['sender_name'] != '' ? $instance['sender_name'] : DEFAULT_NAME),
                    '--CONFIRMATION-- Your appointment is canceled at ' . date('m/d/Y',
                        strtotime($slot['slot_start'])),
                    '--CONFIRMATION-- Your appointment is canceled at ' . date('m/d/Y',
                        strtotime($slot['slot_start']))
                );
            }


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