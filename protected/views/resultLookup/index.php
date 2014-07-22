<?php
/* @var $this ResultLookupController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Result Lookups',
);

$this->menu=array(
	array('label'=>'Create ResultLookup', 'url'=>array('create')),
	array('label'=>'Manage ResultLookup', 'url'=>array('admin')),
);
?>

<h1>Result Lookups</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
