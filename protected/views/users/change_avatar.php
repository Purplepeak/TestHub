<?php 
if(!isset($user->avatar)) {
    $avatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultAvatar'];
} else {
    $avatar = Yii::app()->request->hostInfo . Yii::app()->request->baseUrl . $user->main_avatar;
}

//var_dump(Yii::app()->request->hostInfo.Yii::app()->request->baseUrl);

?>

<script type="text/javascript">
  var srcImg = "http://www.krautchan.net/files/1409749161001.jpg";
  var csrfToken = "<?= Yii::app()->request->getCsrfToken()?>";
</script>

<div class="change-avatar-header">
  <h2>Выберите новый аватар</h2>
  <p>Картинка должна быть в формате: jpg, gif, png.</p>
</div>
<div class="change-avatar-wrapper">
  <img class="change-avatar" src="<?= $avatar?>">
</div>
<div class="form th-avatar-from">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'avatar-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
));
?>

<?php if($model->scenario == 'changeAvatar'):?>
	<div class="row upload-button">
      <?php echo $form->fileField($model, 'newAvatar', array('class' => 'avatar-input')); ?>
    </div>
    <?php echo $form->hiddenField($model, 'avatarX', array('class' => 'image-x')); ?>
    <?php echo $form->hiddenField($model, 'avatarY', array('class' => 'image-y')); ?>
    <?php echo $form->hiddenField($model, 'avatarWidth', array('class' => 'crop-width')); ?>
    <?php echo $form->hiddenField($model, 'avatarHeight', array('class' => 'crop-height')); ?>
<?php endif;?>
<div class="row avatar-send">
		<?php echo CHtml::submitButton('Отправить'); ?>
</div>
<?php echo $form->error($model, 'newAvatar'); ?>

<?php $this->endWidget();?>
</div><!-- form -->

<div class="src-avatar">
</div>