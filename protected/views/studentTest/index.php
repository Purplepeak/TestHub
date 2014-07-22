<?php
/* @var $this StudentTestController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Student Tests',
);

$this->menu=array(
	array('label'=>'Create StudentTest', 'url'=>array('create')),
	array('label'=>'Manage StudentTest', 'url'=>array('admin')),
);
?>

<h1>Student Tests</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
