<?php
$dataProvider = $model->searchTeacherTests();

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'teacher-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'dataProvider' => $dataProvider,
    'emptyText' => 'Тестов не найдено',
    'nullDisplay' => 'N/A',
    'summaryText' => '',
    'filter' => $model,
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'columns' => array(
        array(
            'name' => 'name',
            'header' => 'Название теста',
            'type' => 'html',
            'value' => function ($data)
            {
                return CHtml::link(CHtml::encode($data->name), Yii::app()->createUrl("test/view", array("id"=>$data->id)));
            }
        ),
        'deadline',
        array(
            'name' => 'testGroups',
            'header' => 'Группы',
            'type' => 'html',
            'value' => function ($data)
            {
                return Teacher::groupsToString($data->groups);
            }
        ),
        array(
            'class'=>'CButtonColumn',
            'deleteConfirmation'=> 'Вы уверены, что хотите удалить этот тест?',
            'headerHtmlOptions' => array(
                'style' => 'background: transparent'
            ),
            'htmlOptions' => array('style' => 'width: 70px'),
            'buttons' => array(
                'delete' => array(
                    'label' => 'Удалить',
                    'imageUrl' => Yii::app()->request->baseUrl .'/css/delete.png',
                    'options' => array('class' => 'owner-delete-button')
                ),
                'update' => array(
                    'label' => 'Изменить',
                    'imageUrl' => Yii::app()->request->baseUrl .'/css/update.png',
                    'options' => array('class' => 'owner-update-button')
                ),
                'view' => array(
                    'label' => 'Просмотр',
                    'imageUrl' => Yii::app()->request->baseUrl .'/css/view.png',
                    'options' => array('class' => 'owner-view-button')
                ),
            )
        ),
    )
));
?>