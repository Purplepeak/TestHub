<?php
/* @var $this ResultLookupController */
/* @var $model ResultLookup */

$this->breadcrumbs=array(
	'Result Lookups'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ResultLookup', 'url'=>array('index')),
	array('label'=>'Create ResultLookup', 'url'=>array('create')),
	array('label'=>'View ResultLookup', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ResultLookup', 'url'=>array('admin')),
);
?>

<h1>Update ResultLookup <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>