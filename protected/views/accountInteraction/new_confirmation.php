<?php
$this->pageTitle=Yii::app()->name . ' - Подтверждение аккаунта';
?>
<h2>Отправка повторного письма</h2>
<p>Введите e-mail и пароль к вашему аккаунту для того, чтобы мы отправили вам инструкции по активации повторно.</p>

<?php if(Yii::app()->user->hasFlash('newConfirmError')):?>
    <div class="alert alert-danger">
        <?= Yii::app()->user->getFlash('newConfirmError') ?>
    </div>
<?php endif; ?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'new-confirm-form',
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

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Отправить'); ?>
	</div>
<?php $this->endWidget();?>
</div><!-- form -->