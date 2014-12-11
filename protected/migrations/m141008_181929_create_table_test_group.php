<?php

class m141008_181929_create_table_test_group extends CDbMigration
{
	public function safeUp()
	{
	    $this->createTable('group_test', array(
	        'test_id' => 'INT(11) NOT NULL',
	        'group_id' => 'INT(11) NOT NULL',
	        'PRIMARY KEY (`test_id`, `group_id`)',
	        'INDEX `FK_test_group_idx` (`test_id`)',
	        'INDEX `FK_group_test_idx` (`group_id`)'
	    ));
	     
	    $this->addForeignKey('FK_group_test', 'group_test', 'group_id', 'group', 'id', 'CASCADE', 'RESTRICT');
	    $this->addForeignKey('FK_test_group', 'group_test', 'test_id', 'test', 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
	    $this->dropTable('group_test');
	}
}