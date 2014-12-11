<?php 
class PurificationCommand extends CConsoleCommand {
    
    public function run() {
        $studentTestCriteria = new CDbCriteria();
        $studentTestCriteria->condition = "result IS NULL AND deadline < NOW()";
        
        $studentTest = StudentTest::model();
        $studentTest->updateAll(array('result' => 0), $userCriteria);
    }
}