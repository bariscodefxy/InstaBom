<?php
    $hatalariGoster = 0;
    if($hatalariGoster) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    } else {
        error_reporting(0);
    }
    set_time_limit(0);
    date_default_timezone_set('Europe/Istanbul');

    require_once 'instabom.php';