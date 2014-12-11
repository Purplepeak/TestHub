<?php

class m141008_155213_student_test_change extends CDbMigration
{
	public function safeUp()
	{
	    $this->dropTAble('student_test');
	    
	    $this->createTable('student_test', array(
	        'id' => 'INT NOT NULL AUTO_INCREMENT',
	        'attempts' => 'INT(11) NULL',
	        'deadline' => 'TIMESTAMP NULL',
	        'result' => 'INT(11) NULL',
	        'test_id' => 'INT(11) NOT NULL',
	        'student_id' => 'INT(11) NOT NULL',
	        'PRIMARY KEY (`id`)',
	        'INDEX `FK_test_idx` (`test_id`)',
	        'INDEX `FK_student_idx` (`student_id`)'
	    ));
	    
	    $this->addForeignKey('FK_student', 'student_test', 'student_id', 'users', 'id', 'CASCADE', 'RESTRICT');
	    $this->addForeignKey('FK_test', 'student_test', 'test_id', 'test', 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
	    $this->dropTAble('student_test');
	    
	    $this->createTable('student_test', array(
	        'result' => 'INT(11) NULL',
	        'test_id' => 'INT(11) NOT NULL',
	        'student_id' => 'INT(11) NOT NULL',
	        'PRIMARY KEY (`test_id`, `student_id`)',
	        'INDEX `FK_test_idx` (`test_id`)',
	        'INDEX `FK_student_idx` (`student_id`)'
	    ));
	    
	    $this->addForeignKey('FK_student', 'student_test', 'student_id', 'users', 'id', 'CASCADE', 'RESTRICT');
	    $this->addForeignKey('FK_test', 'student_test', 'test_id', 'test', 'id', 'CASCADE', 'RESTRICT');
	}
}