
<?php if($timeOutMessage):?>
<div class="alert alert-info"><?= $timeOutMessage ?></div>
<?php endif;?>
<div class="test-result-info">
  <h2><?= $message ?></h2>
  <p>Ваш балл: <?= $totalScore ?></p>
  <p>Проходной балл: <?= $studentTest->test->minimum_score ?></p>
  <p>Попыток осталось: <?= $studentTest->attempts ?></p>
  <?php if($studentTest->attempts >= 1):?>
  <a class="start-test-button" type="button" href="<?= Yii::app()->createUrl('test/init', array('id'=>$studentTest->test->id)) ?>">Пройти тест еще раз</a>
  <a class="my-tests-button" type="button" href="<?= Yii::app()->createUrl('studentTest/myTests',  array('status'=>'notpassed')) ?>">Перейти к тестам</a>
  <?php endif;?>
</div>
<?php if($testPassed): ?>
<script>
  $('.start-test-button').click(function() {
	  if (confirm("Вы уверены, что хотите перепройти тест? Текущий результат будет удален.")) {
          return true;
      } else {
          return false;
      }
  });
</script>
<?php endif;?>