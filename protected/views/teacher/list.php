<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'teacher-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'dataProvider' => $model->search(),
    'enableSorting' => false,
    'emptyText' => 'Преподавателей не найдено',
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'fullname',
            'header' => 'Преподаватели',
            'value' => 'CHtml::link($data->getFullName(), Yii::app()->createUrl("teacher/view", array("id"=>$data->id)))',
            'type' => 'raw'
        ),
        array(
            'name' => 'groupNumber',
            'header' => 'Группы',
            'type' => 'html',
            'value' => function ($data)
            {
                return $data->groupsToString();
            }
        )
    )
));
?>
