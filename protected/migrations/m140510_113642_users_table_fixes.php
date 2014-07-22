<?php

class m140510_113642_users_table_fixes extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('users', 'type', 'ENUM("student", "teacher", "admin") NOT NULL');
		$this->alterColumn('users', 'gender', 'ENUM("male", "female", "undefined") NOT NULL');
		$this->alterColumn('users', 'time_registration', 'TIMESTAMP NOT NULL');
	}

	public function down()
	{
		echo "m140510_113642_users_table_fixes does not support migration down.\n";
		return false;
	}
}