<?php
/* @var $this TestImagesController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Test Images',
);

$this->menu=array(
	array('label'=>'Create TestImages', 'url'=>array('create')),
	array('label'=>'Manage TestImages', 'url'=>array('admin')),
);
?>

<h1>Test Images</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
