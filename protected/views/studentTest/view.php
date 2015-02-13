<?php
/* @var $this TestController */
/* @var $model Test */
?>

<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    showProcessingMessages: false,
    showMathMenu: false,
    messageStyle: "none",
    tex2jax: { 
        inlineMath: [['$','$'],['\\(','\\)']],
        displayMath: [ ['\\[','\\]'] ],
        processEscapes: false,
        processClass: "process-mathjax",
        ignoreClass: "ignore-mathjax"
    }
  });
</script>

<?php
    Yii::app()->clientScript->registerScriptFile('https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
?>

<h2 class='first-header'><?= CHtml::encode($model->test->name) ?></h2>

<div class="test-view-foreword process-mathjax">
  <div class="test-view-paragraph">
    <h3>Предисловие</h3>
  </div>
  <?= $model->test->foreword ?>
</div>

<div class="test-view-info">
  <div  class="test-view-paragraph">
    <h3>Информация</h3>
  </div>
  <?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
    'cssFile'=>Yii::app()->request->baseUrl.'/css/custom-detail-view.css',
	'attributes'=>array(
        array(
            'label' => 'Количество вопросов',
            'type' => 'raw',
            'value' => count($model->test->question)
        ),
        array(
           'label' => 'Минимальный балл',
           'type' => 'raw',
           'value' => $model->test->minimum_score
        ),
        array(
            'label' => 'Время на прохождение теста (мин)',
            'type' => 'raw',
            'value' => $model->test->time_limit
        ),
		'attempts',
		'deadline',
		array(               
            'label'=>'Преподаватель',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->test->teacher->getFullName()),
                                 array('teacher/view','id'=>$model->test->teacher->id)),
        ),
	),
  )); ?>
</div>

<?= CHtml::button('Пройти тест', array('class' => 'start-test-button', 'submit' => array('controller/action'))) ?>