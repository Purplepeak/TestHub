<?php
/* @var $this GroupController */
/* @var $model Group */
?>

<h2>Список групп</h2>
<hr>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'group-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
	'dataProvider'=>$model->search(),
    'enableSorting' => false,
    'summaryText' => '',
	'filter'=>$model,
    'pager' => array(
        'class' => 'CSLinkPager',
    ),
	'columns'=>array(
	    array(
	    'name'=>'number',
		'value'=>'CHtml::link($data->number, Yii::app()->createUrl("group/view", array("id"=>$data->id)))',
	    'type'  => 'raw',
	        ),
	    array(
	        'name' => 'teacher_id',
	        'header'=>'Преподаватель',
	        'type'=>'html',
	        'value'=> function($data) {
	            $namesLinks = array();
	            foreach ($data->teacher as $name) {
	                $namesLinks[] = GxHtml::link(GxHtml::encode($name->getFullName()), array('teacher/view', 'id' => GxActiveRecord::extractPkValue($name, true)));
	            }
	            
	            if(empty($data->teacher)) {
	                return 'N/A';
	            }
	            
	            return implode(', ', $namesLinks);
	        },
	        'type'  => 'raw',
	    ),
	    
	),
));?>
