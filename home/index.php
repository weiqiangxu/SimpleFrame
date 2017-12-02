<?php
header("Access-Control-Allow-Origin: *");
@session_start();
$config = require(__DIR__ . '/config.php');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', $config['COOKIE_DOMAIN']);
require($config['PATH_MVC'] . '/mvc.php');
mvc::execute($config);