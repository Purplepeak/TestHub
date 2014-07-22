<?php

class m140629_160041_users_account_status extends CDbMigration
{
	public function up()
	{
		$this->addColumn('users', 'active', "BINARY(1) NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		$this->dropColumn('users', 'active');
	}
}