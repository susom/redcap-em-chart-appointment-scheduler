<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

use REDCap;


$url = $module->getUrl('src/types.php', false, false);
$url = str_replace('pid', 'projectid', $url);
?>
<h3>Authenticated Appointment Scheduler Page</h3>
<a href="<?php echo $url ?>"><?php echo $url ?></a>
