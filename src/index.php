<?php
//JS and CSS with inputs URLs
require_once 'urls.php';
try {
    $actualLink = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    //update pid variable
    $actualLink = str_replace("pid", "projectid", $actualLink);
    //update page variable
    $actualLink = str_replace("index", "types", $actualLink);
    ?>
    <div class="container">
        <h1><a href="<?php echo $actualLink ?>">Public Link</a></h1>
    </div>
    <?php
} catch (\LogicException $e) {
    echo $e->getMessage();
}
?>
