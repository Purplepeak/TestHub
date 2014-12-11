<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'teacher-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'dataProvider' => $model->searchMyTests(),
    'emptyText' => 'Тестов не найдено',
    'nullDisplay' => 'N/A',
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'testName',
            'header' => 'Название теста',
            'type' => 'html',
            'value' => function($data)
            {
                return CHtml::link(CHtml::encode($data->test->name), Yii::app()->createUrl("test/view", array('id' => $data->test->id)));
            }
        ),
        array(
            'name' => 'deadline',
            'type' => 'html',
            'value' => 'Yii::app()->params["dataHandler"]->handleDataTimezone($data->deadline . "[Y-m-d H:i]")'
        ),
        'attempts',
        array(
            'name' => 'testTimeLimit',
            'header' => 'Время на прохождение теста (мин)',
            'type' => 'html',
            'value' => '$data->test->time_limit'
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{startTest}',
            'headerHtmlOptions' => array(
                'style' => 'background: transparent'
            ),
            'buttons' => array(
                'startTest' => array(
                    'label' => 'Начать тест',
                    'imageUrl' => Yii::app()->request->baseUrl . '/css/start-test.png',
                    'options' => array(
                        'class' => 'start-test-button'
                    ),
                    'click' => 'function(){
                        if(confirm("Тест неоходимо выполнить в течении отведенного времени. Вы уверены, что хотите продолжить?")) {
                            return true;
                        } else {
                            return false;
                        }
                    }',
                    'url' => "Yii::app()->createUrl('student/startTest')"
                )
            )
        )
    )
));
?>