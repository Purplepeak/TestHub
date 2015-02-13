<h2 class='first-header'>Преподаватели</h2>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'teacher-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'dataProvider' => $model->search(),
    'enableSorting' => false,
    'emptyText' => 'Преподавателей не найдено',
    'nullDisplay' => 'N/A',
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'fullname',
            'header' => 'Преподаватели',
            'value' => 'CHtml::link(CHtml::encode($data->getFullName()), Yii::app()->createUrl("teacher/view", array("id"=>$data->id)))',
            'type' => 'raw'
        ),
        array(
            'name' => 'groupNumber',
            'header' => 'Группы',
            'type' => 'html',
            'value' => function ($data)
            {
                return Teacher::groupsToString($data->groups1);
            }
        )
    )
));
?>
