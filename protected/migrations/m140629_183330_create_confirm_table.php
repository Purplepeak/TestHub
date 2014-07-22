<?php

class m140629_183330_create_confirm_table extends CDbMigration
{
	public function safeUp()
	{
		$this->createTable('confirm_account', array(
				'id' => 'INT NOT NULL AUTO_INCREMENT',
				'user_name' => 'VARCHAR(30) NOT NULL',
				'user_id' => 'INT NOT NULL',
				'key' => 'VARCHAR(128) NOT NULL',
				'email' => 'VARCHAR(250) NOT NULL',
				'PRIMARY KEY (`id`)'
		));
	}

	public function safeDown()
	{
		$this->dropTable('confirm_account');
	}
}