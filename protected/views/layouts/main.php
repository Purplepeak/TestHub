<?php /* @var $this Controller */ 

/**
 * $userMainAvatar и $userMenuAvatar необходимы для того, чтобы показать залогиненому
 * пользователю его аватар.
 */

$userType = '';
$userId = '';
$userMainAvatar = '';
$userMenuAvatar = '';

if(!Yii::app()->user->isGuest && !empty(Yii::app()->user->__userData)) {
    $userType = CHtml::encode(Yii::app()->user->__userData['type']);
    $userId = CHtml::encode(Yii::app()->user->__userData['id']);
    $userMainAvatar = CHtml::encode(Yii::app()->user->__userMainAvatar);
    $userMenuAvatar = CHtml::encode(Yii::app()->user->__userMenuAvatar);
}

//var_dump(Yii::app()->user);

$userFunctions = array(
    array(
        'label' => 'Профиль',
        'url' => array(
            '/' . $userType . '/profile'
        )
    ),
    array(
        'label' => 'Сменить аватар',
        'url' => array(
            '/' . $userType . '/changeAvatar/'
        )
    ),
    array(
        'label' => 'Выйти',
        'url' => array(
            '/site/logout'
        )
    )
);

if ($userType === 'teacher') {
    $userFunctions = array_slice($userFunctions, 0, 1, true) + array(
        'createTest' => array(
            'label' => 'Создать тест',
            'url' => array(
                '/test/create'
            )
        ),
        'teacherTests' => array(
            'label' => 'Мои тесты',
            'url' => array(
                '/test/teacher'
            )
        ),
        'myGroups' => array(
            'label' => 'Мои Группы',
            'url' => array(
                '/group/list/f/mygroups'
            )
        ),
    ) + array_slice($userFunctions, 1, count($userFunctions) - 1, true);
}

if($userType === 'student') {
    $userFunctions = array_slice($userFunctions, 0, 1, true) + array(
        'studentTests' => array(
            'label' => 'Мои тесты',
            'url' => array(
                '/studentTest/myTests'
            )
        ),
    ) + array_slice($userFunctions, 1, count($userFunctions) - 1, true);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="icon" href="<?= Yii::app()->request->baseUrl; ?>/images/favicon.ico">
    
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/css/bootstrap-social.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/custom-bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mainpage.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/pager.css">
    
    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo Yii::app()->request->baseUrl; ?>/js/main.js"></script>
    
    <script type="text/javascript">
      var mainAvatar = "<?= $userMainAvatar ?>";
      var menuAvatar = "<?= $userMenuAvatar ?>";
    </script>
    
    <?= Yii::app()->params['timezoneDetector']->getHtmlJsCode() ?>
    
  </head>
  <body>
    <div class="container th-container">
      <div class="navbar navbar-default th-navbar-default" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a href="<?= Yii::app()->request->hostInfo . Yii::app()->request->baseUrl?>/site/index"><img class="header-img" src="<?= Yii::app()->request->baseUrl; ?>/images/logo.png"></a>
          </div>
          <div class="navbar-collapse collapse">
            <?php
$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateParents' => true,
    'htmlOptions' => array(
        'class' => 'nav navbar-nav th-navbar-nav'
    ),
    'items' => array(
        array(
            'label' => 'Главная',
            'url' => array(
                'site/index'
            )
        ),
        array(
	        'label' => 'Группы',
            'url' => array(
                'group/list'
            )
        ),
        array(
            'label' => 'Преподаватели',
            'url' => array(
                'teacher/list'
            )
        ),
    )
));

$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateParents' => true,
    'encodeLabel'=>false,
    'htmlOptions' => array(
        'class' => 'nav navbar-nav th-navbar-nav navbar-right'
    ),
    'submenuHtmlOptions' => array(
        'class' => 'dropdown-menu'
    ),
    'items' => array(
        array(
            'label' => 'Войти',
            'url' => array(
                '/site/login'
            ),
            'visible' => Yii::app()->user->isGuest,
        ),
array(
    'label' => '<img class="js-user-menu-avatar" src="">',
    'url' => array(
        '/' .$userType. '/profile'
    ),
    'linkOptions' => array(
        'class' => 'menu-avatar',
    ),
    'visible' => !Yii::app()->user->isGuest
),
        array(
            'label' => Yii::app()->user->name,
            'url' => array(
                '#'
            ),
            'linkOptions' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown'
            ),
            'itemOptions' => array(
                'class' => 'dropdown'
            ),
            'visible' => !Yii::app()->user->isGuest,
            'items' => $userFunctions
        ),
        array(
            'label' => 'Регистрация',
            'url' => array(
                '#'
            ),
            'linkOptions' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown'
            ),
            'itemOptions' => array(
                'class' => 'dropdown'
            ),
            'visible' => Yii::app()->user->isGuest,
            'items' => array(
                array(
                    'label' => 'Регистрация для студентов',
                    'url' => array(
                        '/student/registration'
                    )
                ),
                array(
                    'label' => 'Регистрация для преподавателей',
                    'url' => array(
                        '/teacher/registration'
                    )
                )
            )
        )
    )
));
?>
          </div>
          <!--/.nav-collapse -->
        </div>
        <!--/.container-fluid -->
      </div>
      <div class="jumbotron th-jumbotron">
        <?php echo $content; ?>
      </div>
    </div>
  </body>
</html>