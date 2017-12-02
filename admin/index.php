<?php
@session_start();
$config = require(__DIR__ . '/config.php');
require($config['PATH_MVC'] . '/mvc.php');
mvc::execute($config);