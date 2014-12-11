<?php 
$rowId = "question-" . $key;

$answerOptionNumber = 0;
$csrfTokenName = Yii::app()->request->csrfTokenName;
$csrfToken = Yii::app()->request->csrfToken;
$scenario = $model->scenario;
/*
if($model->type === 'select_one' || $model->type === 'select_many') {
    $a = array();
    foreach($model->answerOptions as $option) {
        $a[$option->option_number] = $option->option_text;
    }
    
    $model->answerOptionsArray = $a;
}
*/
$this->widget('ImperaviRedactorWidget', array(
    'selector' => '.question-text-' . $key,

    'options' => array(
        'lang' => 'ru',
        'toolbarFixed' => false,
        'imageUpload' => Yii::app()->createUrl('questionImages/tmpUpload'),
        'imageUploadParam' => 'QuestionImage[imageFile]',
        'imageUploadErrorCallback' => 'js:function(json){ alert(json.message); }',
        'uploadImageFields' => array(
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
        ),
        'uploadFileFields' => array(
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
        )
    )
));

//var_dump($model);
//echo $form->errorSummary($model);
//echo $form->hiddenField($model, "[$key]id");

?>
<div class='row-fluid' id="<?php echo $rowId ?>">
    <?php echo $form->hiddenField($model, "[$key]id");?>
    <?php echo $form->updateTypeField($model, $key, "updateType", array('key' => $key));?>
    <div class="question-header">
      <div class="delete-question-button">
        <?php echo $form->deleteFormButton($rowId, $key);?>
      </div>
      <div class="question-counter" id="question-key-<?= $key ?>">
        Вопрос №<?= $questionNumber ?>
      </div>
    </div>
    <div class="row">
		<?php echo $form->labelEx($model,"title"); ?>
		<?php echo $form->textArea($model,"[$key]title",array('rows'=>6, 'cols'=>50, 'class' => 'questionField question-text-'.$key)); ?>
		<?php echo $form->error($model,"[$key]title", array(), false, false); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,"difficulty"); ?>
		<?php echo $form->textField($model,"[$key]difficulty", array('class' => 'questionField')); ?>
		<?php echo $form->error($model,"[$key]difficulty", array(), false, false); ?>
	</div>
	
	<?php if($scenario === 'select'):?>
    <div class="js-options-<?= $key ?>">
    <?php echo $form->labelEx($model,"answerOptionsArray"); ?>
    <?php foreach($model->optionsNumber as $i): ?>
        <?php $answerOptionNumber++;?>
	    <div class="row answer-option-<?= $i ?>">
	    <div class="answer-option-number-<?= $i ?>"><?= $answerOptionNumber ?>)</div>
	    <?php echo $form->textArea($model, "[$key]answerOptionsArray[{$i}]", array('rows'=>2, 'cols'=>30,'class' => 'questionField')); ?>
	    <?php echo CHtml::button('', array('class' => 'deleteAnswerOption', 'onclick' => "deleteOption(this)"));?>
	    <?php echo $form->error($model, "[$key]answerOptionsArray[{$i}]", array('class' => 'errorMessage answerOptionError'), false, false); ?>
	    </div>
	<?php endforeach;?>
	</div>
	<?php echo CHtml::button('Добавить вариант ответа', array('class' => 'addAnswerOption', 'data-add' => "js-options-{$key}", 'onclick' => "addOption(this)"));?>
	
    <div class="row">
	    <?php echo $form->labelEx($model,"correctAnswers"); ?>
	    <em>Укажите номера правильных ответов через зяпятую, если их несколько</em>
	    <?php echo $form->textField($model, "[$key]correctAnswers", array('class' => 'questionField')); ?>
	    <?php echo $form->error($model,"[$key]correctAnswers", array(), false, false); ?>
    </div>
    <?php endif;?>

    <?php if($scenario === 'string'):?>
    <div class="row">
	    <?php echo $form->labelEx($model,"answer_text"); ?>
	    <em>Правильный ответ в виде строки</em>
	    <?php echo $form->textField($model,"[$key]answer_text",array('size'=>50,'maxlength'=>50, 'class' => 'questionField')); ?>
	    <?php echo $form->error($model,"[$key]answer_text", array(), false, false); ?>
    </div>
    <?php endif;?>

    <?php if($scenario === 'numeric'):?>
    <div class="row">
	    <?php echo $form->labelEx($model,"answer_number"); ?>
	    <em>Правильный ответ в виде числа</em>
	    <?php echo $form->textField($model,"[$key]answer_number",array('size'=>9,'maxlength'=>9, 'class' => 'questionField')); ?>
	    <?php echo $form->error($model,"[$key]answer_number", array(), false, false); ?>
    </div>

    <div class="row">
	    <?php echo $form->labelEx($model,"Погрешность в процентах"); ?>
	    <em>Если необходимо, укажите погрешность ответа в процентах</em>
	    <?php echo $form->textField($model,"[$key]precision_percent", array('class' => 'questionField')); ?>
	    <?php echo $form->error($model,"[$key]precision_percent", array(), false, false); ?>
    </div>
    <?php endif;?>
    
    <?php echo $form->hiddenField($model,"[$key]modelScenario", array('value' => $scenario, 'class' => 'js-question-type')); ?>
</div>