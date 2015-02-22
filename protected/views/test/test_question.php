<?php 
if(!empty($questionDataArray)) {
    $i = $questionNumber;
    
    foreach($questionNumberIdPair as $number=>$questionId) {
        if($i + 1 > $numberOfQuestions) {
            $nextQuestionNumber = null;
            break;
        }
    
        if(!in_array($questionNumberIdPair[$i + 1], $studentAnswersQuestionId)) {
            $nextQuestionNumber = $i + 1;
            break;
        }
        $i++;
    }
    
    if($nextQuestionNumber === null) {
        $nextQuestionNumber = 'end';
    }
    
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'answer-form',
        'action' => array('test/process', 'id'=>$testID, 'q'=>$questionNumber+1),
        'enableAjaxValidation'=>false,
        'enableClientValidation'=>false,
        'clientOptions'=>array(
            'validateOnSubmit'=>false,
        ),
        'htmlOptions' => array(
            'class'=>'answer-form next-question-number-'.$nextQuestionNumber),
    ));
    
    if($questionDataArray['type'] === 'select_many' || $questionDataArray['type'] === 'select_one') {
        $questionDataArray['answerIdTextPair'] = array_map(function ($string) { return CHtml::encode($string); }, $questionDataArray['answerIdTextPair']);
    
        $htmlOptions = array(
            'class' => 'answer-radio-button',
            'separator' => ' ',
            'template' => "{beginLabel} {input} <span class='option-number'></span> {labelTitle} {endLabel}",
            'container' => "div class='question-choices'",
            'labelOptions' => array('class'=>'question-choice')
        );
    }
    
    Yii::app()->clientScript->registerScript("ForewordRedactor",'
           var answeredQuestion = '.CJSON::encode($studentAnswersQuestionId).';
           $.each(answeredQuestion, function(key, questionID) {
               $("#question-anchor-"+questionID).css("background", "#eee");
           });
    ');
    
    switch($questionDataArray['type']) {
    	case 'select_many':
    	    $questionAnswer = $form->checkBoxList($answerModel,'selectedAnswers', $questionDataArray['answerIdTextPair'], $htmlOptions);
    	    $attrID = get_class($answerModel).'_selectedAnswers'.'_em_';
    	    break;
    	case 'select_one':
    	    $questionAnswer = $form->radioButtonList($answerModel,'answer_id', $questionDataArray['answerIdTextPair'], $htmlOptions);
    	    $attrID = get_class($answerModel).'_answer_id'.'_em_';
    	    break;
    	case 'numeric':
    	    $questionAnswer = $form->numberField($answerModel, 'answer_number', array('autocomplete' => 'off'));
    	    $attrID = get_class($answerModel).'_answer_number'.'_em_';
    	    break;
    	case 'string':
    	    $questionAnswer = $form->textField($answerModel,'answer_text', array('autocomplete' => 'off'));
    	    $attrID = get_class($answerModel).'_answer_text'.'_em_';
    	    break;
    }
    
    if($questionDataArray['type'] == 'select_many' || $questionDataArray['type'] == 'select_one') {
        $i=0;
        foreach($questionDataArray['answerIdTextPair'] as $value) {
            $i++;
            $questionAnswer = preg_replace("{(<span class='option-number'>)(<\/span>)}ui", "$1 {$i} $2", $questionAnswer, 1);
        }
    }
}

?>

<?php if(empty($questionAlert)):?>
    <div class="test-question-counter">
      <span><?= $questionNumber ?> из <?= $numberOfQuestions ?></span>
    </div>
    <div class='question-number'>
      Вопрос №<?= $questionNumber ?>
    </div>
    <div class='question-text'>
      <?= $questionDataArray['title'] ?>
      <div class="err"></div>
    </div>
    <div class='question-answer'>
      <?= $questionAnswer ?>
      <div class="errorMessage" id="<?= $attrID ?>" style="display:none"></div>
    </div>
    <?= $form->hiddenField($answerModel, 'question_id', array('value' => $questionDataArray['id'])); ?>
    <?= $form->hiddenField($answerModel, 'questionNumber', array('value' => $questionNumber)); ?>
    <?= $form->hiddenField($answerModel, 'scenario', array('value' => $questionDataArray['type'])); ?>
    <?= CHtml::hiddenField('nextQuestionNumber', $nextQuestionNumber); ?>
    <?= CHtml::hiddenField('testId', $testID); ?>
    <?= CHtml::hiddenField('testTimeLimit', $testTimeLimit); ?>
    <?= CHtml::hiddenField('testStartTime', $testStartTime); ?>
    <div class="answer-buttons-container">
      <?php echo CHtml::ajaxSubmitButton('Ответить', array('test/process', 'id'=>$testID, 'q'=>$nextQuestionNumber), array(
              'type' => 'POST',
              'complete'=>'function(e) {
                  var questionNumber = $("#StudentAnswer_questionNumber").val();
                  var nextQuestionNumber = $("#nextQuestionNumber").val();
                  var url = "'.Yii::app()->controller->createUrl("test/process",array('id'=>$testID)).'"+"/q/"+nextQuestionNumber;
                  var response = JSON.parse(e.responseText);
                  if(response.hasOwnProperty("redirect")) {
                      window.location.replace("'.Yii::app()->request->hostInfo.'" + response.redirect);
                  }
        
                  if(!response.hasOwnProperty("validateStatus")){
                      $.each(response, function(key, val) {
                          //$(".err").text(val);
                          $("#answer-form #"+key+"_em_").text(val);
                          $("#answer-form #"+key+"_em_").css("display", "block");
                      });
                  } else {
                      $(".question-anchor-"+questionNumber).css("background", "#eee");
                      swapQuestion(url);
                      history.pushState(null, null, url);
                  }
              }',
        'error' => 'function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            console.log(err.Message);
        }',
          ),
          array(
              'type' => 'submit',
              'class' => 'submit-answer-button'
      ));?>
    </div>
    
<?php $this->endWidget();?>
<?php endif;?>

<?php if(!empty($questionAlert)):?>
<div class="question-alert">
  <p><?= $questionAlert ?></p>
  <?php echo CHtml::beginForm(Yii::app()->controller->createUrl('test/result', array('id'=>$testID)));?>
  <?php echo CHtml::hiddenField('endTest', true) ?>
  <?php echo CHtml::submitButton('Завершить', array('class'=>'end-test-button')) ?>
  <?php echo CHtml::endForm(); ?>
</div>
<?php endif;?>