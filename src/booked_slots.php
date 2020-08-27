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

    //get records for all reservations.
    $records = $module->getParticipant()->getAllReservedSlots($module->getProjectId(), $module->getReservationEvents());


    $locations = parseEnum($module->getProject()->metadata['location']['element_enum']);
    $trackcovid_monthly_followup_survey_complete_statuses = parseEnum($module->getProject()->metadata['monthly_followup_survey_complete']['element_enum']);

    //get all open time slots so we can exclude past reservations.
    $slots = $module->getAllOpenSlots();
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
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($records as $id => $events) {
                    $user = $module->getParticipant()->getUserInfo($id, $module->getFirstEventId());
                    $mrn = $module->getMRN($events[$module->getFirstEventId()]);
                    foreach ($events as $eventId => $record) {
                        //skip past or empty reservation
                        if (empty($record['reservation_datetime']) || $module->isReservationInPast($record['reservation_datetime'])) {
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
                            <td><?php echo $user['consent_date'] ? 'Completed' : 'Incomplete' ?></td>
                            <td><?php echo $record['monthly_followup_survey_complete'] ? $trackcovid_monthly_followup_survey_complete_statuses[$record['monthly_followup_survey_complete']] : 'Incomplete' ?></td>
                            <td>
                                <select data-participant-id="<?php echo $id ?>"
                                        data-event-id="<?php echo $eventId ?>"
                                        class="participants-no-show">
                                    <option>CHANGE STATUS</option>
                                    <?php
                                    foreach ($module->getParticipantStatus() as $key => $status) {
                                        // list all statuses from reservation instrument. update comment.
                                        ?>
                                        <option value="<?php echo $key ?>" <?php echo($record['reservation_participant_status'] == $key ? 'selected' : '') ?>><?php echo $status ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <div class="clear"></div>
                                <strong><a target="_blank" href="
                                <?php echo rtrim(APP_PATH_WEBROOT_FULL,
                                            '/') . APP_PATH_WEBROOT . 'DataEntry/index.php?pid=' . $module->getProjectId() . '&page=chart_visit_summary&id=' . $id . '&event_id=' . $eventId ?>
                                ">Go
                                        to Visit Summary</a></strong>
                                <div class="clear"></div>
                                <strong><a target="_blank" href="<?php echo $module->getUrl('src/user.php', false,
                                            true) . '&' . $module->getProjectSetting('validation-field') . '=' . $user[$module->getProjectSetting('validation-field')] . '&id=' . $id ?>">Go
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

