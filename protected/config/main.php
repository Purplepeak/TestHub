<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

$dater = new Dater\Dater(new Dater\Locale\Ru(), 'Europe/Moscow');
$dater->setClientTimezone('Europe/London');
$timezoneDetector = new Dater\TimezoneDetector();
//$dater->setClientTimezone($timezoneDetector->getClientTimezone());

$dataHandler = new Dater\DataHandler($dater);
$dataHandler->convertRequestDataToServerTimezone();

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'TestHub',
    'aliases' => array(
        'avatarDir' => 'webroot.uploads.avatars',
        'forewordImages' => 'webroot.uploads.testImages.foreword',
        'questionImages' => 'webroot.uploads.testImages.question'
    ),
    
    // preloading 'log' component
    'preload' => array(
        'log'
    ),
    
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.components.DynamicTabularForm.*',
        'ext.giix-components.*',
        'ext.CSLinkPager',
        'ext.smailer.*',
        'ext.savatar.*',
        'ext.CJuiDateTimePicker.*',
        'ext.imperavi-redactor-widget.*'
    ),
    
    'modules' => array(
        // uncomment the following to enable the Gii tool
        
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'generatorPaths' => array(
                'ext.giix-core' // giix generators
                        ),
            'password' => '901117',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array(
                '127.0.0.1',
                '::1'
            )
        )
    ),
    
    // application components
    'components' => array(
        'user' => array(
            'class' => 'SWebUser',
            'loginUrl'=>array('site/login'),
            'allowAutoLogin' => true
        ),
        'file'=>array(
            'class'=>'ext.file.CFile',
        ),
        'authManager'=>array(
            'class'=>'SPhpAuthManager',
            'defaultRoles' => array('guest'),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            //'urlSuffix' => '/',
            'showScriptName' => false,
            'rules' => array(
                '/uploads/avatars/<id:\d+>/<res:\d+x\d+>/<method:[a-zA-z]+>/<img:[\w.]+>' => 'site/getAvatar',
                '' => 'site/index',
               '<_c:(studentTest|test|group|student)>/<_a:(myTests|teacher|list)>' => '<_c>/<_a>',
            )
        ),
        'clientScript' => array(
            'packages' => array(
                'jquery' => array(
                    'baseUrl' => '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/',
                    'js' => array('jquery.min.js'),
                )
            )
        ),
        
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=test_me',
            'enableProfiling' => YII_DEBUG_PROFILING,
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '901117',
            'charset' => 'utf8'
        ),
        
        'errorHandler' => array(
            'errorAction' => 'site/error'
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CWebLogRoute',
                    'enabled' => YII_DEBUG_SHOW_PROFILER,
                    'categories' => 'system.db.*'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info, error, warning',
                    'categories' => 'application.*'
                )
            )
        ),
        'request' => array(
            'enableCsrfValidation' => true,
            'enableCookieValidation' => true,
            'csrfCookie' => array(
                'httpOnly' => true
            )
        ),
        'session' => array(
            'cookieParams' => array(
                'httponly' => true
            )
        ),
        'soauth' => array(
            'class' => 'ext.soauth.SOauth',
            'services' => array(
                'facebook' => array(
                    'class' => 'FacebookService'
                ),
                'vk' => array(
                    'class' => 'VkService'
                ),
                'mail' => array(
                    'class' => 'MailRuService'
                )
            )
        )
    ),
    
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'dev' => true,
        'defaultMainAvatar' => '/images/default_avatar.png',
        'defaultMenuAvatar' => '/images/default_menu_avatar.png',
        'mainAvatarSize' => array(
            'width' => 190,
            'height' => 190
        ),
        'menuAvatarSize' => array(
            'width' => 30,
            'height' => 30
        ),
        'allowedAvatarSizes' => array(
            '190x190',
            '30x30'
        ),
        'avatarRelativePath' => '/uploads/avatars',
        'rememberMeTime' => 3600 * 24 * 30, // Запоминаем пользователя на указанный срок
        'privateConfig' => require (dirname(__FILE__) . '/private.php'),
        // Почта для отправки пользователям различной информации, например, при регистрации, изменении пароля и т.д.
        'siteEmail' => array(
            'email' => '', 
            'password' => ''
        ),
        'adminEmail' => 'admin@th.ru',
        'teacherAccessCode' => 'testaccess',
        'serverLocale' => 'Europe/Moscow',
        'dater' => $dater,
        'dataHandler' => $dataHandler,
        'timezoneDetector' => $timezoneDetector,
        'tmpDir' => '/uploads/tmp', // Папка для временных файлов загружаемых пользователем
        'testImages' => '/uploads/testImages', // Папка с изображениями, которые содержит тест
    )
);