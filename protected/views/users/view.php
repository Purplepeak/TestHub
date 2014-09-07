<?php
$attributes = array(
    'name',
    'surname',
    'email'
);

if(!isset($model->avatar)) {
    $avatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultAvatar'];
} else {
    $avatar = $model->avatar;
}

$modelAttributes = array();

if ($model->_type == 'teacher') {
    array_push($attributes, array(
        'label' => 'Роль',
        'type' => 'raw',
        'value' => 'Преподаватель'
    ), array(
        'name' => 'groups',
        'type' => 'html',
        'value' => $model->groupsToString()
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
        'value' => CHtml::link($model->student_group->number, array(
            'student/list',
            'id' => $model->student_group->id
        ))
    ));
}
?>

<div class="profile-header">
	<div class="user-avatar-wrapper">
		<img src="<?= $avatar?>">
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


