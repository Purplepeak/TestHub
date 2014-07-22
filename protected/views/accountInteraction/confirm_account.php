<?php
/* @var $this ConfirmAccountController */
/* @var $model ConfirmForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Активация аккаунта';
?>
<p>Пожалуйста введите ключ активации отправленный вам на Email при регистрации:</p>

<?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'confirm-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
    'focus'=>array($model,'email'),
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->emailField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'key'); ?>
		<?php echo $form->textField($model,'key'); ?>
		<?php echo $form->error($model,'key'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'captcha'); ?>
		<?php $this->widget('ext.srecaptcha.SReCaptchaWidget', array(
				'theme' => 'white',
				'publicKey' => Yii::app()->params['socialKeys']['recaptcha']['publicKey'],
				'lang' => 'ru'
		));?>
		<?php echo $form->error($model,'captcha'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Активировать'); ?>
	</div>
	
<?php $this->endWidget(); ?>
</div><!-- form -->
