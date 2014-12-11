<?php
/* @var $this QuestionController */
/* @var $model Question */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'question-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textArea($model,'title',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'difficulty'); ?>
		<?php echo $form->textField($model,'difficulty'); ?>
		<?php echo $form->error($model,'difficulty'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'answer_id'); ?>
		<?php echo $form->textField($model,'answer_id'); ?>
		<?php echo $form->error($model,'answer_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'answer_text'); ?>
		<?php echo $form->textField($model,'answer_text',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'answer_text'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'answer_number'); ?>
		<?php echo $form->textField($model,'answer_number',array('size'=>9,'maxlength'=>9)); ?>
		<?php echo $form->error($model,'answer_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'precision_percent'); ?>
		<?php echo $form->textField($model,'precision_percent'); ?>
		<?php echo $form->error($model,'precision_percent'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->