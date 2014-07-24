<?php
/* @var $this UserController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Логин';

$user = Yii::app()->urlManager->parseUrl(Yii::app()->request);

if($user == 'teacher/login') {
	$user = 'teacher';
} elseif($user == 'student/login') {
	$user = 'student';
}
?>
<div class='social-login'>
      <?php Yii::app()->soauth->renderWidget(array('action' => "{$this->index}/login", 'scenario' => 'login')); ?>
</div>
<hr>
<?php if(Yii::app()->request->getQuery('newPassword') == 1):?>
<div class="alert alert-success">
    <strong>Вам пароль был успешно изменен.</strong>
</div>
<?php endif;?>
<p>Введите свой e-mail и пароль, указанный при регистрации:</p>

<?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
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
		<?php echo CHtml::link('забыли пароль?', array('accountInteraction/passRestore'), array('class' => 'pass-restore'));?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe', array('style'=>'display:inline; font-size:11px')); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Войти'); ?>
	</div>
	
	<p>
	  <?php echo CHtml::link('Не зарегистрированы?', array($user . '/registration'), array('style'=>'font-size:16px'));?>
	</p>
<?php $this->endWidget(); ?>
</div><!-- form -->
