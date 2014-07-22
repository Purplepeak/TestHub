<?php
/* @var $this ConfirmAccountController */
/* @var $model ConfirmAccount */

$this->breadcrumbs=array(
	'Confirm Accounts'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ConfirmAccount', 'url'=>array('index')),
	array('label'=>'Create ConfirmAccount', 'url'=>array('create')),
	array('label'=>'Update ConfirmAccount', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ConfirmAccount', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ConfirmAccount', 'url'=>array('admin')),
);
?>

<h1>View ConfirmAccount #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'user_name',
		'user_id',
		'key',
		'email',
	),
)); ?>
