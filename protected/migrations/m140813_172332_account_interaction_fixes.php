<?php

class m140813_172332_account_interaction_fixes extends CDbMigration
{
	public function safeUp()
	{
	    $this->dropColumn('account_interaction', 'user_name');
	    $this->addForeignKey('FK_account_interaction', 'account_interaction', 'user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
	}

	public function safeDown()
	{
	    $this->addColumn('account_interaction', 'user_name', 'INT NOT NULL');
	    $this->dropForeignKey('FK_account_interaction', 'account_interaction');
	}
}