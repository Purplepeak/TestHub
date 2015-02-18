<?php 
    if($scenario == 'registration') {
    	$message = 'Регистрация';
    } elseif($scenario == 'login') {
    	$message = 'Вход';
    }
?>

<div class="services-bs">
	<?php
		foreach ($services as $name => $service) {
			$html = '<i class="fa fa-' .$service->id. '"></i>' .$message. ' через ' .$service->title;
			$html = CHtml::link($html, array_merge(array($action, 'service' => $name), $additionalQuery), array(
			'class' => 'btn btn-block btn-social btn-' .$service->id,
			));
			echo $html;
		}
	?>
</div>
