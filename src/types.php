<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

use REDCap;


$url = $module->getUrl('src/list.php', false, true, true);
$instances = $module->getInstances();
$calendar = $module->getUrl('src/calendar.php', true, true) . '&projectid=' . $module->getProjectId();

$managerURL = $module->getUrl('src/manager.php', false, false, true) . '&projectid=' . $module->getProjectId();
?>


<?php

//JS and CSS with inputs URLs
require_once 'urls.php';
?>
<link rel="stylesheet" href="<?php echo $module->getUrl('src/css/types.css', true, true) ?>">
<div id="brandbar">
    <div class="container">
        <div class="row">
            <div class="col-3">
                <a href="http://www.stanford.edu"><img
                            src="https://www-media.stanford.edu/su-identity/images/brandbar-stanford-logo@2x.png"
                            alt="Stanford University" width="152" height="23"></a>
            </div>
            <div class="col-9">
                <nav class="navbar-expand-sm navbar-dark">
                    <div class="collapse navbar-collapse justify-content-end" id="navbarCollapse">
                        <?php
                        if (defined('USERID')) {
                            ?>
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="#">Logged in
                                        as: <?php echo(defined('USERID') ? USERID : ' NOT LOGGED IN') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="manage nav-link" href="#">Manage my Appointments</a>
                                </li>
                                <?php
                                if ($module::isUserHasManagePermission()) {
                                    ?>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link " href="<?php echo $managerURL ?>" id="navbarDropdown">
                                            Go to Manager Page
                                        </a>
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
        </div>
    </div>
</div>
<div class="container">
    <?php
    foreach ($instances as $instance) {
        $title = $instance['title'];
        if (isset($_GET['complementary']) && $_GET['complementary'] == 'true') {
            $slotsEventId = $instance['survey_complementary_slot_event_id'];
            $reservationEventId = $instance['survey_complementary_reservation_event_id'];
        } else {
            $slotsEventId = $instance['slot_event_id'];
            $reservationEventId = $instance['reservation_event_id'];
        }
        // get the event array to know the number of offset days to baseline visit
        $event = $module->getProject()->events['1']['events'][$reservationEventId];
        list($month, $year) = $module->getEventMonthYear($event['day_offset']);
        ?>

        <div class="card">
            <input type="hidden" id="<?php echo $slotsEventId ?>-reservation-event-id"
                   value="<?php echo $reservationEventId ?>"
                   class="hidden"/>
            <div class="card-header" id="headingOne">
                <div class="float-left">
                    <button class="type btn btn-link collapsed" type="button"
                            data-month="<?php echo $month ?>"
                            data-year="<?php echo $year ?>"
                            data-toggle="collapse-<?php echo $slotsEventId ?>"
                            data-target="#collapse-<?php echo $slotsEventId ?>" aria-expanded="true"
                            aria-controls="collapse-<?php echo $slotsEventId ?>"
                            data-url="<?php echo $url . '&event_id=' . $slotsEventId . '&' . PROJECTID . '=' . $module->getProjectId() . '&month=' . $month . '&year=' . $year . (!defined('USERID') ? '&NOAUTH' : '') ?>"
                            data-key="<?php echo $slotsEventId ?>"
                            data-default-view="<?php echo $instance['default_view'] ?>"
                            data-name="<?php echo $title ?>">
                        <h4><?php echo $title ?></h4>
                    </button>
                </div>
                <div class="float-right">
                    <a class="btn btn-danger calendar-view" data-key="<?php echo $slotsEventId ?>"
                       href="javascript:;"
                       data-url="<?php echo $calendar . '&event_id=' . $slotsEventId ?>" role="button">
                        Calendar View
                    </a>
                </div>
            </div>

            <div id="collapse-<?php echo $slotsEventId ?>" class="collapse" aria-labelledby="headingOne"
                 data-parent="#accordionExample">
                <div class="card-body" id="<?php echo $slotsEventId ?>-calendar">
                    <div id="<?php echo $slotsEventId ?>-<?php echo $month ?>-<?php echo $year ?>-list-view">
                        <div class="row">
                            <div class="col-12">
                                <?php
                                echo $instance['instance_description']
                                ?>
                            </div>
                            <!--                                <div class="col-2 text-right">-->
                            <!--                                    <a class="btn btn-danger calendar-view" data-key="-->
                            <?php //echo $slotsEventId ?><!--"-->
                            <!--                                       href="javascript:;"-->
                            <!--                                       data-url="-->
                            <?php //echo $calendar . '&event_id=' . $slotsEventId ?><!--" role="button">Calendar-->
                            <!--                                        View</a>-->
                            <!--                                </div>-->
                        </div>
                        <hr>
                        <table class="list-result display table table-striped table-bordered"
                               cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Time(PDT)</th>
                                <th>Available Slots</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div id="<?php echo $slotsEventId ?>-calendar-view">

                    </div>
                </div>
            </div>
        </div>

        <?php
    }
    ?>

</div>

<!-- LOAD JS -->
<script src="<?php echo $module->getUrl('src/js/types.js', true, true) ?>"></script>

<!-- LOAD MODALS -->
<?php
require_once 'models.php';
?>
<div class="loader"><!-- Place at bottom of page --></div>
