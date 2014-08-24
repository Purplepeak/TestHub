<?php 
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Cron',

    'preload'=>array('log'),

    'import'=>array(
        'application.components.*',
        'application.models.*',
    ),
    
    'components'=>array(
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron.log',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron_trace.log',
                    'levels'=>'trace',
                ),
            ),
        ),

        'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=test_me',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '901117',
			'charset' => 'utf8',
		),
    ),
    'params' => array(
        'purificationTime' => 2, // Через сколько дней удалять неактивированные аккаунты
    )
);