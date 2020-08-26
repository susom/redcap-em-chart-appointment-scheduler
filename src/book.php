<?php

namespace Stanford\ChartAppointmentScheduler;

use REDCap;
/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */


try {
    $id = filter_var($_POST['participant_id'], FILTER_SANITIZE_STRING);;
    if ($user = $module->verifyCookie('login', $id)) {
        /**
         * if survey booking with NOAUTH ignore login validation.
         */
        if (!defined('USERID') && !defined('NOAUTH')) {
            throw new \LogicException('Please login.');
        }

        $data = $module->sanitizeInput();


        $data['reservation_participant_status' . $module->getSuffix()] = RESERVED;
        if (!isset($_POST['participant_id'])) {
            $data['reservation_participant_id'] = USERID;
        } else {
            $data['reservation_participant_id'] = filter_var($_POST['participant_id'], FILTER_SANITIZE_STRING);
        }

        $reservationEventId = filter_var($_POST['reservation_event_id'], FILTER_VALIDATE_INT);
        $slot = $module::getSlot(filter_var($_POST['record_id'], FILTER_SANITIZE_STRING), $data['event_id'],
            $module->getProjectId(), $module->getPrimaryRecordFieldName());


        // check if any slot is available
        $counter = $module->getParticipant()->getSlotActualCountReservedSpots(filter_var($_POST['record_id'],
            FILTER_SANITIZE_STRING),
            $reservationEventId, '', $module->getProjectId());

        if ((int)($slot['number_of_participants'] - $counter['counter']) <= 0) {
            throw new \Exception("All time slots are booked please try different time");
        }

        if (defined('USERID')) {
            $userid = USERID;
        } else {
            $userid = $data['reservation_participant_id'];
        }

//        $module->doesUserHaveSameDateReservation($date, $userid, $module->getSuffix(), $data['event_id'],
//            $reservationEventId);
        /**
         * let mark it as complete so we can send the survey if needed.
         * Complete status has different naming convention based on the instrument name. so you need to get instrument name and append _complete to it.
         */
        $labels = \REDCap::getValidFieldsByEvents($module->getProjectId(), array($reservationEventId));
        $completed = preg_grep('/_complete$/', $labels);
        $second = array_slice($completed, 1, 1);  // array("status" => 1)

        $data[$second] = REDCAP_COMPLETE;

        // the location is defined in the slot.
        $data['reservation_participant_location' . $module->getSuffix()] = $slot['location'];

        $data['reservation_datetime'] = $slot['slot_start'];
        $data['reservation_date'] = date('Y-m-d', strtotime($slot['slot_start']));
        $data['reservation_created_at'] = date('Y-m-d H:i:s');

        $data['redcap_event_name'] = $module->getUniqueEventName($reservationEventId);
        $data[$module->getPrimaryRecordFieldName()] = filter_var($_POST['participant_id'],
            FILTER_SANITIZE_STRING);


        // if this appointment was scheduled before make sure to count that.
        $rescheduleCounter = $module->getRecordRescheduleCounter($data[$module->getPrimaryRecordFieldName()],
            $reservationEventId);
        if ($rescheduleCounter != '') {
            $data['reservation_reschedule_counter'] = $rescheduleCounter + 1;
        }

        $response = \REDCap::saveData($module->getProjectId(), 'json', json_encode(array($data)));
        if (empty($response['errors'])) {

            //if slot has instructor identified then send email to the instructor
//            if (!empty($slot['instructor'])) {
//                $data['instructor'] = $slot['instructor'];
//            }

            // add email and mobile to notify the user about
            if ($user['record'][$module->getFirstEventId()]['email'] != '') {
                $data['email'] = $user['record'][$module->getFirstEventId()]['email'];
            }

            if ($user['record'][$module->getFirstEventId()]['phone_number'] != '') {
                $data['mobile'] = $user['record'][$module->getFirstEventId()]['phone_number'];
            }
            $data['newuniq'] = $user['id'];
            $return = $module->notifyUser($data, $slot);
            echo json_encode(array(
                'status' => 'ok',
                'message' => 'Appointment saved successfully!' . (isset($return['error']) ? ' with following errors' . $return['message'] : ''),
                'id' => array_pop($response['ids']),
                'email' => $data['email']
            ));
        } else {
            if (is_array($response['errors'])) {
                throw new \Exception(implode(",", $response['errors']));
            } else {
                throw new \Exception($response['errors']);
            }

        }
    } else {
        throw new \LogicException("User not login");
    }

} catch (\LogicException $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}