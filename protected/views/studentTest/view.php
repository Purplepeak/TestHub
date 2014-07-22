<?php
/* @var $this StudentTestController */
/* @var $model StudentTest */

$this->breadcrumbs=array(
	'Student Tests'=>array('index'),
	$model->student_id,
);

$this->menu=array(
	array('label'=>'List StudentTest', 'url'=>array('index')),
	array('label'=>'Create StudentTest', 'url'=>array('create')),
	array('label'=>'Update StudentTest', 'url'=>array('update', 'id'=>$model->student_id)),
	array('label'=>'Delete StudentTest', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->student_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage StudentTest', 'url'=>array('admin')),
);
?>

<h1>View StudentTest #<?php echo $model->student_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'student_id',
		'test_id',
		'result',
	),
)); ?>
