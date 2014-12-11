<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'teacher-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'dataProvider' => $model->searchTests(),
    'enableSorting' => false,
    'emptyText' => 'Тестов не найдено',
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'testName',
            'header' => 'Название теста',
            'value' => function ($data)
            {
                $testNameArray = array();
                
                foreach($data->tests1 as $test) {
                    $testNameArray[] = CHtml::link(CHtml::encode($test->name), Yii::app()->createUrl("test/view", array("id"=>$test->id)));
                    //return end($testNameArray);
                }
                
                return ($testNameArray);
            },
            'type' => 'raw'
        ),
        /*
        array(
            'name' => 'groupNumber',
            'header' => 'Группы',
            'type' => 'html',
            'value' => function ($data)
            {
                return $data->groupsToString();
            }
        )
        */
    )
));
?>
