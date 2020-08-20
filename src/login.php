<?php

namespace Stanford\ChartAppointmentScheduler;

/** @var \Stanford\ChartAppointmentScheduler\ChartAppointmentScheduler $module */

use REDCap;

//JS and CSS with inputs URLs
require_once 'urls.php';
if (!isset($_COOKIE['participant_login'])) {
    ?>
    <link rel="stylesheet" href="<?php echo $module->getUrl('src/css/verification_form.css', true, true) ?>">
    <script src="<?php echo $module->getUrl('src/js/login.js', true, true) ?>"></script>
    <script>
        Form.ajaxURL = "<?php echo $module->getUrl("src/verify.php", true,
                true) . '&pid=' . $module->getProjectId() . '&NOAUTH'?>"
    </script>
    <style>
        #pagecontainer {
            margin: 0 auto;
            max-height: 100%;
            padding: 0 0 10px;
            text-align: left;
            max-width: 800px;
            border: 1px solid #ccc;
            border-top: 0;
            border-bottom: 0;
        }

        #example_img {
            position: absolute;
            width: 100%;
            height: 500px;
            max-width: 696px;
            left: 50%;
            margin-left: -348px;
            top: 10%;
            z-index: 10;
            background: url(<?php echo $module->getUrl('src/images/example_code.png', false, true) ?>) no-repeat;
            background-size: contain;
        }

        .example_code {
            width: 100%;
            height: 100vh;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
            display: none;
        }

        .example_code:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #333;
            opacity: .8;
            z-index: 3;
        }

        #trackcovid-background {
            float: none;
            left: 5%;
            right: 50%;
        }

        #title {
            text-align: center;
            margin: 20px auto;
            background-image: none;
            background-color: #faf7f4;
            color: #554948;
            font-weight: bold;
            font-size: 22px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .help_text {
            color: #0a6ebd;
            font-size: smaller;
        }

        .fa-question-circle:before {
            content: "\f059";
        }

        .code_info > i {
            color: #0a6ebd;
        }
    </style>
    <div id="pagecontainer">
        <div class="row offset-3 col-5 center-block" id="trackcovid-background">
            <div class="row">
                <div style="padding:10px 0 0;"><img id="survey_logo"
                                                    src="https://redcap.stanford.edu/api/?type=module&prefix=chart_appointment_scheduler&page=src%2Fimages%2Fchart_logo.png"
                                                    alt="image" title="Chart Logo" class="img-fluid h-auto">
                </div>
            </div>
        </div>

        <div class="row">
            <h1 id="title">CHART Login</h1>
        </div>
        <div id="new-form" class="container ">
            <h3>Please click on your survey link that was emailed to login to your scheduer.</h3>
        </div>

    </div>
    <div class="example_code">
        <div id="example_img"></div>
    </div>
    <?php
} else {
    //todo redirect to complete list.
}
?>
