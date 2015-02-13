<h2>Мои тесты</h2>
<?php 
$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateParents' => true,
    'htmlOptions' => array(
        'class' => 'student-test-status-nav nav nav-pills'
    ),
    'items' => array(
        array(
            'label' => 'Невыполненные',
            'url' => array(
                'studentTest/myTests',
                'status'=>'notpassed'
            ),
            'linkOptions' => array('class'=>'student-tests-link'),
        ),
        array(
            'label' => 'Выполненные',
            'url' => array(
                'studentTest/myTests',
                'status'=>'passed'
            ),
            'linkOptions' => array('class'=>'student-tests-link'),
        ),
        array(
            'label' => 'Проваленные',
            'url' => array(
                'studentTest/myTests',
                'status'=>'failed'
            ),
            'linkOptions' => array('class'=>'student-tests-link'),
        ),
    )
));
?>
<div id="student-tests" class="student-tests">
  <?php $this->renderPartial('s_tests_grid', array('model'=>$model));?>
</div>