<?php


namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

try {
    /**
     * check if user still logged in
     */
    $module->emLog("start");
    if (!$module::isUserHasManagePermission()) {
        throw new \LogicException('You cant be here');
    }

    $module->emLog("after permissions");
    //get records for all reservations.
    $records = $module->getParticipant()->getAllReservedSlots($module->getProjectId(), $module->getReservationEvents());
    $module->emLog("after records");

    $locations = parseEnum($module->getProject()->metadata['location']['element_enum']);
    $trackcovid_monthly_followup_survey_complete_statuses = parseEnum($module->getProject()->metadata['chart_study_followup_survey_complete']['element_enum']);
    $reservation_statuses = parseEnum($module->getProject()->metadata['reservation_participant_status']['element_enum']);
    $statuses = parseEnum($module->getProject()->metadata['visit_status']["element_enum"]);

    $url = $module->getUrl('src/user.php', false,
        true);
    //get all open time slots so we can exclude past reservations.
    $slots = $module->getAllOpenSlots();
    $validationField = $module->getProjectSetting('validation-field');
    $firstEvent = $module->getFirstEventId();
    $module->emLog("before if statements");
    if ($records) {
        ?>
        <div class="container-fluid">
            <table id="booked-slots" class="display">
                <thead>
                <tr>
                    <th>Record ID</th>
                    <th>Participant ID</th>
                    <th>Demographics Information</th>
                    <th>Visit type</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Appointment time</th>
                    <th>Consent status</th>
                    <th>Survey status</th>
                    <th>Visit status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($records as $id => $events) {
                    $user = $events[$firstEvent];
                    foreach ($events as $eventId => $record) {
                        //skip past, skipped or empty reservation
                        if (empty($record['reservation_datetime']) || $module->isReservationInPast($record['reservation_datetime']) || $module->isAppointmentSkipped($record['visit_status'])) {
                            continue;
                        }

                        //exception for imported reservation.
                        if (empty($record['reservation_slot_id'])) {
                            $slot['slot_start'] = $record['reservation_datetime'];
                            // because we do not know the end of the lost we assumed its 15 minutes after the start
                            $slot['slot_end'] = date('Y-m-d H:i:s', strtotime($record['reservation_datetime']) + 900);
                        } else {
                            //if past reservation we do not want to see it.
                            if (!array_key_exists($record['reservation_slot_id'], $slots)) {
                                continue;
                            } else {
                                $slot = end($slots[$record['reservation_slot_id']]);
                            }
                        }

                        ?>
                        <tr>
                            <td><?php echo $id ?></td>
                            <td><?php echo $record['participant_id'] ?></td>
                            <td>
                                <div class="row"><?php echo $user['name'] ?>
                                    DOB:<?php echo $user['dob'] ? date('m/d/Y', strtotime($user['dob'])) : '' ?></div>
                                <div class="row">Email: <?php echo $user['email'] ?> </div>
                                <div class="row">Phone: <?php echo $user['phone'] ?></div>
                                <div class="row">MRN: <?php echo $user['mrn_all_sites'] ?></div>
                            </td>
                            <td><?php echo $module->getProject()->events[1]['events'][$eventId]['descrip'] ?></td>
                            <!--                            <td>-->
                            <td><?php echo $locations[$record['reservation_participant_location']]; ?></td>
                            <td><?php echo date('m/d/Y', strtotime($slot['slot_start'])) ?></td>
                            <td><?php echo date('H:i', strtotime($slot['slot_start'])) . ' - ' . date('H:i',
                                        strtotime($slot['slot_end'])) ?></td>
                            <td><?php echo $user['calc_consent_valid'] ? 'Completed' : 'Incomplete' ?></td>
                            <td><?php echo $record['chart_study_followup_survey_complete'] ? $trackcovid_monthly_followup_survey_complete_statuses[$record['chart_study_followup_survey_complete']] : 'Incomplete' ?></td>
                            <td><?php echo $statuses[$record['visit_status']]; ?></td>
                            <td>
                                <button data-participant-id="<?php echo $id ?>"
                                        data-event-id="<?php echo $eventId ?>"
                                        data-status="<?php echo false ?>"
                                        class="participants-no-show btn btn-sm btn-danger">Cancel
                                </button>
                                <div class="clear"></div>
                                <strong><a target="_blank" href="
                                <?php echo rtrim(APP_PATH_WEBROOT_FULL,
                                            '/') . APP_PATH_WEBROOT . 'DataEntry/index.php?pid=' . $module->getProjectId() . '&page=chart_visit_summary&id=' . $id . '&event_id=' . $eventId ?>
                                ">Go
                                        to Visit Summary</a></strong>
                                <div class="clear"></div>
                                <strong><a target="_blank"
                                           href="<?php echo $url . '&' . $validationField . '=' . $user[$validationField] . '&id=' . $id ?>">Go
                                        to Scheduling Page</a></strong>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>


        </div>
        <?php
    } else {
        echo 'No saved participation for you';
    }
} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>
<!-- LOAD JS -->
<script src="<?php echo $module->getUrl('src/js/manage_calendar.js') ?>"></script>

