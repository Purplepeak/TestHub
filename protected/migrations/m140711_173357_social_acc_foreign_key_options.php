<?php

class m140711_173357_social_acc_foreign_key_options extends CDbMigration
{
	public function safeUp()
	{
		$this->dropForeignKey('FK_social_user', 'social_accounts');
		$this->addForeignKey('FK_social_user', 'social_accounts', 'user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
		$this->dropForeignKey('FK_social_user', 'social_accounts');
		$this->addForeignKey('FK_social_user', 'social_accounts', 'user_id', 'users', 'id', 'NO ACTION', 'NO ACTION');
	}
}