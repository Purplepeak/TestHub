<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'TestHub',
    'aliases' => array(
        'avatarFolder' => 'webroot.avatars'
    ),
    
    // preloading 'log' component
    'preload' => array(
        'log'
    ),
    
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'ext.giix-components.*',
        'ext.CSLinkPager',
        'ext.smailer.*'
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
            'allowAutoLogin' => true
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                'fblogin' => 'social/fblogin',
                '' => 'site/index'
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
        )
        ,
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
        'defaultAvatar' => '/images/default_avatar.png',
        'defaultMenuAvatar' => '/images/default_menu_avatar.png',
        'avatarRelativePath' => '/avatars',
        'rememberMeTime' => 3600 * 24 * 30, // Запоминаем пользователя на указанный срок
        'privateConfig' => require (dirname(__FILE__) . '/private.php'),
        'siteEmail' => array(
            'email' => 'testhubv2@gmail.com', // Почта для отправки пользователям различной информации, например, при регистрации
            'password' => '901117testhub'
        )
    )
);