<?php

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
            'header' => 'Преподаватели',
            'value' => 'CHtml::link($data->getFullName(), Yii::app()->createUrl("teacher/view", array("id"=>$data->id)))',
            'type' => 'raw'
        ),
        array(
            'name' => 'group_id',
            'header' => 'Группы',
            'type' => 'html',
            'value' => function ($data)
            {
                $groupLinks = array();
                foreach ($data->groups1 as $group) {
                    $groupLinks[] = GxHtml::link(GxHtml::encode($group->number), array(
                        'group/view',
                        'id' => GxActiveRecord::extractPkValue($group, true)
                    ));
                }
                
                if (empty($data->groups1)) {
                    return 'N/A';
                }
                
                return implode(', ', $groupLinks);
            }
        )
    )
));
?>
