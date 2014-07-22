<?php 
require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. 'recaptchalib.php');

class SReCaptchaWidget extends CInputWidget
{
	public $publicKey;
	public $lang = 'en';
	public $theme;
	
	public function init() 
	{
		$cs = Yii::app()->getClientScript();
		
		if (!$cs->isScriptRegistered(get_class($this) . '_options')) {
			$script = <<<EOP
var RecaptchaOptions = {
   theme : '{$this->theme}',
   lang : '{$this->lang}',
};
EOP;
			$cs->registerScript(get_class($this) . '_options', $script, CClientScript::POS_HEAD);
		}
	}
	
	public function run() 
	{
		echo recaptcha_get_html($this->publicKey);
	}
} 