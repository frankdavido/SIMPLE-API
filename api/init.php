<?php

if (!defined("DOCUMENT_ROOT")) {
    define("DOCUMENT_ROOT", $_SERVER['DOCUMENT_ROOT']);
}

define("TOPMOST_FILE", 'init.php'); //this is just a constant that let us know the referrer of a file. We want to initialize databse if only we browse /api/init.php directly from browser.

require_once DOCUMENT_ROOT . '/api/settings.php';
