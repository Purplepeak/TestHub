<?php
/* @var $this AnswerOptionsController */
/* @var $model AnswerOptions */

$this->breadcrumbs=array(
	'Answer Options'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AnswerOptions', 'url'=>array('index')),
	array('label'=>'Create AnswerOptions', 'url'=>array('create')),
	array('label'=>'View AnswerOptions', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AnswerOptions', 'url'=>array('admin')),
);
?>

<h1>Update AnswerOptions <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>