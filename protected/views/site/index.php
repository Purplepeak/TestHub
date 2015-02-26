<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<link class="size-stylesheet" rel="stylesheet" type="text/css" href="<?= Yii::app()->request->baseUrl?>/css/normal.css" />

<div class="main-page-intro">
  <?php if(Yii::app()->user->hasFlash('success')):?>
    <div class="alert alert-success">
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
  <?php endif; ?>
  <h1>TestHub</h1>
  <div class="th-info">
    <p>Сервис позволяет проверять знания студентов в автоматическом режиме с помощью тестов. Зарегистрируйтесь, чтобы принять участие.</p>
    <div class="main-register-pics">
      <a class="main-student-pic" href="<?= Yii::app()->controller->createUrl('student/registration')?>"><img title="Регистрация студента" src="<?= Yii::app()->request->baseUrl?>/images/student_pic.png"></a>
      <a class="main-teacher-pic" href="<?= Yii::app()->controller->createUrl('teacher/registration')?>"><img title="Регистрация преподавателя" src="<?= Yii::app()->request->baseUrl?>/images/teacher_pic.png"></a>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
	 
	 function imageresize() {
	     var contentwidth = $('.main-register-pics').width();
	     $('.size-stylesheet').attr('href', "<?= Yii::app()->request->baseUrl?>/css/normal.css");
	     
	     if (contentwidth <  '330'){
	         $('.size-stylesheet').attr('href', "<?= Yii::app()->request->baseUrl?>/css/small.css");
	     } else if(contentwidth <  '450') {
	    	 $('.size-stylesheet').attr('href', "<?= Yii::app()->request->baseUrl?>/css/medium.css");
		 }
	 }
	 
	 imageresize();   
	 
	 $(window).bind("resize", function(){
	     imageresize();
	 });
});
</script>