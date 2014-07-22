<?php

class m140624_152147_delete_column_username extends CDbMigration
{
	public function up()
	{
		$this->dropColumn('users', 'username');
	}

	public function down()
	{
		$this->addColumn('users', 'username', 'VARCHAR(50) NOT NULL');
	}
}