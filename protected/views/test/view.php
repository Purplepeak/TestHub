<?php
if($studentTestModel) {
    $testUrl = Yii::app()->createUrl('test/init', array('id'=>$model->id));
    $startButtonLabel = 'Начать тест';
    
    if($testInProgress) {
        $testUrl = Yii::app()->createUrl('test/process', array('id'=>$model->id));
        $startButtonLabel = 'Продолжить тест';
    }
}

if($isTeacher) {
    $updateTestUrl = Yii::app()->createUrl('test/update', array('id'=>$model->id));
    $deleteTestUrl = Yii::app()->createUrl('test/delete', array('id'=>$model->id));
}
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
    Yii::app()->clientScript->registerScript('DeliteConfirmation', "
        $('.delete-test-view').click(function() {
            if (confirm('Вы уверены, что хотите удалить тест?')) {
                return true;
            } else {
                return false;
            }
        });        
    ");
?>

<h2 class='first-header'><?= CHtml::encode($model->name) ?></h2>

<div class="test-view-foreword process-mathjax">
  <div class="test-view-paragraph">
    <h3>Предисловие</h3>
  </div>
  <?= $model->foreword ?>
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
            'value' => count($model->question)
        ),
		'minimum_score',
		'time_limit',
		'attempts',
		'deadline',
		array(               
            'label'=>'Преподаватель',
            'type'=>'raw',
            'value'=>CHtml::link(CHtml::encode($model->teacher->getFullName()), array('teacher/view','id'=>$model->teacher->id)),
        ),
	),
  )); ?>
</div>
<?php if($isTeacher):?>
<a class="general-button update-test-view" type="button" href="<?= $updateTestUrl ?>">Изменить</a>
<?php endif;?>
<?php if($studentTestModel):?>
<a class="start-test-button" type="button" href="<?= $testUrl ?>"><?= $startButtonLabel ?></a>
<?php endif;?>