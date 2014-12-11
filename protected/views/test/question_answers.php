<?php if($scenario === 'select_one' || $scenario === 'select_many'):?>
<div class="span3">
	<?php echo $form->labelEx($model,"[$key]answer_id"); ?>
	<?php echo $form->textField($model,"[$key]answer_id"); ?>
	<?php echo $form->error($model,"[$key]answer_id"); ?>
</div>
<?php endif;?>

<?php if($scenario === 'string'):?>
<div class="span3">
	<?php echo $form->labelEx($model,"[$key]answer_text"); ?>
	<?php echo $form->textField($model,"[$key]answer_text",array('size'=>50,'maxlength'=>50)); ?>
	<?php echo $form->error($model,"[$key]answer_text"); ?>
</div>
<?php endif;?>

<?php if($scenario === 'numeric'):?>
<div class="span3">
	<?php echo $form->labelEx($model,"[$key]answer_number"); ?>
	<?php echo $form->textField($model,"[$key]answer_number",array('size'=>9,'maxlength'=>9)); ?>
	<?php echo $form->error($model,"[$key]answer_number"); ?>
</div>


<div class="span3">
	<?php echo $form->labelEx($model,"[$key]precision_percent"); ?>
	<?php echo $form->textField($model,"[$key]precision_percent"); ?>
	<?php echo $form->error($model,"[$key]precision_percent"); ?>
</div>
<?php endif;?>