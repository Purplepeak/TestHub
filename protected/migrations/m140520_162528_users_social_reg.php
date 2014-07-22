<?php

class m140520_162528_users_social_reg extends CDbMigration
{
	public function up()
	{
		$this->addColumn('users', 'oauth_provider', 'ENUM("facebook","vk","mail") NULL');
		$this->addColumn('users', 'oauth_uid', 'BIGINT NULL');
	}

	public function down()
	{
		$this->dropColumn('users', 'oauth_uid');
		$this->dropColumn('users', 'oauth_provider');
	}
}