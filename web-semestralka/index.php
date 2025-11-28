<?php
require __DIR__ . '/vendor/autoload.php';

require_once("config/settings.php");
require_once("app/ApplicationStart.php");

$app = new ApplicationStart();
$app->appStart();


?>