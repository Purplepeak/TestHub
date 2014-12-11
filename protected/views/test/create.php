<?php
$deleteImageUrl = $this->createUrl('testImagesController/deleteImage');
$csrfTokenName = Yii::app()->request->csrfTokenName;
$csrfToken = Yii::app()->request->csrfToken;
$validateDataUrl = $this->createUrl('question/validateData');
$newOptionUrl = $this->createUrl('question/optionField');

$this->widget('ImperaviRedactorWidget', array(
    'selector' => '.foreword-redactor',
    
    'options' => array(
        'lang' => 'ru',
        'toolbarFixed' => false,
        'imageLink'=> false,
        'imageUpload' => Yii::app()->createUrl('testForewordImages/tmpUpload'),
        'imageUploadParam' => 'TestForewordImage[imageFile]',
        'imageUploadErrorCallback' => 'js:function(json){ alert(json.message); }',
        'imageDeleteCallback' => "js:
            function(url, image){ 
                $.ajax({
                    url:'" . $deleteImageUrl . "',
                    type: 'POST',
                    data: {
                        url:url,
                        " . $csrfTokenName . ": '" . $csrfToken . "'
                    },
        }); 
            }",
        'uploadImageFields' => array(
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
        ),
        'uploadFileFields' => array(
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
        )
    )
));

if ($test->scenario === 'insert') {
    $pageLabel = 'Создать тест';
}

if ($test->scenario === 'update') {
    $pageLabel = 'Изменить тест';
}

echo "<h2>{$pageLabel}</h2>";
$cs = Yii::app()->clientScript;

$cs->registerScript("AjaxValidation", "
        $('#test-form').submit(function(event){
            if($('.question-forms').is(':empty')) {
                event.preventDefault();
            }
        
            if($('.qeustion-forms').is(':empty')) {
                $('.qeustions-empty-error').append('Добавьте вопросы, которые будет содержать тест.');
                $('.qeustions-empty-error').addClass('alert alert-danger');
            }
        });
        
        $(document).on('blur','.questionField',function(e){
        e.preventDefault();
        var id= $(this).attr('id');
        var name= $(this).attr('name');
        var name = name.match(/\[([a-zA-Z_-]+)\]/);
        var name = name[1];
    
    $.ajax({
        url:'" . $validateDataUrl . "',
        type: 'POST',
        data: {
            value: $.trim($(this).val()), 
            name: name,
            scenario: $(this).parents('div[id^=question-]').children('.js-question-type').val(),
            " . $csrfTokenName . ": '" . $csrfToken . "'
        },
        success :function(data){
            var parent = $('#'+id).parent();
        
            if($.trim(data))
            {   console.log('a');
                parent.removeClass('success');
                parent.addClass('error');
                if(parent.children('.errorMessage')[0]) {
                    parent.children('.errorMessage').remove();
                }
                parent.append(data);
            }
            else
            {
                if(!parent.hasClass('success'))
                {                       
                    parent.removeClass('error');
                    parent.addClass('success');
                    //$('#'+id).next().remove();
                    parent.children('.errorMessage').remove();
        console.log('b');
                }
        console.log('c');
            }

        },
        error:function(){
        },
    });
});
    ");

$cs->registerScript('QuestionOption', "
            // Функция добавляет поле для ввода варианта ответа
    
            function addOption(input){
                var parentClass = $(input).attr('data-add');
    
                var key = parentClass.match(/\d+$/);
    
                // <div>, который содержит все варианты ответов
                var parent = $('.'+parentClass);
    
                // Вычисляем номер следующего варианта ответа
                var answerOptionNumber = parent.children('.row').size() + 1;
    
                var optionIdArray = new Array();
                var newOptionId;
    
                $('.'+parentClass+' div[class^=row]').each(function () {
                     var optionId = $(this).attr('class').match(/answer-option-(\d+)/);
                     optionIdArray.push( optionId[1] );
                });
    
                if(optionIdArray.length === 0) {
                   newOptionId = 1;
                } else {
                   newOptionId = Math.max.apply(Math, optionIdArray) + 1;
                }
    
                //console.log(optionIdArray);
    
                //newOptionId += 1;
    
                // GET запрос на экшен, который рендерит текстовое поле для ввода варианта
                $.ajax({
                    url:'" . $newOptionUrl . "',
                    data:{
                        i:newOptionId,
                        key:key[0],
                        number:answerOptionNumber
                    },
                    cache: false,
                    dataType: 'html',
                    success:function(data){
                        parent.append(data);
                    },
                });
            };
    
            // Функция удаляет поле варианта ответа и изменяет номера вариантов в соответствии с их количеством
    
            function deleteOption(input){
    
                // <div>, который содержит все варианты ответов
                var optionsContainer = $(input).parents().eq(1);
    
                // Удаляем указанный <div>
                $(input).parent().remove();
    
                // Вычисляем класс optionsContainer
                var optionsContainerClass = optionsContainer.attr('class');
    
                var optionNumber = new Array();
    
                // Добавляем в массив классы оставшихся после удаления вариантов
                $('.'+optionsContainerClass+' div[class^=answer-option-number]').each(function () {
                     optionNumber.push( $(this).attr('class') );
                });
    
                //console.log(optionNumber);
    
                var i=1;
    
                // Пересчитываем номера оставшихся в результате выполнения функции вариантов ответа
                $.each(optionNumber, function( index, value ) {
                    optionsContainer.find('.'+value).html(i+')');
                    i++;
                });
            };
        ", CClientScript::POS_HEAD);
?>

<?php $this->renderPartial('test_form', array('test'=>$test, 'questions'=>$questions, 'pageLabel'=>$pageLabel)); ?>