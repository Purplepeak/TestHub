<h2 class='first-header'>Студенты</h2>

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
    'emptyText' => 'Студентов не найдено',
    'nullDisplay' => 'N/A',
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'fullname',
            'header' => $header,
            'value' => 'CHtml::link(CHtml::encode($data->getFullName()), Yii::app()->createUrl("student/view", array("id"=>$data->id)))',
            'type' => 'raw'
        )
    )
));
?>
