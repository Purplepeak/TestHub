<h2>Мои тесты</h2>
<?php $this->renderPartial('tests_grid', array('model'=>$model));?>
<a class="general-button new-test-button" type="button" href="<?= Yii::app()->controller->createUrl('test/create') ?>">Создать тест</a>