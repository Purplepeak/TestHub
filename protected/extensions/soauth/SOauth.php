<?php 

class SOauth extends CApplicationComponent
{
	public $services = array();
	
	public function init() {
		if (!Yii::getPathOfAlias('soauth')) {
			Yii::setPathOfAlias('soauth', dirname(__FILE__));
		}
	
		Yii::import('soauth.*');
		Yii::import('soauth.services.*');
	}
	
	public function getClass($provider, $gender = array()) {
		if (!isset($this->services[$provider])) {
			throw new SOauthException("Undefined service name: {$provider}");
		}
		
		$service = $this->services[$provider];
		
		$class = $service['class'];
		
		$identity = new $class();
		$identity->init($provider, $gender);
		
		return $identity;
	}
	
	public function renderWidget($properties = array()) {
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'SOauthWidget.php';
		$widget = Yii::app()->getWidgetFactory()->createWidget($this, 'SOauthWidget', $properties);
		$widget->action = $properties['action'];
		$widget->scenario = $properties['scenario'];
		$widget->init();
		$widget->run();
	}
	
	public function getServices() {
		$services = false;
		
		if (false === $services || !is_array($services)) {
			$services = array();
			foreach ($this->services as $service => $options) {
				$class = $this->getClass($service);
				$services[$service] = (object)array(
						'id' => $class->provider,
						'title' => $class->title,
						'type' => $class->type,
				);
			}
		}
		return $services;
	}
}