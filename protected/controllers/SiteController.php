<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public $layout='//layouts/column1';
	
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->render('index');
	}
	
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest) {
				echo $error['message'];
			} else {
			    $defaultMessage = 'Сервис временно испытывает проблемы. Приносим извинения за временные неудобства.';
			    
				switch($error['type']) {
					case 'SOauthException':
					    $error['message'] = 'Сервис временно испытывает проблемы с входом через социальные сети, повторите попытку позже. Приносим извинения за временные неудобства.';
					    break;
					case 'Swift_SwiftException':
					    $error['message'] = $defaultMessage;
					    break;
					case 'SAvatarCropperException':
					    $error['message'] = $defaultMessage;
					    break;
					default:
					    $error['message'] = $defaultMessage;
					    break;
				}
				
				$layout = 'error';
				
				switch($error['code']) {
					case '404':
					    $layout = 'error404';
					    break;
					case '500':
					    $error['message'] = $defaultMessage;
					    break;
					default:
					    $error['message'] = 'Возникла непредвиденная ошибка. Приносим извинения за временные неудобства.';
					    break;
				}
				
				$this->render($layout, $error);
			}
		}
	}
	
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-Type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function actionGetAvatar($id, $res, $method, $img)
	{
	    $user = Users::model()->findByPk($id);
	    $avatarDir = sprintf("%s" . "/" . "%d" . "/", Yii::getPathOfAlias('avatarFolder'), $user->id);
	    $croppedAvatarDir = sprintf("%s" . "%d" . "%s" . "%d" . "/" . "%s" . "/", $avatarDir, $user->avatarWidth, 'x', $user->avatarHeight, $method);
	    
	    
	    $resReg = '{(\d+)x(\d+)}';
	    
	    if (!preg_match($resReg, $res, $thumbRes)) {
	        Yii::log("Regular expression {$resReg} for the thumbnail size does not match the link.", CLogger::LEVEL_ERROR, 'application.extensions.savatar');
	        throw new SAvatarCropperException("Regular expression {$resReg} for the thumbnail size does not match the link.");
	    }
	    
	    $thumbWidth = $thumbRes[1];
	    $thumbHeight = $thumbRes[2];
	    
	    
	    $cropper = new SAvatarCropper($avatarDir);
	    
	    $cropper->setAllowedSizes(Yii::app()->params['allowedAvatarSizes']);
	    
	    if(!$cropper->isAllowedSize($thumbWidth, $thumbHeight)) {
	        throw new CHttpException(404);
	    }
	     
	    $cropper->getResizedImage($croppedAvatarDir . $img, $thumbWidth, $thumbHeight, $method);
	}
}