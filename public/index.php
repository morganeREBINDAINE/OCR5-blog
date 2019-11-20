<?php

use OCR5\App\App;

require('../vendor/autoload.php');

App::init();

unset($_SESSION['flashbag']);
$_SESSION['last_page'] = $_SERVER['REQUEST_URI'];