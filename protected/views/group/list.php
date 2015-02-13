<?php
/* @var $this GroupController */
/* @var $model Group */
?>

<h2 class='first-header'>Группы</h2>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'group-grid',
    'cssFile' => Yii::app()->baseUrl . '/css/grid-view.css',
	'dataProvider'=>$model->search(),
    'enableSorting' => false,
    'emptyText' => 'Групп не найдено',
    'summaryText' => '',
	'filter'=>$model,
    'pager' => array(
        'class' => 'CSLinkPager',
    ),
	'columns'=>array(
	    array(
	        'name'=>'number',
		    'value'=>'CHtml::link(CHtml::encode($data->number), Yii::app()->createUrl("student/list", array("id"=>$data->id)))',
	        'type'  => 'raw',
	    ),
	    array(
	        'name' => 'fullname',
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
	    array(
	        'name'=>'numberOfStudents',
	        'header' => 'Cтудентов в группе',
	        'value'=>function($data) {
                return count($data->student);
            },
	        'type'  => 'raw',
	        'filter' => false,
	    ),
	    
	),
));?>
