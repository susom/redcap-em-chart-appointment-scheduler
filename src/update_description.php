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


    $module->setProjectSetting('instance_description', array($_POST['description']), $module->getProjectId());

    echo json_encode(array('status' => 'ok', 'message' => 'Instance Description Updated Successfully!'));

} catch (\LogicException $e) {
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}