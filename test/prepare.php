<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('simpletest/unit_tester.php');
require_once('simpletest/mock_objects.php');
if (file_exists(dirname(__FILE__) . '/../Xhwlay/Runner.php')) {
    set_include_path(realpath(dirname(__FILE__) . '/..')
        . PATH_SEPARATOR . get_include_path());
}
