<?php

class m150204_145451_question_decimal_fix extends CDbMigration
{
	public function safeUp()
	{
	    $this->alterColumn('question', 'answer_number', 'DECIMAL(15,4) NULL');
	    $this->alterColumn('student_answer', 'answer_number', 'DECIMAL(15,4) NULL');
	}

	public function safeDown()
	{
	    $this->alterColumn('question', 'answer_number', 'DECIMAL(9,4) NULL');
	    $this->alterColumn('student_answer', 'answer_number', 'DECIMAL(9,4) NULL');
	}
}