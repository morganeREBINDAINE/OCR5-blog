<?php

use OCR5\App\App;
use OCR5\App\Session;

require '../vendor/autoload.php';

($app = new App())->init();

Session::unset('flashbag');
Session::set('lastpage', $app->getServer('REQUEST_URI'));
