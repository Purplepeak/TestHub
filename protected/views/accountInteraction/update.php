<?php
/* @var $this ConfirmAccountController */
/* @var $model ConfirmAccount */

$this->breadcrumbs=array(
	'Confirm Accounts'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ConfirmAccount', 'url'=>array('index')),
	array('label'=>'Create ConfirmAccount', 'url'=>array('create')),
	array('label'=>'View ConfirmAccount', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ConfirmAccount', 'url'=>array('admin')),
);
?>

<h1>Update ConfirmAccount <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>