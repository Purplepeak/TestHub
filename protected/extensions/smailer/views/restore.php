<p>Здравствуйте, <?= $name ?>!</p>
<p>Вы сделали запрос для восстановление пароля на <?= $siteTitle ?>.</p>
<p>Если вы пройдете по этой ссылке, то сможете сменить пароль для вашего аккаунта и в дальнейшем использовать его: <?= $sitePath ?>/accountInteraction/changePass?email=<?= $email ?>&key=<?= $key ?></p>
<br><br>
<p>Если вы не запрашивали смену пароля, пожалуйста, <a href="<?= $sitePath ?>/site/contact" style="color: #03110A;">дайте нам об этом знать</a></p>