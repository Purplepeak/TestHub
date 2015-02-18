<?php
$this->pageTitle=Yii::app()->name . ' - Активация аккаунта';
?>
<div class="alert alert-danger">
 Произошел сбой при подтверждении аккаунта
</div>
<p>Указан недействительный e-mail или ключ активации. <?= CHtml::link('Отправить письмо повторно?', array('accountInteraction/sendNewConfirmation'), array('class' => 'th-link'));?></p>
<p>Напоминаем, что аккаунт должен быть подтвержен вами в течении двух дней со дня регистрации, иначе он автоматически удаляется.</p>