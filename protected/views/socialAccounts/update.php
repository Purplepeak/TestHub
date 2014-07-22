<?php
/* @var $this SocialAccountsController */
/* @var $model SocialAccounts */

$this->breadcrumbs=array(
	'Social Accounts'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List SocialAccounts', 'url'=>array('index')),
	array('label'=>'Create SocialAccounts', 'url'=>array('create')),
	array('label'=>'View SocialAccounts', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage SocialAccounts', 'url'=>array('admin')),
);
?>

<h1>Update SocialAccounts <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>