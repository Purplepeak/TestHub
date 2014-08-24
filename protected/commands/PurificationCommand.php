<?php 
class PurificationCommand extends CConsoleCommand {
    
    public function run() {
        $userCriteria = new CDbCriteria();
        $userCriteria->condition = "active=:active AND time_registration < DATE_SUB(NOW(), INTERVAL :interval DAY)";
        $userCriteria->params = array(
            ':active' => false,
            ':interval' => Yii::app()->params['purificationTime']
        );
        
        $users = Users::model();
        $users->deleteAll($userCriteria);
    }
}