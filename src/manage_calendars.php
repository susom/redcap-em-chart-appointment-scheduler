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
    $records = $module->getAllOpenSlots($suffix);
    $data = $module->prepareInstructorsSlots($records, $suffix);
    $instructors = array_keys($data);
    $pointer = 0;
    $primary = $module->getPrimaryRecordFieldName();
    if ($instructors) {
        ?>
        <div class="container">

            <div class="row"><h3>Manage Time Slots</h3></div>
            <table id="manage-calendars" class="display">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Instructor</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $pointer = 1;
                $primary = $module->getPrimaryRecordFieldName();
                foreach ($data as $slot) {
                    /**
                     * skip past slots.
                     */
                    if ($module->isSlotInPast($slot, $suffix)) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?php echo $slot[$primary] ?></td>
                        <td><?php echo $slot['instructor'] ?></td>
                        <td>
                            <?php echo $module->getLocationLabel($slot['location' . $suffix]) ?></td>
                        <td>
                            <?php echo date('m/d/Y',
                                strtotime($slot['slot_start' . $suffix])) ?>
                        </td>
                        <td><?php echo date('H:i',
                                strtotime($slot['slot_start' . $suffix])) ?> – <?php echo date('H:i',
                                strtotime($slot['slot_end' . $suffix])) ?></td>
                        <td><?php
                            if ($slot['slot_status' . $suffix] == CANCELED) {
                                ?>
                                Slot Canceled
                                <?php
                            } else {
                                ?>
                                <button type="button"
                                        data-record-id="<?php echo $slot[$primary] ?>"
                                        data-event-id="<?php echo $slot['event_id'] ?>"
                                        class="cancel-slot"><i class="fas fa-power-off"></i>
                                </button>
                                <?php
                            }
                            ?>
                            <button type="button"
                                    data-record-id="<?php echo $slot[$primary] ?>"
                                    data-event-id="<?php echo $slot['event_id'] ?>"
                                    data-location="<?php echo $slot['location' . $suffix] ?>"
                                    data-date="<?php echo date('m/d/Y',
                                        strtotime($slot['slot_start' . $suffix])) ?>"
                                    data-start="<?php echo date('h:i A',
                                        strtotime($slot['slot_start' . $suffix])) ?>"
                                    data-end="<?php echo date('h:i A', strtotime($slot['slot_end' . $suffix])) ?>"
                                    data-instructor="<?php echo $slot['instructor'] ?>"
                                    class="reschedule-slot"><i class="fas fa-edit"></i>
                            </button>
                            <button type="button"
                                    data-record-id="<?php echo $slot[$primary] ?>"
                                    data-event-id="<?php echo $slot['event_id'] ?>"
                                    data-modal-title="<?php echo date('m/d/Y',
                                        strtotime($slot['slot_start' . $suffix])) ?> <?php echo date('h:i A',
                                        strtotime($slot['slot_start' . $suffix])) ?> – <?php echo date('h:i A',
                                        strtotime($slot['slot_end' . $suffix])) ?>"
                                    class="participants-list"><i class="fas fa-list"></i>
                            </button>
                        </td>
                    </tr>
                    <?php
                    $pointer++;
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
<script src="<?php echo $module->getUrl('src/js/manage_calendar.js', true, true) ?>"></script>

