<?php
$group = Group::model()->findByPk($model->searchGroup);

if(!empty($group)) {
    $header = "Группа {$group->number}";
} else {
    $this->redirect(array('site/index'));
}

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'teacher-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'dataProvider' => $model->search(),
    'enableSorting' => false,
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'fullname',
            'header' => $header,
            'value' => 'CHtml::link($data->getFullName(), Yii::app()->createUrl("student/view", array("id"=>$data->id)))',
            'type' => 'raw'
        )
    )
));
?>
