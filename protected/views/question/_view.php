<?php
/* @var $this QuestionController */
/* @var $data Question */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::encode($data->title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('type')); ?>:</b>
	<?php echo CHtml::encode($data->type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('difficulty')); ?>:</b>
	<?php echo CHtml::encode($data->difficulty); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('answer_id')); ?>:</b>
	<?php echo CHtml::encode($data->answer_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('answer_text')); ?>:</b>
	<?php echo CHtml::encode($data->answer_text); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('answer_number')); ?>:</b>
	<?php echo CHtml::encode($data->answer_number); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('precision_percent')); ?>:</b>
	<?php echo CHtml::encode($data->precision_percent); ?>
	<br />

	*/ ?>

</div>