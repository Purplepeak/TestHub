<?php
mb_internal_encoding('utf-8');
date_default_timezone_set('Europe/Moscow');
require dirname(__FILE__) . '/vendor/autoload.php';

$yii = dirname(__FILE__) . '/vendor/yiisoft/yii/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/main.php';

// debug
defined('YII_DEBUG') or define('YII_DEBUG', true);
// show profiler
defined('YII_DEBUG_SHOW_PROFILER') or define('YII_DEBUG_SHOW_PROFILER', true);
// enable profiling
defined('YII_DEBUG_PROFILING') or define('YII_DEBUG_PROFILING', true);
// trace level
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 0);
// execution time
defined('YII_DEBUG_DISPLAY_TIME') or define('YII_DEBUG_DISPLAY_TIME', false);

require_once ($yii);
Yii::createWebApplication($config)->run();

if (YII_DEBUG_DISPLAY_TIME)
    echo Yii::getLogger()->getExecutionTime();