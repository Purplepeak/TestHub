<?php

class m141202_201245_images_table_create extends CDbMigration
{

    public function up()
    {
        $this->createTable('test_images', array(
	        'id' => 'INT NOT NULL AUTO_INCREMENT',
	        'link' => 'VARCHAR(200) NOT NULL',
            'type' => 'ENUM("question", "test") NOT NULL',
            'question_id' => 'INT NULL',
            'test_id' => 'INT NULL',
	        'PRIMARY KEY (`id`)',
	        'INDEX `FK_question_image_idx` (`question_id`)',
            'INDEX `FK_test_image_idx` (`test_id`)'
	    ));
        
        $this->addForeignKey('FK_question_image', 'test_images', 'question_id', 'question', 'id', 'CASCADE', 'RESTRICT');
        $this->addForeignKey('FK_test_image', 'test_images', 'test_id', 'test', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropForeignKey('FK_question_image', 'test_images');
        $this->dropForeignKey('FK_test_image', 'test_images');
        
        $this->dropTable('test_images');
    }
}