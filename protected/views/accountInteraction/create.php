<?php
/* @var $this ConfirmAccountController */
/* @var $model ConfirmAccount */

$this->breadcrumbs=array(
	'Confirm Accounts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ConfirmAccount', 'url'=>array('index')),
	array('label'=>'Manage ConfirmAccount', 'url'=>array('admin')),
);
?>

<h1>Create ConfirmAccount</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>