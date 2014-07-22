<?php
/* @var $this StudentTestController */
/* @var $model StudentTest */

$this->breadcrumbs=array(
	'Student Tests'=>array('index'),
	$model->student_id=>array('view','id'=>$model->student_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List StudentTest', 'url'=>array('index')),
	array('label'=>'Create StudentTest', 'url'=>array('create')),
	array('label'=>'View StudentTest', 'url'=>array('view', 'id'=>$model->student_id)),
	array('label'=>'Manage StudentTest', 'url'=>array('admin')),
);
?>

<h1>Update StudentTest <?php echo $model->student_id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>