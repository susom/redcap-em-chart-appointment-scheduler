<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

if (isset($_GET['date'])) {
    /*
     * Sanitize your dates
     */
    $date = preg_replace("([^0-9/])", "", $_GET['date']);
    $eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
    $slots = $module->getDateAvailableSlots($date, $eventId);
    if (!empty($slots)) {

        $reservationEventId = $module->getReservationEventIdViaSlotEventId($eventId);

        foreach ($slots as $recordId => $record) {
            /**
             * Get first array element
             */
            $slot = array_pop(array_reverse($record));

            /**
             * skip past slots.
             */
            if ($module->isSlotInPast($slot, '')) {
                continue;
            }
            /**
             * get appointment type
             */
            $typeText = $module->getLocationLabel($slot['location']);
            $counter = $module->getParticipant()->getSlotActualCountReservedSpots($recordId,
                $reservationEventId, '', $module->getProjectId());
            ?>
            <div class="alert alert-light" role="alert">
                <?php
                if ($slot['number_of_participants'] > $counter['counter']) {

                    ?>
                    <button type="button"
                            data-record-id="<?php echo $recordId ?>" <?php echo $slot['booked'] ? 'disabled' : '' ?>
                            data-date="<?php echo date('Ymd', strtotime($slot['start'])) ?>"
                            data-event-id="<?php echo $eventId ?>"
                            data-notes-label="<?php echo $module->getNoteLabel(); ?>"
                            data-show-projects="<?php echo $module->showProjectIds(); ?>"
                            data-show-attending-options="<?php echo $module->showAttendingOptions(); ?>"
                            data-show-location-options="<?php echo $module->showLocationOptions(); ?>"
                            data-show-attending-default="<?php echo $module->getDefaultAttendingOption(); ?>"
                            data-show-notes="<?php echo $module->showNotes(); ?>"
                            data-show-locations="<?php echo(empty($slot['attending_options']) ? CAMPUS_AND_VIRTUAL : $slot['attending_options']); ?>"
                            data-start="<?php echo date('Hi', strtotime($slot['start'])) ?>"
                            data-end="<?php echo date('Hi', strtotime($slot['end'])) ?>"
                            data-modal-title="<?php echo date('h:i A',
                                strtotime($slot['start'])) ?> – <?php echo date('h:i A', strtotime($slot['end'])) ?>"
                            class="time-slot btn btn-block <?php echo $slot['booked'] ? 'disabled btn-secondary' : 'btn-success' ?>"><?php echo $module->getLocationLabel($slot['location']) . '(Slots left: ' . (int)($slot['number_of_participants'] - $counter['counter']) . ')<br>' . date('h:i A',
                                strtotime($slot['start'])) ?> – <?php echo date('h:i A',
                            strtotime($slot['end'])) ?></button>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-warning text-center"><?php echo $typeText . '<br>' . date('h:i A',
                                strtotime($slot['start'])) ?> – <?php echo date('h:i A', strtotime($slot['end'])) ?> is
                        FULL
                    </div>
                    <?php
                }
                if ($counter['userBookThisSlot']) {
                    ?>
                    <div class="alert alert-light" role="alert">
                        <p class="font-weight-bold">Reservation: </p>
                        <?php
                        //for admin few display user name and option to cancel
                        if ($module::isUserHasManagePermission()) {
                            foreach ($counter['userBookThisSlot'] as $reservation) {
                                ?>
                                <div class="alert alert-light" role="alert">
                                    <?php echo $reservation['name'] ?>
                                    <button type="button"
                                            data-participation-id="<?php echo $reservation[$module->getPrimaryRecordFieldName()] ?>"
                                            data-event-id="<?php echo $reservationEventId; ?>"
                                            class="cancel-appointment btn btn-block btn-danger col-4"
                                    ">Cancel
                                    </button>
                                </div>
                                <?php
                            }
                        } else {
                            //if not admin regular user will have only one record!
                            $reservation = end($counter['userBookThisSlot']);
                            ?>
                            <div class="alert alert-primary" role="alert">
                                <?php echo $reservation['name'] ?>
                                <button type="button"
                                        data-participation-id="<?php echo $reservation[$module->getPrimaryRecordFieldName()] ?>"
                                        data-event-id="<?php echo $reservationEventId; ?>"
                                        class="cancel-appointment btn btn-block btn-danger col-4"
                                ">Cancel
                                </button>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php

                }
                ?>
            </div>
            <?php

        }
    } else {
        echo 'No Available time slots found!';
    }

} else {
    echo 'Invalid request for time slots';
}