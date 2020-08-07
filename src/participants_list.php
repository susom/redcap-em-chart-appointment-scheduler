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
    $suffix = $module->getSuffix();
    $recordId = filter_var($_GET['record_id'], FILTER_SANITIZE_NUMBER_INT);
    $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
    $reservationEventId = $module->getReservationEventIdViaSlotEventId($eventId);
    $participants = $module->getParticipant()->getSlotParticipants($recordId, $reservationEventId, $suffix,
        $module->getProjectId());
    if (!empty($participants)) {
        ?>
        <table id="participants-datatable" class="display">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Notes</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $pointer = 1;
            foreach ($participants as $participantId => $record) {
                $participant = $record[$reservationEventId];
                ?>
                <tr>
                    <td><?php echo $pointer ?></td>
                    <td><?php echo $participant['name' . $suffix] ?></td>
                    <td>
                        <a href="mailto:<?php echo $participant['email' . $suffix] ?>"><?php echo $participant['email' . $suffix] ?></a>
                    </td>
                    <td><?php echo $participant['mobile' . $suffix] ?></td>
                    <td><?php echo $participant['participant_notes' . $suffix] ?></td>
                    <td><?php
                        if ($participant['reservation_participant_status' . $suffix] == RESERVED) {
                            ?>
                            <!--                        <button type="button"-->
                            <!--                                data-participant-id="--><?php //echo $participantId ?><!--"-->
                            <!--                                data-event-id="--><?php //echo $reservationEventId ?><!--"-->
                            <!--                                class="participants-no-show">No Show-->
                            <!--                        </button>-->
                            <select data-participant-id="<?php echo $participantId ?>"
                                    data-event-id="<?php echo $reservationEventId ?>"
                                    class="participants-no-show">
                                <option>CHANGE STATUS</option>
                                <?php
                                foreach ($module->getParticipantStatus() as $key => $status) {
                                    // list all statuses from reservation instrument. update comment.
                                    ?>
                                    <option value="<?php echo $key ?>" <?php echo($participant['reservation_participant_status'] == $key ? 'selected' : '') ?>><?php echo $status ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <?php
                        } elseif ($participant['reservation_participant_status' . $suffix] == CANCELED) {
                            ?>
                            User cancelled this appointment!
                            <?php
                        } elseif ($participant['reservation_participant_status' . $suffix] == NO_SHOW) {
                            ?>
                            Instructor Marked this Participant as no show
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
                $pointer++;
            }
            ?>
            </tbody>
        </table>
        <?php
    } else {
        ?>
        <div class="row">
            <div class="p-3 mb-2 col-lg-12 text-dark">
                <strong>No Participants in this appointment</strong></div>
        </div>
        <?php
    }

} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}