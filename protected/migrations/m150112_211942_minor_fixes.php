<?php

class m150112_211942_minor_fixes extends CDbMigration
{
	public function safeUp()
	{
	    $this->alterColumn('student_answer', 'answer_text', 'VARCHAR(200) NULL');
	    $this->alterColumn('question', 'precision_percent', 'DECIMAL(6,5) NULL');
	}

	public function safeDown()
	{
		$this->alterColumn('student_answer', 'answer_text', 'VARCHAR(50) NULL');
	    $this->alterColumn('question', 'precision_percent', 'DECIMAL(3,3) NULL');
	}
}