<?php
/* @var $this ResultLookupController */
/* @var $model ResultLookup */

$this->breadcrumbs=array(
	'Result Lookups'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ResultLookup', 'url'=>array('index')),
	array('label'=>'Manage ResultLookup', 'url'=>array('admin')),
);
?>

<h1>Create ResultLookup</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>