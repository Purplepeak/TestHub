<?php
$testAction = "test/view";

if($model->testStatus === 'passed' || $model->testStatus === 'failed') {
    $testAction = "test/result";
}

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'student-tests-grid',
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
            'value' => function($data) use ($testAction)
            {
                return CHtml::link(CHtml::encode($data->test->name), Yii::app()->createUrl($testAction, array('id' => $data->test->id)));
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
                    'label' => 'Страница теста',
                    'imageUrl' => Yii::app()->request->baseUrl . '/css/start-test.png',
                    'options' => array(
                        'class' => 'start-test-icon'
                    ),
                    /*
                    'click' => 'function(){
                        if(confirm("Тест неоходимо выполнить в течении отведенного времени. Вы уверены, что хотите продолжить?")) {
                            return true;
                        } else {
                            return false;
                        }
                    }',
                    */
                    'url' => "Yii::app()->createUrl('test/view', array('id' => \$data->test->id))"
                )
            )
        )
    )
));
?>