<?php

use OCR5\App\App;
use OCR5\App\Session;

require('../vendor/autoload.php');

App::init();

Session::unset('flashbag');
Session::set('lastpage', $_SERVER['REQUEST_URI']);