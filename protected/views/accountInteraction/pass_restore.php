<div class="pass-restore-div">
  <h2>Забыли пароль?</h2>
  <p>Пожалуйста, укажите e-mail, использованный при регистрации. После нажатия кнопки "Отправить", на этот адрес будет выслано письмо с инструкцией по восстановлению пароля.</p>
  <div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'account-interaction-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->emailField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Отправить'); ?>
	</div>
<?php $this->endWidget(); ?>
</div>
</div>