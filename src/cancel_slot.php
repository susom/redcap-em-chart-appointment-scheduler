<?php

namespace Stanford\ChartAppointmentScheduler;

use REDCap;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */


try {
    $suffix = $module->getSuffix();
    $primary = $module->getPrimaryRecordFieldName();
    $data[$primary] = filter_var($_GET['record_id'], FILTER_SANITIZE_NUMBER_INT);
    $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
    if ($data[$primary] == '') {
        throw new \LogicException('Record ID is missing');
    }
    if ($eventId == '') {
        throw new \LogicException('Event ID is missing');
    }
    if (!$module::isUserHasManagePermission()) {
        throw new \LogicException('You should not be here');
    } else {
        $data['slot_status' . $suffix] = CANCELED;
        $data['redcap_event_name'] = $module->getUniqueEventName($eventId);
        $response = \REDCap::saveData($module->getProjectId(), 'json', json_encode(array($data)));
        if (!empty($response['errors'])) {
            throw new \LogicException(implode("\n", $response['errors']));
        } else {

            $slot = ChartAppointmentScheduler::getSlot($data[$primary], $eventId, $module->getProjectId(),
                $module->getPrimaryRecordFieldName());
            $message['subject'] = $message['body'] = 'Your reservation at ' . date('m/d/Y',
                    strtotime($slot['slot_start' . $suffix])) . ' at ' . date('H:i',
                    strtotime($slot['slot_start' . $suffix])) . ' to ' . date('H:i',
                    strtotime($slot['slot_end' . $suffix])) . ' has been canceled';
            $reservationEventId = $module->getReservationEventIdViaSlotEventId($eventId);

            $module->notifyParticipants($data[$primary], $reservationEventId, $message);
            echo json_encode(array('status' => 'ok', 'message' => 'Slot canceled successfully!'));
        }
    }
} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}