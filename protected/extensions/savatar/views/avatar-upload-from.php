<script type="text/javascript">
  var uploadImageConfig = <?= json_encode($uploadImageConfig) ?>;
  var imgAreaSelectConfig = <?= json_encode($imgAreaSelectConfig) ?>;
  var previewMaxWidth = <?= $previewMaxWidth ?>;
  var previewMaxHeight = <?= $previewMaxHeight ?>;
</script>
<div class="s-avatar-form">
  <?php echo CHtml::beginForm($action, 'post', array('enctype' => 'multipart/form-data', 'class' => 'avatar-uploader', 'id' => 'avatar-uploader')); ?>
  <div class="s-upload-button">
    <?php echo CHtml::activeFileField($model, $modelAttributes['avatarFileAtt'], array('class' => 's-avatar-input')); ?>
  </div>
  <?php echo CHtml::activeHiddenField($model, $modelAttributes['avatarX'], array('class' => 'image-x')); ?>
  <?php echo CHtml::activeHiddenField($model, $modelAttributes['avatarY'], array('class' => 'image-y')); ?>
  <?php echo CHtml::activeHiddenField($model, $modelAttributes['avatarWidth'], array('class' => 'crop-width')); ?>
  <?php echo CHtml::activeHiddenField($model, $modelAttributes['avatarHeight'], array('class' => 'crop-height')); ?>
  
  <div class="s-avatar-send">
		<?php echo CHtml::submitButton('Отправить'); ?>
  </div>
  
  <?php echo CHtml::error($model, $modelAttributes['avatarFileAtt']); ?>
  
  <?php echo CHtml::endForm(); ?>
</div>

<div class="js-avatar-wrapper">
</div>