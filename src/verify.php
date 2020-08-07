<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

try {
    $newuniq = strtoupper(filter_var($_POST['newuniq'], FILTER_SANITIZE_STRING));
    $zipcode_abs = filter_var($_POST['zipcode_abs'], FILTER_VALIDATE_INT);

    if ($module->verifyUser($newuniq, $zipcode_abs)) {
        //$module->setUserCookie('login', $module->generateUniqueCodeHash($newuniq));
        echo json_encode(array(
            'status' => 'success',
            'cookie' => $module->generateUniqueCodeHash($newuniq),
            'link' => $module->getUrl('src/user.php', true, true) . '&pid=' . $module->getProjectId() . '&NOAUTH'
        ));
    } else {
        throw new \LogicException("No user was found for provided information");
    }


} catch (\LogicException $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
} catch (\Exception $e) {
    $module->emError($e->getMessage());
    http_response_code(404);
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}