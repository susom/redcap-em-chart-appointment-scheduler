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
    $records = $module->getParticipant()->getAllReservedSlots($module->getProjectId());

    //get all open time slots so we can exclude past reservations.
    $slots = $module->getAllOpenSlots();
    if ($records) {
        ?>
        <div class="container-fluid">
            <table id="booked-slots" class="display">
                <thead>
                <tr>
                    <th>Record ID</th>
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
                    foreach ($events as $eventId => $record) {
                        //if past reservation we do not want to see it.
                        if (!array_key_exists($record['reservation_slot_id'], $slots)) {
                            continue;
                        } else {
                            $slot = end($slots[$record['reservation_slot_id']]);
                        }
                        $locations = parseEnum($module->getProject()->metadata['location']['element_enum']);
                        $trackcovid_monthly_followup_survey_complete_statuses = parseEnum($module->getProject()->metadata['monthly_followup_survey_complete']['element_enum']);
                        ?>
                        <tr>
                            <td><?php echo $id ?></td>
                            <td>
                                <div class="row"><?php echo $user['first_name'] . ' ' . $user['last_name'] ?>
                                    DOB:<?php echo $user['dob'] ? date('m/d/Y', strtotime($user['dob'])) : '' ?></div>
                                <div class="row"><?php echo $user['email'] ?> </div>
                                <div class="row"><?php echo $user['phone_number'] ?></div>
                            </td>
                            <td><?php echo $module->getProject()->events[1]['events'][$eventId]['descrip'] ?></td>
                            <!--                            <td>-->
                            <td><?php echo $locations[$record['reservation_participant_location']]; ?></td>
                            <td><?php echo date('m/d/Y', strtotime($slot['start'])) ?></td>
                            <td><?php echo date('H:i', strtotime($slot['start'])) . ' - ' . date('H:i',
                                        strtotime($slot['end'])) ?></td>
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
                                <strong><a target="_blank" href="<?php echo rtrim(APP_PATH_WEBROOT_FULL,
                                            '/') . APP_PATH_WEBROOT . 'DataEntry/index.php?pid=' . $module->getProjectId() . '&page=trackcovid_visit_summary&id=' . $id . '&event_id=' . $eventId ?>">Go
                                        to Visit Summary</a></strong>
                                <div class="clear"></div>
                                <strong><a target="_blank" href="<?php echo $module->getUrl('src/user.php', false,
                                            true) . '&code=' . $id . '&zip=' . $user['zipcode_abs'] ?>">Go
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

