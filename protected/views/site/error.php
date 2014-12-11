<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Error';
?>

<h2>Ошибка <?php echo $code; ?></h2>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>
<div class="contact-admin">
    Электронный адрес для связи с администрацией: <?= CHtml::link(Yii::app()->params['adminEmail'], array('site/contact')); ?>.
</div>