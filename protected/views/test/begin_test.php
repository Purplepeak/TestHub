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
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.plugin.js', CClientScript::POS_HEAD);
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.countdown.min.js', CClientScript::POS_HEAD);
    $studentAnswersQuestionId = $test->getStudentAnswersByQuestionsId($questionNumberIdPair);
?>

<script type='text/javascript'>

function setupMathJax() {
	  var head = document.getElementsByTagName("head")[0], script;
	  script = document.createElement("script");
	  script.type = "text/x-mathjax-config";
	  script[(window.opera ? "innerHTML" : "text")] =
	    "MathJax.Hub.Config({\n" +
	    "  tex2jax: { inlineMath: [['$','$'],['\\(','\\)']], displayMath: [ ['\\[','\\]'] ], processEscapes: false, processClass: 'process-mathjax', ignoreClass: 'ignore-mathjax' }\n" +
	    "});"
	  head.appendChild(script);
	  script = document.createElement("script");
	  script.type = "text/javascript";
	  script.src  = "http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML";
	  head.appendChild(script);
}

function setupHistoryClicks() {
	$("a.question-anchor").each(function() {
		addClicker(this);
    });
}

function addClicker(link) {
    link.addEventListener("click", function(e) {
	    swapQuestion(link.href);
	    history.pushState(null, null, link.href);
	    e.preventDefault();
	}, false);
}

function swapQuestion(href) {
	var regexp = /^.+[\/\?]q[\=\/]([\d\w]+)$/i;
	var questionNumber = 1;
	if(matches = href.match(regexp)) {
		questionNumber = matches[1];
	}
	
	if(questionNumber === 'end') {
        $('.question-anchors').css('display', 'block');
        $('.question-anchors').off('mouseleave');
	}
	
	var data = {};
	data['testID'] = <?= $test->id ?>;
	data['questionDataArray'] =  <?= CJSON::encode(array('questionDataArray' => $questionDataArray)) ?>.questionDataArray[ questionNumber ];
	data['questionNumberIdPair'] = <?= CJSON::encode($questionNumberIdPair) ?>;
	data['questionNumber'] = questionNumber;
	data['testTimeLimit'] = <?= $testTimeLimit ?>;
	data['testStartTime'] = <?= $testStartTime ?>;
	data['<?= Yii::app()->request->csrfTokenName ?>'] = '<?= Yii::app()->request->csrfToken ?>';
	$.ajax({
        url:"<?= Yii::app()->createUrl('test/postQuestion') ?>",
        type: "POST",
        data: data,
        success: function(data){
            document.getElementById("answer-question-form").innerHTML = data;
            changeSkipQuestionButton();
            MathJax.Hub.Queue(["Typeset",MathJax.Hub,"answer-question-form"]);
        },
        error: function(xhr, status, error) {
        	var err = eval("(" + xhr.responseText + ")");
        	console.log(err.Message);
        }
    });
}

function changeSkipQuestionButton() {
	if(nextQuestionNumber = document.getElementById('nextQuestionNumber')) {
		var href = "<?= Yii::app()->controller->createUrl('test/process', array('id'=>$test->id)) ?>/q/" + nextQuestionNumber.value;
		$('.answer-buttons-container').append('<a id="skip-question" class="skip-question" type="button" href="'+href+'">Пропустить</a>');
		addClicker(document.getElementById("skip-question"));
    } else {
    	$('.skip-question').css('display', 'none');
    }
}

function toggleQuestionAnchors() {
	$('.question-anchors').toggle('500');
}

function serverTime() {
	var time = null; 
    $.ajax({url: "<?= Yii::app()->controller->createUrl('site/getServerTime') ?>", 
        async: false, dataType: 'text', 
        success: function(text) { 
            time = new Date(text); 
        }, error: function(http, message, exc) { 
            time = new Date(); 
    }}); 
    return time; 
}

window.onload = function() {
	var timeLimit = <?= $testTimeLimit ?>;
	var countdownFormat = 'MS';
	if(timeLimit > 3600) {
		countdownFormat = 'HMS';
	}
	
	$('.test-countdown-clock').countdown({
		until: new Date("<?= date('Y/m/d H:i:s', $testStartTime + $testTimeLimit) ?>"),
		serverSync: serverTime,
		format: countdownFormat,
		compact: true,
		onExpiry: function() {
			document.getElementById("answer-form").submit();
		}
	});
	
	if (!supports_history_api()) { return; }
	setupHistoryClicks();
	window.setTimeout(function() {
	  window.addEventListener("popstate", function(e) {
	    swapQuestion(location.pathname);
	  }, false);
	}, 1);
	
	changeSkipQuestionButton();
	
	$('.answer-question-form').on('click', '.test-question-counter', function() {
		toggleQuestionAnchors();
    });
	$('.question-anchors').mouseleave(function() {
		toggleQuestionAnchors();
	});
}

function supports_history_api() {
	  return !!(window.history && history.pushState);
}

</script>

<div id="answer-question-form" class=" form answer-question-form">
<?php
echo $this->renderPartial('test_question', array(
    'answerModel' => $answerModel,
    'testID' => $test->id,
    'questionNumber' => $directQuestionNumber,
    'questionNumberIdPair' => $questionNumberIdPair,
    'questionDataArray' => $questionDataArray[$directQuestionNumber],
    'numberOfQuestions' => count($questionNumberIdPair),
    'studentAnswersQuestionId' => $studentAnswersQuestionId,
    'testTimeLimit' => $testTimeLimit,
    'testStartTime' => $testStartTime,
    'questionAlert' => ''
));
?>
</div>
