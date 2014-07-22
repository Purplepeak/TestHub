<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
    <link rel="icon" href="<?php echo Yii::app()->request->baseUrl; ?>/images/favicon.ico">
  
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mainpage.css" />
	
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/css/bootstrap-social.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap/css/font-awesome.css" />

	<title><?= CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">
	<div id="menu-top">
<?php
$this->widget('zii.widgets.CMenu',array(
  'activeCssClass'=>'active',
  'activateParents'=>true,
  'items'=>array(
    array(
		'label'=>'Главная',
		'url'=>array('/site/index'),
		'linkOptions'=>array('id'=>'menuBlog')
    ),

    array(
      'label'=>'Войти',
      'url'=>array('site/index'),
      'linkOptions'=>array('id'=>'menuUser'),
      'itemOptions'=>array('id'=>'itemUser'),
      'visible'=>Yii::app()->user->isGuest,
      'items'=>array(
        array('label'=>'Вход для студентов', 'url'=>array('/student/login')),
        array('label'=>'Вход для преподавателей', 'url'=>array('/teacher/login')),
      ),
    ),

    array(
        'label'=>'Выйти ('.Yii::app()->user->name.')', 
        'url'=>array('/site/logout'), 
        'visible'=>!Yii::app()->user->isGuest),

   array(
		'label'=>'Регистрация',
		'url'=>array('site/index'),
		'linkOptions'=>array('id'=>'menuUser'),
		'itemOptions'=>array('id'=>'itemUser'),
		'visible'=>Yii::app()->user->isGuest,
		'items'=>array(
				array('label'=>'Регистрация для студентов', 'url'=>array('/student/registration')),
				array('label'=>'Регистрация для преподавателей', 'url'=>array('/teacher/registration')),
		),
   ),
  ),
)); ?>
</div>
	<?php echo $content; ?>
</div><!-- page -->

</body>
</html>
