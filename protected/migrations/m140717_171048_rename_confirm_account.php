<?php

class m140717_171048_rename_confirm_account extends CDbMigration
{
	
	public function safeUp()
	{
		$this->addColumn('confirm_account', 'create_time', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->addColumn('confirm_account', 'scenario', 'ENUM("confirm", "restore") NOT NULL');
		$this->renameTable('confirm_account', 'account_interaction');
	}

	public function safeDown()
	{
		$this->dropColumn('account_interaction', 'scenario');
		$this->dropColumn('account_interaction', 'create_time');
		$this->renameTable('account_interaction', 'confirm_account');
	}
}