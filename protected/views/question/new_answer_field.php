<div class="row answer-option-<?= $i ?>">
  <div class="answer-option-number-<?= $i ?>"><?= $number ?>)</div>
  <?php echo CHtml::activeTextArea($model, "[$key]answerOptionsArray[{$i}]", array('rows'=>2, 'cols'=>30, 'class' => 'questionField')); ?>
  <?php echo CHtml::button('', array('class' => 'deleteAnswerOption', 'onclick' => "deleteOption(this)"));?>
  <?php echo CHtml::error($model,"[$key]answerOptionsArray[{$i}]"); ?>
</div>