<?php

$eventId = filter_var($_GET['event_id'], FILTER_SANITIZE_NUMBER_INT);
$url = $module->getUrl('src/list.php', true, true);
$instance = $module->getEventInstance();
?>
<link rel="stylesheet" href="<?php echo $module->getUrl('src/css/calendar.css', true, true) ?>">

<div class="container">
    <div class="row p-3 mb-2">
        <div class="col-8">
            <?php echo $instance['instance_description'] ?>
        </div>
        <div class="col-4 text-right">
            <a class="btn btn-danger list-view" data-key="<?php echo $eventId ?>" href="javascript:;"
               data-url="<?php echo $url . '&config=' . $key . '&event_id=' . $eventId ?>" role="button">List View</a>
        </div>
    </div>

    <div class="row">
        <div class="date-picker-2" data-toggle="popover" data-html="true" data-content=""
             placeholder="Recipient's username" id="ttry" aria-describedby="basic-addon2"></div>
        <span class="" id="example-popover-2"></span>
    </div>
</div>

<input type="hidden" name="selected-date" id="selected-date"/>
<input type="hidden" name="selected-time" id="selected-time"/>

<!-- LOAD JS -->
<script src="<?php echo $module->getUrl('src/js/calendar.js', true, true) ?>"></script>