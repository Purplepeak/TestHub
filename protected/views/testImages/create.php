<?php
/* @var $this TestImagesController */
/* @var $model TestImages */

$this->breadcrumbs=array(
	'Test Images'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List TestImages', 'url'=>array('index')),
	array('label'=>'Manage TestImages', 'url'=>array('admin')),
);
?>

<h1>Create TestImages</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>