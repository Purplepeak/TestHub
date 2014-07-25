<?php
$this->pageTitle = Yii::app()->name . ' - Регистрация';
?>
<div class="confirm-note">
	<h2>Спасибо за регистраию на нашем сайте, ваш аккаунт был успешно создан!</h2>
	<p>На электронный адрес указанный вами при регистрации (<span class="bold-text"><?= $model->email?></span>) была выслана инструкция по активации аккаунта.</p>
	<p>Если вы не получили письмо, пожалуйста, проверьте папку со спамом, есть вероятность, что ваш почтовый фильтр отправил наше письмо туда.</p>
	<p>Если письма нет и там, то вы можете отправить сообщение еще раз:</p>
<div class="resend-wrapper">
<?php
echo CHtml::ajaxButton('Отправить повторно', array(
    'accountInteraction/resend',
    'id' => $model->id,
    'name' => $model->name,
    'email' => $model->email
), array(
    'update' => '.dialogWrapper',
    'beforeSend' => 'function() {
           $(".dialogWrapper").text("");
           $(".dialogWrapper").removeClass("alert alert-success");
     }',
    'complete' => 'function() {
          $(".dialogWrapper").text("Сообщение отправлено");
          $(".dialogWrapper").addClass("alert alert-success");
     }'
), array('class' => 'resend-button'));
?>
<div class="dialogWrapper"></div>
</div>
</div>