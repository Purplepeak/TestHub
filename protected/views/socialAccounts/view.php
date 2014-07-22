<?php
/* @var $this SocialAccountsController */
/* @var $model SocialAccounts */

$this->breadcrumbs=array(
	'Social Accounts'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List SocialAccounts', 'url'=>array('index')),
	array('label'=>'Create SocialAccounts', 'url'=>array('create')),
	array('label'=>'Update SocialAccounts', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete SocialAccounts', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage SocialAccounts', 'url'=>array('admin')),
);
?>

<h1>View SocialAccounts #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'provider',
		'social_user_id',
		'info',
		'user_id',
	),
)); ?>
