<?php 
$activateUrl = $sitePath . '/accountInteraction/confirm' . '?' . http_build_query(array('email' => $email, 'key' => $key));
?>

<p>Добро пожаловать на <?= $siteTitle ?>, <?= $username ?>! Вы почти завершили процесс регистрации, осталось только подтвердить аккаунт.</p>
<p>Для активации вашего аккаунта пройдите по ссылке: <a href="<?= $activateUrl ?>"></a><?= $activateUrl ?></p>
<br>
<p>Если вы не регистрировали аккаунт на нашем сайте, пожалуйста, <a href="<?= $sitePath ?>/site/contact" style="color: #03110A;">дайте нам об этом знать</a></p>