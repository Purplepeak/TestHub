<?php
/* @var $this AnswerOptionsController */
/* @var $model AnswerOptions */

$this->breadcrumbs=array(
	'Answer Options'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AnswerOptions', 'url'=>array('index')),
	array('label'=>'Manage AnswerOptions', 'url'=>array('admin')),
);
?>

<h1>Create AnswerOptions</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>