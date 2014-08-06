<h2>Группа <?= $model->number; ?></h2>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'number',
		'techer_id',
	),
)); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'student-grid',
	'dataProvider'=>$students->search(),
	'filter'=>$students,
	'columns'=>array(
		'name',
	),
)); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'group-grid',
    //'itemsCssClass' => 'asgasg',
    //'pagerCssClass' => 'asgasasg',
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
	        'name' => 'group_id',
	        'header'=>'Студент',
	        'type'=>'html',
	        'value'=> function($data) {
	            $namesLinks = array();
	            foreach ($data->student as $name) {
	                $namesLinks[] = GxHtml::link(GxHtml::encode($name->getFullName()), array('student/view', 'id' => GxActiveRecord::extractPkValue($name, true)));
	            }
	            
	            if(empty($data->student)) {
	                return 'N/A';
	            }
	            
	            return implode(', ', $namesLinks);
	        },
	        'type'  => 'raw',
	    ),
	    
	),
));?>
