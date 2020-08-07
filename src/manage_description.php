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
    $instances = $module->getInstances();
    $instance = $instances[0];
    ?>
    <div class="container">
        <form>
            <div class="form-group">
                    <textarea class="form-control" id="instance-description" rows="3" name="instance_description"><?php
                        echo $instance['instance_description']
                        ?></textarea>
            </div>

            <button id="submit-instance-description" data-event-id="<?php echo $module->getEventInstance() ?>"
                        class="btn btn-primary mb-2" type="submit">Update
                </button>
            </form>
        </div>
        <?php
} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
?>

