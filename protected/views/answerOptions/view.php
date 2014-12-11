<?php
/* @var $this AnswerOptionsController */
/* @var $model AnswerOptions */

$this->breadcrumbs=array(
	'Answer Options'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List AnswerOptions', 'url'=>array('index')),
	array('label'=>'Create AnswerOptions', 'url'=>array('create')),
	array('label'=>'Update AnswerOptions', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete AnswerOptions', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage AnswerOptions', 'url'=>array('admin')),
);
?>

<h1>View AnswerOptions #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'question_id',
		'answer',
	),
)); ?>
