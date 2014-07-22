<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'TestHub',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'ext.CAdvancedArBehavior',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'901117',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'fblogin'=>'social/fblogin',
			),
		),
		
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=test_me',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '901117',
			'charset' => 'utf8',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				
				array(
					'class'=>'CWebLogRoute',
					'categories' => 'application',
            'levels'=>'error, warning, trace, profile, info',
				),
				
			),
		),
		'request'=>array(
				'enableCsrfValidation'=>true,
				'enableCookieValidation'=>true,
				'csrfCookie'=>array(
						'httpOnly'=>true,
				),
		),
		'session' => array(
            'cookieParams' => array(
                'httponly' => true,
            ),
        ),
        'soauth' => array(
            'class' => 'ext.soauth.SOauth',
            'services' => array(
        	    'facebook' => array(
        	         'class' => 'FacebookService',
                ),
                'vk' => array(
                	 'class' => 'VkService',
                ),
                'mail' => array(
                		'class' => 'MailRuService',
                ),
            ),
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
	    'defaultAvatar' => '/images/default_avatar.png',
		'rememberMeTime'=>3600*24*30, // Запоминаем пользователя на указанный срок
		'socialKeys' => require(dirname(__FILE__).'/private.php'),
		'siteEmail' => array('email' => 'testhubme@gmail.com', 'password' => '901117901117')// Почта для отправки пользователям различной информации, например при регистрации
	),
);