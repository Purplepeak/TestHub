<?php
/* @var $this SocialAccountsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Social Accounts',
);

$this->menu=array(
	array('label'=>'Create SocialAccounts', 'url'=>array('create')),
	array('label'=>'Manage SocialAccounts', 'url'=>array('admin')),
);
?>

<h1>Social Accounts</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
