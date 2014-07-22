<?php
$this->pageTitle=Yii::app()->name . ' - Регистрация';
?>
<div class="confirm-note">
  <h2>Спасибо за регистраию на нашем сайте, ваш аккаунт был успешно создан!</h2>
  <p>На электронный адрес указанный вами при регистрации (<span class="bold-text"><?= $model->email?></span>) была выслана инструкция по активации аккаунта.</p>
  <p>Если вы не получили письмо, пожалуйста, проверьте папку со спамом, есть вероятность, что ваш почтовый фильтр отправил наше письмо туда.</p>
  <p>Если письма нет и там, то вы можете отправить сообщение еще раз:</p>
  <form action="<?= Yii::app()->request->hostInfo . Yii::app()->request->baseUrl?>/accountInteraction/notification/sendMessage/1">
    <button>Отправить повторно</button>
  </form>
</div>