<?php

class m150119_181058_add_test_start_time extends CDbMigration
{
	public function safeUp()
	{
	    $this->addColumn('student_test', 'start_time', 'TIMESTAMP NULL');
	    $this->addColumn('student_test', 'end_time', 'TIMESTAMP NULL');
	    $this->alterColumn('student_answer', 'exec_time', 'INT NULL');
	}

	public function safeDown()
	{
		$this->dropColumn('student_test', 'start_time');
		$this->dropColumn('student_test', 'end_time');
		$this->alterColumn('student_answer', 'exec_time', 'INT NOT NULL');
	}
}