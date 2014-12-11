<?php

class m141012_184349_create_question_tables extends CDbMigration
{

    public function safeUp()
    {
        $this->alterColumn('test', 'name', 'VARCHAR(255) NOT NULL');
        
        $this->createTable('question', array(
            'id' => 'INT NOT NULL AUTO_INCREMENT',
            'title' => 'TEXT NOT NULL',
            'type' => 'ENUM("select_one", "select_many", "numeric", "string") NOT NULL',
            'difficulty' => 'INT NOT NULL',
            'answer_id' => 'INT NULL',
            'answer_text' => 'VARCHAR(50)',
            'answer_number' => 'DECIMAL(9,4) NULL',
            'precision_percent' => 'DECIMAL(5,5) NULL',
            'picture' => 'VARCHAR(200) NULL',
            'test_id' => 'INT NOT NULL',
            'PRIMARY KEY(`id`)',
            'INDEX `FK_question_test_idx` (`test_id`)'
        ));
        
        $this->createTable('answer_options', array(
            'id' => 'INT NOT NULL AUTO_INCREMENT',
            'question_id' => 'INT NOT NULL',
            'option_text' => 'TEXT NOT NULL',
            'option_number' => 'INT NOT NULL',
            'PRIMARY KEY(`id`)',
            'INDEX `FK_options_question_idx` (`question_id`)'
        ));
        
        $this->createTable('correct_answers', array(
            'question_id' => 'INT NOT NULL',
            'c_answer' => 'INT NOT NULL',
            'PRIMARY KEY(`question_id`, `c_answer`)',
            'INDEX `FK_correct_question_idx` (`question_id`)',
            'INDEX `FK_correct_answer_idx` (`c_answer`)'
        ));
        
        $this->createTable('s_many_answers', array(
            'answer_id' => 'INT NOT NULL',
            's_answer' => 'INT NOT NULL',
            'PRIMARY KEY(`answer_id`, `s_answer`)',
            'INDEX `FK_student_question_idx` (`answer_id`)',
            'INDEX `FK_student_answer_idx` (`s_answer`)'
        ));
        
        $this->createTable('student_answer', array(
            'id' => 'INT NOT NULL AUTO_INCREMENT',
        	'question_id' => 'INT NOT NULL',
            'student_id' => 'INT NOT NULL',
            'answer_id' => 'INT NULL',
            'answer_text' => 'VARCHAR(50)',
            'answer_number' => 'DECIMAL(9,4) NULL',
            'exec_time' => 'INT NOT NULL',
            'result' => 'INT NULL',
            'test_result' => 'INT NULL',
            'PRIMARY KEY(`id`)',
            'INDEX `FK_answer_question_idx` (`question_id`)',
            'INDEX `FK_answer_test_result_idx` (`test_result`)'
        ));
        
        $this->addForeignKey('FK_options_question', 'answer_options', 'question_id', 'question', 'id', 'CASCADE', 'RESTRICT');
        
        $this->addForeignKey('FK_correct_question', 'correct_answers', 'question_id', 'question', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('FK_correct_answer', 'correct_answers', 'c_answer', 'answer_options', 'id', 'CASCADE', 'RESTRICT');
        
        $this->addForeignKey('FK_student_question', 's_many_answers', 'answer_id', 'student_answer', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('FK_student_answer', 's_many_answers', 's_answer', 'answer_options', 'id', 'CASCADE', 'RESTRICT');
        
        $this->addForeignKey('FK_question_test', 'question', 'test_id', 'test', 'id', 'CASCADE', 'RESTRICT');
        
        $this->addForeignKey('FK_answer_question', 'student_answer', 'question_id', 'question', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('FK_answer_test_result', 'student_answer', 'test_result', 'student_test', 'id', 'CASCADE', 'RESTRICT');
    }

    public function safeDown()
    {
        //$this->alterColumn('test', 'name', 'VARCHAR(45) NOT NULL');
        
    	$this->dropForeignKey('FK_options_question', 'answer_options');
    	
    	$this->dropForeignKey('FK_correct_question', 'correct_answers');
    	$this->dropForeignKey('FK_correct_answer', 'correct_answers');
    	
    	$this->dropForeignKey('FK_student_question', 's_many_answers');
    	$this->dropForeignKey('FK_student_answer', 's_many_answers');
    	
    	$this->dropForeignKey('FK_question_test', 'question');
    	$this->dropForeignKey('FK_answer_question', 'student_answer');
    	$this->dropForeignKey('FK_answer_test_result', 'student_answer');
    	
    	
    	$this->truncateTable('question');
    	$this->truncateTable('answer_options');
    	$this->truncateTable('correct_answers');
    	$this->truncateTable('s_many_answers');
    	$this->truncateTable('student_answer');
    	
    	$this->dropTable('question');
    	$this->dropTable('answer_options');
    	$this->dropTable('correct_answers');
    	$this->dropTable('s_many_answers');
    	$this->dropTable('student_answer');
    	
    }
}