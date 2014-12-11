<?php
/* @var $this ServTransactController */
/* @var $models ServTransact */
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
    //'focus'=>array($model,'name'),
)); ?>

	<?php echo $form->errorSummary($models); ?>

	<?php foreach($models as $key=>$model):?>
	<p>ВОПРОС <?= $key+1 ?></p>
	<div class="row">
		<?php echo $form->labelEx($model,"[$key]title"); ?>
		<?php echo $form->textArea($model,"[$key]title",array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,"[$key]title"); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"[$key]type"); ?>
		<?php echo $form->textField($model,"[$key]type",array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,"[$key]type"); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"[$key]difficulty"); ?>
		<?php echo $form->textField($model,"[$key]difficulty"); ?>
		<?php echo $form->error($model,"[$key]difficulty"); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"[$key]answer_id"); ?>
		<?php echo $form->textField($model,"[$key]answer_id"); ?>
		<?php echo $form->error($model,"[$key]answer_id"); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"[$key]answer_text"); ?>
		<?php echo $form->textField($model,"[$key]answer_text",array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,"[$key]answer_text"); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"[$key]answer_number"); ?>
		<?php echo $form->textField($model,"[$key]answer_number",array('size'=>9,'maxlength'=>9)); ?>
		<?php echo $form->error($model,"[$key]answer_number"); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"[$key]precision_percent"); ?>
		<?php echo $form->textField($model,"[$key]precision_percent"); ?>
		<?php echo $form->error($model,"[$key]precision_percent"); ?>
	</div>
	<?php endforeach; ?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->