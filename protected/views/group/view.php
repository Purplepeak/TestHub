<?php
$config = array(
    'pagination' => array(
        'pageSize' => 20
    ),
);
$dataProvider = new CArrayDataProvider($rawData = $model->student, $config);

$this->widget('zii.widgets.grid.CGridView', array(
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'pager' => array(
        'class' => 'CSLinkPager'
    ),
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
    'summaryText' => '',
    'dataProvider' => $dataProvider,
    'columns' => array(
        array(
            'name' => 'group_id',
            'header' => "Группа {$model->number}",
            'type' => 'html',
            'value' => function ($data)
            {
                return CHtml::link($data->getFullName(), array(
                    'student/view',
                    'id' => $data->id
                ));
            },
            'type' => 'raw'
        )
    )
));
?>	        
