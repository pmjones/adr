<?php
require dirname(__DIR__) . "/vendor/autoload.php";
use Aura\Web\WebFactory;

$web_factory = new WebFactory($GLOBALS);
$request = $web_factory->newRequest();
$response = $web_factory->newResponse();

$helper = require dirname(__DIR__) . "/vendor/aura/html/scripts/instance.php";

$view_factory = new \Aura\View\ViewFactory;
$view = $view_factory->newInstance($helper);
