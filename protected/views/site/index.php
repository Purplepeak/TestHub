<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<div class="main-page-intro">
  <?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="alert alert-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
  <?php endif; ?>
  <h1>Добро пожаловать!</h1>
  <p>TestHub - это сервис, который позволяет преподавателям проверять знания студентов в автоматическом режиме с помощью тестов.</p>
</div>