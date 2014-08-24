<?php

class m140816_154934_user_active_type_change extends CDbMigration
{
	public function up()
	{
	    $this->alterColumn('users', 'active', "TINYINT(1) NOT NULL DEFAULT '0'");
	}

	public function down()
	{
	    $this->alterColumn('users', 'active', "BINARY(1) NOT NULL DEFAULT '0'");
	}
}