<?php
/* @var $this SocialAccountsController */
/* @var $model SocialAccounts */

$this->breadcrumbs=array(
	'Social Accounts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List SocialAccounts', 'url'=>array('index')),
	array('label'=>'Manage SocialAccounts', 'url'=>array('admin')),
);
?>

<h1>Create SocialAccounts</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>