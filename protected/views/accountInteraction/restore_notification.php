<?php
$this->pageTitle=Yii::app()->name . ' - Восстановление пароля';
?>
<div class="confirm-note">
  <h2>Восстановление пароля.</h2>
  <p>Инструкция по восстановлению пароля была выслана вам на адрес:</p>
  <p><span class="bold-text"><?= $model->email?></span></p>
</div>