<?php

class m140608_175227_social_accounts_table_add_url extends CDbMigration
{
	public function up()
	{
		$this->addColumn('social_accounts', 'url', 'TEXT NOT NULL');
	}
	
	public function down()
	{
		$this->dropColumn('social_accounts', 'url');
	}
}