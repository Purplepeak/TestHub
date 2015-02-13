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
      <?php Yii::app()->soauth->renderWidget(array('action' => "site/login", 'scenario' => 'login')); ?>
</div>
<hr>
<?php if(Yii::app()->request->getQuery('newPassword') == 1):?>
<div class="alert alert-success">
    <strong>Ваш пароль был успешно изменен.</strong>
</div>
<?php endif;?>
<p>Введите свой Email и пароль, указанный при регистрации:</p>

<?php if(Yii::app()->user->hasFlash('confirmError')):?>
    <div class="alert alert-danger">
        <?= Yii::app()->user->getFlash('confirmError') . ' Если вы не получили письма, попробуйте отправить его ' .CHtml::link('еще раз', array('accountInteraction/sendNewConfirmation')) . '.' ?>
    </div>
<?php endif; ?>

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
		<?php echo CHtml::link('Забыли пароль?', array('accountInteraction/passRestore'), array('class' => 'pass-restore'));?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe', array('style'=>'display:inline; font-size:11px')); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Войти', array('class' => 'login-button')); ?>
	</div>
	
	<div class="dropdown">
      <a class="sign-in-question dropdown-toggle" href="#" data-toggle="dropdown">Не зарегистрированы?</a>
      <ul class="dropdown-menu">
        <li><a href="<?= Yii::app()->controller->createUrl('student/registration') ?>">Студент</a></li>
        <li><a href="<?= Yii::app()->controller->createUrl('teacher/registration') ?>">Преподаватель</a></li>
      </ul>
    </div>
<?php $this->endWidget();?>
</div><!-- form -->
