<div class="change-avatar-header">
	<h2>Выберите новый аватар</h2>
	<p>Поддерживаемые форматы изображения: jpeg, gif, png.</p>
</div>
<div class="change-avatar-wrapper">
	<img class="js-user-main-avatar" src="">
</div>
<?php
$this->widget('SAvatarWidget', array(
    'model' => $model,
    'action' => Yii::app()->request->baseUrl . '/' . $user->type . '/changeAvatar',
    'modelAttributes' => array(
        'avatarFileAtt' => 'newAvatar',
        'avatarX' => 'avatarX',
        'avatarY' => 'avatarY',
        'avatarWidth' => 'avatarWidth',
        'avatarHeight' => 'avatarHeight'
    ),
    'previewMaxWidth' => 600,
    'previewMaxHeight' => 600,
    'maxImageWidth' => 5000,
    'maxImageHeight' => 5000,
    'minImageWidth' => 200,
    'minImageHeight' => 200,
    'maxImageRatio' => 3,
    'minImageRatio' => 0.25,
    'maxImageSize' => 1536000
));
?>