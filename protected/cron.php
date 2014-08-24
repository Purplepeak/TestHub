<?php 
defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once(dirname(__FILE__) . '/../vendor/yiisoft/yii/framework/yii.php');

$configFile=dirname(__FILE__) . '/config/cron.php';

Yii::createConsoleApplication($configFile)->run();