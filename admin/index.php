<?php
@session_start();
// 加载当前模块配置文件，（内部文件查找，基于服务器磁盘绝对定位）
$config = require(__DIR__ . '/config.php');
// 加载mvc类
require($config['PATH_MVC'] . '/mvc.php');
mvc::execute($config);