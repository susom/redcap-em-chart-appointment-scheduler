<?php


namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

try {
    /**
     * check if user still logged in
     */
    if (!isset($user_email)) {
        throw new \LogicException('Please login.');
    }

    $records = $module->getParticipant()->getUserParticipation(USERID, $module->getSuffix(), $module->getProjectId());
    if (count($records) > 0) {

        ?>
        <div class="container">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="active nav-item">
                    <a class="nav-link active" href="#<?php echo RESERVED_TEXT ?>" role="tab" data-toggle="tab">
                        <?php echo RESERVED_TEXT ?>
                    </a>
                </li>
                <li><a class="nav-link" href="#<?php echo CANCELED_TEXT ?>" role="tab" data-toggle="tab">
                        <?php echo CANCELED_TEXT ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#<?php echo NO_SHOW_TEXT ?>" role="tab" data-toggle="tab">
                        <?php
                        $text = str_replace("_", " ", NO_SHOW_TEXT);
                        echo $text ?>
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade active in show" id="<?php echo RESERVED_TEXT ?>">
                    <?php
                    $reservedRecords = $module->getParticipant()->getUserParticipationViaStatus($records, RESERVED,
                        $module->getSuffix());
                    if ($reservedRecords) {
                        foreach ($reservedRecords as $reserved) {
                            $slots = $module->getParticipant()->getParticipationSlotData($reserved['slot_id' . $module->getSuffix()],
                                $module->getProjectId(), $module->getPrimaryRecordFieldName());

                            foreach ($slots as $eventId => $slot) {
                                $suffix = $module->getSuffixViaEventId($eventId);
                                $reservationEventId = $module->getReservationEventIdViaSlotEventId($eventId);
                                /**
                                 * if no suffix then its no slots event ignore
                                 */
                                if (!$reservationEventId) {
                                    continue;
                                }
                                ?>
                                <div class="row">
                                    <div class="p-3 mb-2 col-lg-4 text-dark"><?php echo $slot['location' . $suffix] ?></div>
                                    <div class="p-3 mb-2 col-lg-4 text-dark">
                                        <?php echo date('m/d/Y',
                                            strtotime($slot['slot_start' . $suffix])) ?>
                                        <br><?php echo date('h:i A',
                                            strtotime($slot['slot_start' . $suffix])) ?> – <?php echo date('h:i A',
                                            strtotime($slot['slot_end' . $suffix])) ?></div>
                                    <div class="p-3 mb-2 col-lg-4 text-dark">
                                        <?php
                                        if (strtotime($slot['slot_start' . $suffix]) > time()) {
                                            ?>
                                            <button type="button"
                                                    data-participation-id="<?php echo $reserved[$module->getPrimaryRecordFieldName()] ?>"
                                                    data-event-id="<?php echo $module->getReservationEventIdViaSlotEventId($eventId) ?>"
                                                    class="cancel-appointment btn btn-block btn-danger">Cancel
                                            </button>
                                            <?php
                                        } else {
                                            ?>
                                            Appointment Completed
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        echo 'No Active Reserved Appointment at this time';
                    }
                    ?>
                </div>
                <div class="tab-pane fade" id="<?php echo CANCELED_TEXT ?>">
                    <?php
                    $canceledRecords = $module->getParticipant()->getUserParticipationViaStatus($records, CANCELED,
                        $module->getSuffix());
                    if ($canceledRecords) {
                        foreach ($canceledRecords as $canceled) {
                            $slots = $module->getParticipant()->getParticipationSlotData($canceled['slot_id' . $module->getSuffix()],
                                $module->getProjectId(), $module->getPrimaryRecordFieldName());
                            foreach ($slots as $eventId => $slot) {
                                $suffix = $module->getSuffixViaEventId($eventId);
                                ?>
                                <div class="row">
                                    <div class="p-3 mb-2 col-lg-4 text-dark"><?php echo $slot['location' . $suffix] ?></div>
                                    <div class="p-3 mb-2 col-lg-4 text-dark">
                                        <?php echo date('m/d/Y',
                                            strtotime($slot['slot_start' . $suffix])) ?>
                                        <br><?php echo date('h:i A',
                                            strtotime($slot['slot_start' . $suffix])) ?> – <?php echo date('h:i A',
                                            strtotime($slot['slot_end' . $suffix])) ?></div>
                                    <div class="p-3 mb-2 col-lg-4 text-dark"><?php echo
                                            $canceled['notes' . $suffix] . ($slot['notes' . $suffix] != '' ? '<br>Instructor Notes:' . $slot['notes' . $suffix] : '') ?></div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <div class="row">No Canceled Appointment</div>
                        <?php
                    }
                    ?>
                </div>
                <div class="tab-pane fade" id="<?php echo NO_SHOW_TEXT ?>">
                    <?php
                    $noShowRecords = $module->getParticipant()->getUserParticipationViaStatus($records, NO_SHOW,
                        $module->getSuffix());
                    if ($noShowRecords) {
                        foreach ($noShowRecords as $noShow) {
                            $slots = $module->getParticipant()->getParticipationSlotData($noShow['slot_id' . $module->getSuffix()],
                                $module->getProjectId(), $module->getPrimaryRecordFieldName());

                            foreach ($slots as $eventId => $slot) {
                                $suffix = $module->getSuffixViaEventId($eventId);
                                ?>
                                <div class="row">
                                    <div class="p-3 mb-2 col-lg-4 text-dark"><?php echo $slot['location' . $suffix] ?></div>
                                    <div class="p-3 mb-2 col-lg-4 text-dark">
                                        <?php echo date('m/d/Y',
                                            strtotime($slot['slot_start' . $suffix])) ?>
                                        <br><?php echo date('h:i A',
                                            strtotime($slot['slot_start' . $suffix])) ?> – <?php echo date('h:i A',
                                            strtotime($slot['slot_end' . $suffix])) ?></div>
                                    <div class="p-3 mb-2 col-lg-4 text-dark"><?php echo
                                            $canceled['notes' . $suffix] . ($slot['notes' . $suffix] != '' ? '<br>Instructor Notes:' . $slot['notes' . $suffix] : '') ?></div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        ?>
                        <div class="row">No Appointments has NO SHOW status.</div>
                        <?php
                    }
                    ?>
                </div>
            </div>

        </div>
        <?php
    } else {
        echo 'No saved participation for you';
    }
} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>