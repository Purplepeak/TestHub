<?php 
require_once(dirname(__FILE__) .DIRECTORY_SEPARATOR. 'recaptchalib.php');

class SReCaptchaValidator extends CValidator
{
	public $privateKey;
	
	public function validateAttribute($object, $attribute)
	{
	$valid = false;
      if (isset($_POST['recaptcha_challenge_field']) && isset($_POST['recaptcha_response_field'])) {
        if ($session = Yii::app()->session) {
          if (isset($session['captchaCash']) && isset($session['captchaCash']['question'])
            && $session['captchaCash']['question'] == $_POST['recaptcha_challenge_field']
          ) {
            if (isset($session['captchaCash']['answer']) && 
                $session['captchaCash']['answer'] == sha1($_POST['recaptcha_response_field'])) {
              $valid = true;
            }
          } else {
            $resp = recaptcha_check_answer(
              $this->privateKey,
              $_SERVER['REMOTE_ADDR'],
              $_POST['recaptcha_challenge_field'],
              $_POST['recaptcha_response_field']
            );
            if ($resp->is_valid) {
              if (Yii::app()->session) {
                Yii::app()->session['captchaCash'] = array(
                  'question' => $_POST['recaptcha_challenge_field'],
                  'answer' => sha1($_POST['recaptcha_response_field']),
                );
              }
              $valid = true;
            }
          }
        }
      }
      if (!$valid) {
        $this->addError($object, $attribute, 'Проверочный код введен неверно.');
      }
	}
}


