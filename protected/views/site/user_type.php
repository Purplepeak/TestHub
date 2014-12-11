<div class="ask-guest-form">
  <p>Вы не можете войти сразу поскольку еще не зарегистрировались на нашем сайте. Пожалуйста, укажите к какой категории пользователей вы будете принадлежать: </p>
  <?php echo CHtml::beginForm('userType', 'post', array('enctype' => 'multipart/form-data', 'class' => 'avatar-uploader', 'id' => 'avatar-uploader')); ?>
  <div class="s-upload-button">
    <?php echo CHtml::radioButtonList('userType', 'student', array('student' => 'Студент', 'teacher' => 'Преподаватель')); ?>
  
  <div class="ask-form-send">
		<?php echo CHtml::submitButton('Продолжить'); ?>
  </div>
  
  <?php echo CHtml::endForm(); ?>
</div>