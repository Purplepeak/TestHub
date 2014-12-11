<?php
/* @var $this AnswerOptionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Answer Options',
);

$this->menu=array(
	array('label'=>'Create AnswerOptions', 'url'=>array('create')),
	array('label'=>'Manage AnswerOptions', 'url'=>array('admin')),
);
?>

<h1>Answer Options</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
