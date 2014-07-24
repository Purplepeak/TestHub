<?php if($model->scenario == 'oauth'):?>
    <?php if($model->_type == 'teacher'):?>
      <p>Укажите список групп, которые вы обучаете:</p>
    <?php endif;?>
    <?php if($model->_type == 'student'):?>
      <p>Укажите группу к которой вы принадлежите:</p>
    <?php endif;?>
<?php endif;?>
<?php if($model->scenario == 'register'):?>
  <div class = 'reg-header'>
    <h2>Создать новый профиль</h2>
    <div class = 'reg-sign-in'>
      Уже зарегистрированы? <?php echo CHtml::link('Войти', array("{$model->_type}/login"));?>
    </div>
  </div>
<?php endif;?>
<?php if($model->scenario == 'register'):?>
  <p>Вы можете зарегистрироваться через предложенные социальные сети, либо пройти обычную процедуру с заполнением необходимых полей.</p>
  <div class='social-registration'>
        <?php Yii::app()->soauth->renderWidget(array('action' => "{$model->_type}/login" , 'scenario' => 'registration')); ?>
  </div>
<?php endif;?>
<br>
<?php $this->renderPartial('//users/register_form', array('model'=>$model));?>

<div class="form-note">
  <p><span style="color:red">*</span> обязательные для заполнения поля</p>
</div>