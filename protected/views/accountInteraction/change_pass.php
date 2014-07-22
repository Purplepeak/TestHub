<div class="pass-restore-div">
  <h2>Смена пароля</h2>
  <p>Пожалуйста, введите новый пароль.</p>
  <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'change-pass-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'passwordText'); ?>
		<?php echo $form->passwordField($model,'passwordText'); ?>
		<?php echo $form->error($model,'passwordText'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'password2'); ?>
		<?php echo $form->passwordField($model,'password2'); ?>
		<?php echo $form->error($model,'password2'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Изменить'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
</div>