<?php
/* @var $this StudentTestController */
/* @var $model StudentTest */

$this->breadcrumbs=array(
	'Student Tests'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List StudentTest', 'url'=>array('index')),
	array('label'=>'Manage StudentTest', 'url'=>array('admin')),
);
?>

<h1>Create StudentTest</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>