<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

use REDCap;


try {
    if (!defined('USERID') || !$module::isUserHasManagePermission()) {
        throw new \LogicException("You are allowed to access this page");
    }

    $url = $module->getUrl('src/types.php', false, true, true) . '&projectid=' . $module->getProjectId();
} catch (\LogicException $e) {
    echo $e->getMessage();
}

?>


<?php

//JS and CSS with inputs URLs
require_once 'urls.php';
?>
<link rel="stylesheet" href="<?php echo $module->getUrl('src/css/types.css', true, true) ?>">
<div id="brandbar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <a href="http://www.stanford.edu"><img
                            src="<?php echo $module->getUrl('src/images/trackcovid_logo.png', true,
                                true) ?>"
                            alt="TrackCovid" class="h-auto"></a>
            </div>
            <div class="col-9">
                <nav class="navbar-expand-sm navbar-light">
                    <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
                        <?php
                        if (defined('USERID')) {
                            ?>
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="#">Logged in
                                        as: <?php echo(defined('USERID') ? USERID : ' NOT LOGGED IN') ?></a>
                                </li>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>
                </nav>
            </div>
        </div>

    </div>
</div>
<!-- below code need to be hidden to trigger jquery click -->
<div class="container" style="display: none">
    <nav class="navbar navbar-expand-sm bg-light navbar-light">

        <div class="collapse navbar-collapse justify-content-end hidden" id="navbarCollapse">
            <?php
            if (defined('USERID')) {
                ?>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="manage nav-link" href="#">Manage my Appointments</a>
                    </li>
                    <?php
                    if ($module::isUserHasManagePermission()) {
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Manage Calendar
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="manage-calendars nav-link" href="#">Manage Time Slots</a>
                                <a class="booked-slots nav-link" href="#">Manage Booked Slots</a>
                                <a class="instance-description nav-link" href="#">Manage Instance Description</a>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
        </div>
    </nav>
</div>

<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="booked-tab" data-toggle="tab" href="#booked" role="tab"
               aria-controls="booked"
               aria-selected="false">Manage Booked Slots</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="manage-tab" data-toggle="tab" href="#manage" role="tab"
               aria-controls="manage" aria-selected="true">Manage Time Slots</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="description-tab" data-toggle="tab" href="#description" role="tab"
               aria-controls="description" aria-selected="false">Manage Description</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade  show active  card card-body" id="booked" role="tabpanel"
             aria-labelledby="booked-tab">
            <div id="booked-container"></div>
        </div>
        <div class="tab-pane fadecard card-body" id="manage" role="tabpanel" aria-labelledby="manage-tab">
            <div id="manager-container"></div>
        </div>
        <div class="tab-pane fade card card-body" id="description" role="tabpanel" aria-labelledby="description-tab">
            <div id="instance-description-container"></div>
        </div>
    </div>

</div>
<!-- Reschedule Modal -->
<div class="modal" id="reschedule">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Reschedule Time Slot</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form id="reschedule-form">
                    <input type="hidden" name="reschedule-record-id" id="reschedule-record-id"/>
                    <input type="hidden" name="reschedule-event-id" id="reschedule-event-id"/>
                    <div class="form-group">
                        <label for="start">Start time</label>
                        <input type="text" name="start" class="form-control" id="start"
                               placeholder="Office Hours Start Time" required>
                    </div>
                    <div class="form-group">
                        <label for="end">End time</label>
                        <input type="text" name="end" class="form-control" id="end" placeholder="Office Hours End Time"
                               required>
                    </div>
                    <!--                    <div class="form-group">-->
                    <!--                        <label for="instructor">Instructor (Please type SUNet ID)</label>-->
                    <!--                        <input type="text" name="instructor" class="form-control" id="instructor"-->
                    <!--                               placeholder="Instructor SUNet ID" required>-->
                    <!--                    </div>-->
                    <div class="form-group">
                        <label for="location">Location</label>
                        <select class="form-control" name="location" id="location" required>
                            <option>select location</option>
                            <?php
                            foreach ($module->getDefinedLocations() as $key => $location) {
                                ?>
                                <option value="<?php echo $key ?>"><?php echo $location ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="reschedule-notes" id="reschedule-notes"
                                  rows="3"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="submit" id="submit-reschedule-form" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- END Booking Modal -->

<!-- Generic Modal -->

<div class="modal " id="generic-manager-modal">
    <div class="modal-dialog mw-100 w-75">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Modal Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                Modal body..
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<!-- END Time Slots Modal -->

<!-- LOAD JS -->
<script src="<?php echo $module->getUrl('src/js/types.js', true, true) ?>"></script>
<script src="<?php echo $module->getUrl('src/js/manager.js', true, true) ?>"></script>
<script src="<?php echo $module->getUrl('src/js/manage_calendar.js', true, true) ?>"></script>

<div class="loader"><!-- Place at bottom of page --></div>
