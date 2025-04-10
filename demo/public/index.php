<?php

use l24n\Twigen\Application;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../../vendor/autoload.php';

Debug::enable();

$request = Request::createFromGlobals();

$app = new Application(dirname(__DIR__));
$app->boot();

$response = $app->get('kernel')->handle($request);
$response->send();
