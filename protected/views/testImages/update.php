<?php
/* @var $this TestImagesController */
/* @var $model TestImages */

$this->breadcrumbs=array(
	'Test Images'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List TestImages', 'url'=>array('index')),
	array('label'=>'Create TestImages', 'url'=>array('create')),
	array('label'=>'View TestImages', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage TestImages', 'url'=>array('admin')),
);
?>

<h1>Update TestImages <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>