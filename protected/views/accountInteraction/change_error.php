<?php
$this->pageTitle=Yii::app()->name . ' - Восстановление пароля';
?>
<div class="confirm-note">
  <h2>Восстановление пароля</h2>
  <p>Недействительный e-mail или ключ, попробуйте пройти процедуру восстановления <?php echo CHtml::link('заново', array('accountInteraction/passRestore'));?>.</p>
</div>