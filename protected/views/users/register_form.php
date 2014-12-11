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
<div class="register-fields-wrapper">
<?php if($model->scenario == 'register'):?>
	<div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'email'); ?>
	    </div>
		<?php echo $form->emailField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'passwordText'); ?>
	    </div>
		<?php echo $form->passwordField($model,'passwordText'); ?>
		<?php echo $form->error($model,'passwordText'); ?>
	</div>
	
	<div class="row">
	    <div class="field-label">
	         <?php echo $form->labelEx($model,'password2'); ?>
	    </div>
		<?php echo $form->passwordField($model,'password2'); ?>
		<?php echo $form->error($model,'password2'); ?>
	</div>
	
	<div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'name'); ?>
	    </div>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>
	
	<div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'surname'); ?>
	    </div>
		<?php echo $form->textField($model,'surname'); ?>
		<?php echo $form->error($model,'surname'); ?>
	</div>
	
	<div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'gender'); ?>
	    </div>
		<?php echo $form->radioButtonList($model,'gender', array(Users::GENDER_MALE=>'Мужской', Users::GENDER_FEMALE=>'Женский'), array('separator'=>'  ', 'labelOptions'=>array('style'=>'display:inline; font-size:11px'))); ?>
		<?php echo $form->error($model,'gender'); ?>
	</div>
<?php endif;?>	

	<?php if($model->_type == 'teacher'):?>
	  <div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'groups'); ?>
	    </div>
		<?php echo $form->textField($model,'groups'); ?>
		<em>Группы в которых вы преподаете. Если групп несколько, можно указать их через запятые/пробелы или же перечислить несколько подряд идущих групп 1050-1053.</em>
		<?php echo $form->error($model,'groups'); ?>
	  </div>
	<?php endif;?>
	
	<?php if($model->_type == 'student'):?>
	  <div class="row">
	    <div class="field-label">
	         <?php echo $form->labelEx($model,'group'); ?>
	    </div>
		<?php echo $form->textField($model,'group'); ?>
		<em>Укажите номер группы в которой вы обучаетесь.</em>
		<?php echo $form->error($model,'group'); ?>
	</div>
	<?php endif;?>	
	
	<?php if($model->_type == 'teacher'):?>
	  <div class="row">
	    <div class="field-label">
	        <?php echo $form->labelEx($model,'accessCode'); ?>
	    </div>
		<?php echo $form->textField($model,'accessCode'); ?>
		<?php echo $form->error($model,'accessCode'); ?>
	  </div>
    <?php endif;?>
 <?php if($model->scenario == 'register'):?>
    <div class="row captcha-row">
        <div class="field-label">
             <?php echo $form->labelEx($model,'captcha'); ?>
	    </div>
		<?php $this->widget('ext.srecaptcha.SReCaptchaWidget', array(
				'theme' => 'white',
				'publicKey' => Yii::app()->params['privateConfig']['recaptcha']['publicKey'],
				'lang' => 'ru'
		));?>
		<?php echo $form->error($model,'captcha'); ?>
	</div>
  <?php endif;?> 
</div> 
	<div class="row buttons register-submit-wrapper">
		<?php echo CHtml::submitButton('Зарегистрироваться', array('class' => 'register-submit-button th-submit-button')); ?>
	</div>

<?php $this->endWidget();?>
    
</div><!-- form -->