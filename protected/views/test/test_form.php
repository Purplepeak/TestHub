<?php
    $form = $this->beginWidget('DynamicTabularForm', array(
        'defaultRowView'=>'question_form',
        'id'=>'test-form',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>false,
        ),
    ));
?>

<div class="form th-test-from">
    <div class="test-fields">
    <?php echo $form->errorSummary($test); ?>
    <div class="row">
		<?php echo $form->labelEx($test,'name'); ?>
		<?php echo $form->textField($test,'name',array('size'=>45,'maxlength'=>255, 'class' => 'test-name-field')); ?>
		<?php echo $form->error($test,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($test,'foreword'); ?>
		<?php echo $form->textArea($test,'foreword',array('rows'=>10, 'cols'=>70, 'class'=>'foreword-redactor')); ?>
		<?php echo $form->error($test,'foreword'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($test,'minimum_score'); ?>
		<em>Минимальное количество баллов, необходимых для прохождения теста</em>
		<?php echo $form->textField($test,'minimum_score'); ?>
		<?php echo $form->error($test,'minimum_score'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($test,'time_limit'); ?>
		<em>Время в минутах за которое студент должен выполнить тест</em>
		<?php echo $form->textField($test,'time_limit'); ?>
		<?php echo $form->error($test,'time_limit'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($test,'attempts'); ?>
		<em>Число попыток сдачи теста</em>
		<?php echo $form->textField($test,'attempts'); ?>
		<?php echo $form->error($test,'attempts'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($test,'deadline'); ?>
		<em>Крайний срок для сдачи теста относительно вашего часового пояса. Формат: гггг-мм-дд чч:мм.</em>
		<?php 
		    $dateTimeHtmlOptions = array();
		
		    if(!$test->deadline) {
                $dateTimeHtmlOptions = array('value'=>'гггг-мм-дд чч:мм');
            }
		    
		    echo $form->textField($test,'deadline', $dateTimeHtmlOptions); 
		?>
		<?php echo $form->error($test,'deadline'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($test,'testGroups'); ?>
		<em>Укажите группы, для которых предназначен тест. Если групп несколько, можно указать их через запятые/пробелы или же перечислить несколько подряд идущих групп 1050-1053.</em>
		<?php echo $form->textField($test,'testGroups'); ?>
		<?php echo $form->error($test,'testGroups'); ?>
	</div>
	
	</div>
<?php
    echo $form->myRow($questions);
?>

<div class="test-create-wrapper">
<?php echo CHtml::submitButton($pageLabel, array('class' => 'create-test-button th-submit-button')); ?>
<div class="qeustions-empty-error"></div> 
</div>
<?php $this->endWidget(); ?>

</div>