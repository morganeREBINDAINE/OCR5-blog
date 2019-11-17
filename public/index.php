<?php

use OCR5\App\App;

require('../vendor/autoload.php');

App::init();

unset($_SESSION['flashbag']);
$_SESSION['last_page'] = 'http://blog'.$_SERVER['REQUEST_URI'];