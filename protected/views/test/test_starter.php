<?php
$testName = $test->name;
if(mb_strlen($testName) > 50) {
    $testName = trim(mb_substr($testName, 0, 50)) . '…';
}
?>
<div>
  <h3 class="first-header test-process-header"><?= $testName ?></h3>
  <div class="test-countdown-clock"></div>
</div>
<div class="question-anchors" style="display:none;">
  <?php foreach($test->question as $key=>$question):?>
  <a id="question-anchor-<?= $question->id ?>" class="question-anchor question-anchor-<?= $key+1 ?>" type="button" href="<?= Yii::app()->controller->createUrl('test/process', array('id'=>$test->id, 'q'=>$key+1)) ?>"><?= $key+1 ?></a>
  <?php endforeach;?>
</div>

<?php
if ($directQuestionNumber > count($questionNumberIdPair) || ! is_numeric($directQuestionNumber)) {
    echo '<div class="question-load-error">Не удалось найти вопрос</div>';
} else {
    echo $this->renderPartial('begin_test', array(
        'test' => $test,
        'directQuestionNumber' => $directQuestionNumber,
        'answerModel' => $answerModel,
        'questionDataArray' => $questionDataArray,
        'questionNumberIdPair' => $questionNumberIdPair,
        'testTimeLimit' => $testTimeLimit,
        'testStartTime' => $testStartTime,
    ));
}
?>