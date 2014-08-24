<?php /* @var $this Controller */ ?>
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
    <script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
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
                '#'
            ),
            'linkOptions' => array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown'
            ),
            'visible' => Yii::app()->user->isGuest,
            'itemOptions' => array(
                'class' => 'dropdown'
            ),
            'items' => array(
                array(
                    'label' => 'Вход для студентов',
                    'url' => array(
                        '/student/login'
                    )
                ),
                array(
                    'label' => 'Вход для преподавателей',
                    'url' => array(
                        '/teacher/login'
                    )
                )
            )
        ),
        array(
            'label' => 'Выйти (' . Yii::app()->user->name . ')',
            'url' => array(
                '/site/logout'
            ),
            'visible' => !Yii::app()->user->isGuest
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