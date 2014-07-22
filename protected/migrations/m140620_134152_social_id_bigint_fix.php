<?php

class m140620_134152_social_id_bigint_fix extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('social_accounts', 'social_user_id', "BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'");
	}

	public function down()
	{
		$this->alterColumn('social_accounts', 'social_user_id', "BIGINT(20) UNSIGNED NOT NULL");
	}
}