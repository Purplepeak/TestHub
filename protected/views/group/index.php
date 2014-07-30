<?php
/* @var $this GroupController */
/* @var $dataProvider CActiveDataProvider */


$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'teacher-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		'number',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); 

var_dump($dataProvider->teacher);
?>