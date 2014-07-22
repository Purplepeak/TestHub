<?php

class TeacherController extends UsersController
{
	
	
    public function beforeAction($action)
	{
		$this->userModel = new Teacher();
		$this->index = 'Teacher';
		return parent::beforeAction($action);
	}
	
	public function actionRegistration()
	{
		$this->userModel = new Teacher('register');
		parent::actionRegistration();
	}
	
	public function actionAdmin()
	{
		$this->userModel = new Teacher('search');
		parent::actionAdmin();
	}
	
	public function actionTests()
	{
		$this->render('//users/_register_form', array('model' => $this->userModel));
		if(2>1) {
			$this->renderPartial('//users/social_register', array('model' => $this->userModel));
		}
	}
}
