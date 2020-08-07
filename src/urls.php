<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */
//if(!isset($_COOKIE['participant_login'])) {
//    redirect($module->getUrl('src/login', false,false));
//}
?>
<!DOCTYPE HTML>
<html>
<?php
if (!$module->getMainSurveyId()) {

?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
<script src="//code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

</html>
<?php
}

//define NOAUTH
$noAuth = '';
if (!defined('USERID') || USERID == '[survey respondent]') {
    $noAuth = '&NOAUTH';
}
?>

<!--
        ***********Add COMPLEMENTARY_SUFFIX to the end of each URL so the suffix can be loaded when ever you instantiate $module ***********
        -->
<input type="hidden" id="slots-url" value="<?php echo $module->getUrl('src/slots.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="calendar-url" value="<?php echo $module->getUrl('src/calendar.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="book-slot-url" value="<?php echo $module->getUrl('src/slots.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="book-submit-url" value="<?php echo $module->getUrl('src/book.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="summary-url" value="<?php echo $module->getUrl('src/summary.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="list-view-url" value="<?php echo $module->getUrl('src/list.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="manage-url" value="<?php echo $module->getUrl('src/manage.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="instance-description-update-url"
       value="<?php echo $module->getUrl('src/update_description.php', false,
               true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="manage-calendar-url"
       value="<?php echo $module->getUrl('src/manage_calendars.php', false,
               true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="manage-booked-slots-url"
       value="<?php echo $module->getUrl('src/booked_slots.php', false,
               true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="manage-instance-description"
       value="<?php echo $module->getUrl('src/manage_description.php', false,
               true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="cancel-appointment-url"
       value="<?php echo $module->getUrl('src/cancel.php', false,
               true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&' . PROJECTID . '=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<!--URLS for admin functionality need pid instead of projectid because we need to be in project context and admin MUST be in the project -->
<input type="hidden" id="cancel-slot-url" value="<?php echo $module->getUrl('src/cancel_slot.php',
        false) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="reschedule-submit-url" value="<?php echo $module->getUrl('src/reschedule.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="participants-list-url"
       value="<?php echo $module->getUrl('src/participants_list.php', false,
               true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>

<input type="hidden" id="participants-no-show-url" value="<?php echo $module->getUrl('src/no_show.php', false,
        true) . '&' . COMPLEMENTARY_SUFFIX . '=' . $module->getSuffix() . '&pid=' . $module->getProjectId() . $noAuth ?>"
       class="hidden"/>
<input type="hidden" id="event-id" value="" class="hidden"/>
<input type="hidden" id="user-email" value="<?php echo $user_email ?>" class="hidden"/>
<input type="hidden" id="complementary-suffix" value="<?php echo $module->getSuffix() ?>" class="hidden"/>
<input type="hidden" id="survey-scheduler-header"
       value="<?php echo(isset($_GET['pid']) ? end($module->getProjectSetting("survey-scheduler-header")) : '') ?>"
       class="hidden"/>

<!-- trigger below instance after loading the page. -->
<input type="hidden" name="triggered-instance" id="triggered-instance"
       value="<?php echo(isset($_GET['trigger']) ? filter_var($_GET['trigger'],
           FILTER_SANITIZE_STRING) : '') ?>">