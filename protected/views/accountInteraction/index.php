<?php
/* @var $this ConfirmAccountController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Confirm Accounts',
);

$this->menu=array(
	array('label'=>'Create ConfirmAccount', 'url'=>array('create')),
	array('label'=>'Manage ConfirmAccount', 'url'=>array('admin')),
);
?>

<h1>Confirm Accounts</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
