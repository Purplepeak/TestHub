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
    'previewMaxWidth' => 600,
    'previewMaxHeight' => 600,
    'maxImageWidth' => 5000,
    'maxImageHeight' => 5000,
    'minImageWidth' => 190,
    'minImageHeight' => 190,
    'maxImageRatio' => 3,
    'minImageRatio' => 0.25,
    'maxImageSize' => $model->avatarMaxSize*1024*1024
));var_dump(Yii::app()->request->cookies);
?>