<?php
$attributes = array(
    'name',
    'surname',
    'email'
);

if($model->cropped_avatar) {
    $avatar = Yii::app()->request->baseUrl .Yii::app()->params['avatarRelativePath'].'/'.$model->id. '/190x190/crop/'. CHtml::encode($model->cropped_avatar);
} else {
    $avatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultMainAvatar'];
}

if ($model->_type == 'teacher') {
    array_push($attributes, array(
        'label' => 'Роль',
        'type' => 'raw',
        'value' => 'Преподаватель'
    ), array(
        'name' => 'groups',
        'type' => 'html',
        'value' => Teacher::groupsToString($model->groups1)
    ));
}

if ($model->_type == 'student') {
    array_push($attributes, array(
        'label' => 'Роль',
        'type' => 'raw',
        'value' => 'Студент'
    ), array(
        'name' => 'group',
        'type' => 'html',
        'value' => CHtml::link(CHtml::encode($model->student_group->number), array(
            'student/list',
            'id' => $model->student_group->id
        ))
    ));
}
?>

<div class="profile-header">
	<div class="user-avatar-wrapper">
		<img class="user-main-avatar" src="<?= $avatar ?>">
	</div>
	<div class="user-data">
    <?php
    
    $this->widget('zii.widgets.CDetailView', array(
        'data' => $model,
        'nullDisplay' => 'N/A',
        'cssFile' => Yii::app()->baseUrl . '/css/detail-view.css',
        'attributes' => $attributes
    ));
    ?>
  </div>
</div>


