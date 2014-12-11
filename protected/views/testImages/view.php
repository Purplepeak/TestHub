<?php
/* @var $this TestImagesController */
/* @var $model TestImages */

$this->breadcrumbs=array(
	'Test Images'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List TestImages', 'url'=>array('index')),
	array('label'=>'Create TestImages', 'url'=>array('create')),
	array('label'=>'Update TestImages', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete TestImages', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage TestImages', 'url'=>array('admin')),
);
?>

<h1>View TestImages #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'link',
		'type',
		'question_id',
		'test_id',
	),
)); ?>
