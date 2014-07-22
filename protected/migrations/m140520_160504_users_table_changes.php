<?php

class m140520_160504_users_table_changes extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('users', 'password', 'VARCHAR(200) NULL');
		$this->alterColumn('users', 'email', 'VARCHAR(100) NULL');
	}

	public function down()
	{
		$this->alterColumn('users', 'password', 'VARCHAR(200) NOT NULL');
		$this->alterColumn('users', 'email', 'VARCHAR(100) NOT NULL');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}