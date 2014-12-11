<?php
/* @var $this TestController */
/* @var $model Test */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'test-form',
	'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'clientOptions'=>array(
        'validateOnSubmit'=>true,
    ),
    'focus'=>array($model,'name'),
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Название теста'); ?>
		<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Предисловие'); ?>
		<?php echo $form->textArea($model,'foreword',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'foreword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'minimum_score'); ?>
		<p>Минимальное количество баллов, необходимых для прохождения теста</p>
		<?php echo $form->textField($model,'minimum_score'); ?>
		<?php echo $form->error($model,'minimum_score'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'time_limit'); ?>
		<p>Время за которое студент должен выполнить тест</p>
		<?php echo $form->textField($model,'time_limit'); ?>
		<?php echo $form->error($model,'time_limit'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'attempts'); ?>
		<p>Число попыток сдачи теста</p>
		<?php echo $form->textField($model,'attempts'); ?>
		<?php echo $form->error($model,'attempts'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'deadline'); ?>
		<p>Крайний срок для сдачи теста</p>
		<?php echo $form->textField($model,'deadline'); ?>
		<?php echo $form->error($model,'deadline'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Создать и перейти к созданию вопросов для теста'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->