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
            margin: 0px auto;
            left: 8%;
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
        <div class="row col-10 center-block" id="trackcovid-background">
            <div class="row">
                <div style="padding:10px 0 0;"><img id="survey_logo"
                                                    src="https://redcap.stanford.edu/surveys/index.php?pid=20184&amp;doc_id_hash=a825369bc1de8a7ce50ec7a5765c7fee0a79fc65&amp;__passthru=DataEntry%2Fimage_view.php&amp;s=DPXJ7FAJCX&amp;id=924268"
                                                    alt="image" title="image"
                                                    style="max-width:600px;width:559px;max-width:559px;height:145px;max-height:145px;">
                </div>
            </div>
        </div>

        <div class="row">
            <h1 id="title">TrackCOVID Login</h1>
        </div>
        <div id="new-form" class="container ">

            <div id="errors" class="row col-10 offset-1 text-left alert alert-danger" style="display: none"></div>
            <section>
                <h2 class="code_info">Enter your ID<br><span class="help_text">Where is my ID</span> <i
                            class="far fa-question-circle"></i></h2>
                <div class="row">
                    <div class="col-1"><input data-num="1" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0" type="text" maxLength="1"
                                              size="5" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="2" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" " />
                    </div>
                    <div class="col-1"><input data-num="3" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5"/></div>
                    <div class="col-1"><input data-num="4" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="5" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="6" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="7" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="8" data-type="newuniq"
                                              class="newuniq overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" pattern="[0-9]{1}"/></div>
                </div>
            </section>
            <section><h2>Postal Code</h2>
                <div class="row">
                    <div class="col-1"><input data-num="1" data-type="zipcode"
                                              class="zipcode overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" min="0" max="9" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="2" data-type="zipcode"
                                              class="zipcode overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" min="0" max="9" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="3" data-type="zipcode"
                                              class="zipcode overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" min="0" max="9" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="4" data-type="zipcode"
                                              class="zipcode overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" min="0" max="9" pattern="[0-9]{1}"/></div>
                    <div class="col-1"><input data-num="5" data-type="zipcode"
                                              class="zipcode overflow-auto form-control p-0 " type="text" maxLength="1"
                                              size="5" min="0" max="9" pattern="[0-9]{1}"/></div>
                </div>
            </section>
            <section class="verify">
                <div class="row">
                    <button id="verify" type="button" class="btn btn-info btn-lg btn-block">Login</button>
                </div>
            </section>
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
