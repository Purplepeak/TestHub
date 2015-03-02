<?php 
$restoreUrl = $sitePath . '/accountInteraction/changePass' . '?' . http_build_query(array('email' => $email, 'key' => $key));
?>

<p>Здравствуйте, <?= $username ?>!</p>
<p>Вы сделали запрос для восстановление пароля на <?= $siteTitle ?>.</p>
<p>Если вы пройдете по этой ссылке, то сможете сменить пароль для вашего аккаунта и в дальнейшем использовать его: <a href="<?= $restoreUrl ?>"><?= $restoreUrl ?></a></p>
<br><br>
<p>Если вы не запрашивали смену пароля, пожалуйста, <a href="<?= $sitePath ?>/site/contact" style="color: #03110A;">дайте нам об этом знать</a></p>