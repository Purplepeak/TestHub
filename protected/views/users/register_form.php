<div class="form th-register-from">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'register-form',
	'enableAjaxValidation'=>true,
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'focus'=>array($model,'email'),
));
?>
<?php if($model->scenario == 'register'):?>
	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->emailField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

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
	
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'surname'); ?>
		<?php echo $form->textField($model,'surname'); ?>
		<?php echo $form->error($model,'surname'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'gender'); ?>
		<?php echo $form->radioButtonList($model,'gender', array(Users::GENDER_MALE=>'Мужской', Users::GENDER_FEMALE=>'Женский'), array('separator'=>'  ', 'labelOptions'=>array('style'=>'display:inline; font-size:11px'))); ?>
		<?php echo $form->error($model,'gender'); ?>
	</div>
<?php endif;?>	

	<?php if($model->_type == 'teacher'):?>
	  <div class="row">
		<?php echo $form->labelEx($model,'groups'); ?>
		<p>Укажите группы в которых вы преподаете. Если групп несколько, можно указать их через запятые/пробелы или же перечислить несколько подряд идущих групп 1050-1053.</p>
		<?php echo $form->textField($model,'groups'); ?>
		<?php echo $form->error($model,'groups'); ?>
	  </div>
	<?php endif;?>
	
	<?php if($model->_type == 'student'):?>
	  <div class="row">
		<?php echo $form->labelEx($model,'group'); ?>
		<p>Укажите номер группы в которой вы обучаетесь.</p>
		<?php echo $form->textField($model,'group'); ?>
		<?php echo $form->error($model,'group'); ?>
	</div>
	<?php endif;?>	
	
	<?php if($model->_type == 'teacher'):?>
	  <div class="row">
		<?php echo $form->labelEx($model,'accessCode'); ?>
		<?php echo $form->textField($model,'accessCode'); ?>
		<?php echo $form->error($model,'accessCode'); ?>
	  </div>
    <?php endif;?>
 <?php if($model->scenario == 'register'):?>
    <br><br>
    <div class="row">
		<?php echo $form->labelEx($model,'captcha'); ?>
		<?php $this->widget('ext.srecaptcha.SReCaptchaWidget', array(
				'theme' => 'white',
				'publicKey' => Yii::app()->params['privateConfig']['recaptcha']['publicKey'],
				'lang' => 'ru'
		));?>
		<?php echo $form->error($model,'captcha'); ?>
	</div>
  <?php endif;?>  
	<div class="row buttons">
		<?php echo CHtml::submitButton('Зарегистрироваться'); ?>
	</div>

<?php $this->endWidget();?>
    
</div><!-- form -->