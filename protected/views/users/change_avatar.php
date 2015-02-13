<div class="change-avatar-header">
    <h2 class='first-header'>Сменить аватар</h2>
	<p>Поддерживаемые форматы изображения: jpeg, gif, png.</p>
</div>
<div class="change-avatar-wrapper">
	<img class="js-user-main-avatar" src="">
</div>
<?php
$this->widget('SAvatarWidget', array(
    'model' => $model,
    'action' => Yii::app()->controller->createUrl($user->type . '/changeAvatar'),
    'modelAttributes' => array(
        'avatarFileAtt' => 'newAvatar',
        'avatarX' => 'avatarX',
        'avatarY' => 'avatarY',
        'avatarWidth' => 'avatarWidth',
        'avatarHeight' => 'avatarHeight'
    ),
    'previewMaxWidth' => 1100,
    'previewMaxHeight' => 1100,
    'maxImageWidth' => 5000,
    'maxImageHeight' => 5000,
    'minImageWidth' => 190,
    'minImageHeight' => 190,
    'maxImageRatio' => 3,
    'minImageRatio' => 0.25,
    'maxImageSize' => 2048000
));
?>