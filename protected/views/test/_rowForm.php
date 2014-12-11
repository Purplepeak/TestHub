<?php $row_id = "question-" . $key ?>
<div class='row-fluid' id="<?php echo $row_id ?>">
    <?php
    $csrfTokenName = Yii::app()->request->csrfTokenName;
    $csrfToken = Yii::app()->request->csrfToken;
    
    $cs = Yii::app()->clientScript;
        $cs->registerScript("DlScript", "
            function addFields(){
                var scenario = $('#" ."Question_" .$key. '_type'. "').val();
                $.ajax({
                    type:'POST',
                    url:'" . CController::createUrl('test/dynamictype') . "',
                    data:{
                        scenario:scenario,
                        key:".$key.",
                        ".$csrfTokenName.":'".$csrfToken."'
                    },
                    success:function(data){ $('#" . $row_id . "').append(data)},
                });
            }
            
            $('#" ."Question_" .$key. '_type'. "').change(function(e){addFields()});
        ");
   //var_dump($model);
    
    echo $form->hiddenField($model, "[$key]id");
    echo $form->updateTypeField($model, $key, "updateType", array('key' => $key));
    ?>
    hhhh
    <div class="span3">
		<?php echo $form->labelEx($model,"[$key]title"); ?>
		<?php echo $form->textArea($model,"[$key]title",array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,"[$key]title"); ?>
	</div>

	<div class="span3">
		<?php echo $form->labelEx($model,"[$key]type"); ?>
		<?php echo $form->dropDownList($model,"[$key]type", array(
		    '' => 'Выберите тип ответа на вопрос',
		    Question::TYPE_ONE => 'Одиночный ответ', 
		    Question::TYPE_MANY => 'Множественный ответ',
		    Question::TYPE_STRING => 'Ответ строкой',
		    Question::TYPE_NUMERIC => 'Ответ числом'
		)); ?>
		<?php echo $form->error($model,"[$key]type"); ?>
	</div>

	<div class="span3">
		<?php echo $form->labelEx($model,"[$key]difficulty"); ?>
		<?php echo $form->textField($model,"[$key]difficulty"); ?>
		<?php echo $form->error($model,"[$key]difficulty"); ?>
	</div>
    
    <div class="span2">
 
            <?php echo $form->deleteRowButton($row_id, $key); ?>
        </div>
</div>