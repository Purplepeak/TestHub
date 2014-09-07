<?php

class m140904_205750_add_main_avatar_column extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('users', 'main_avatar', 'VARCHAR(200) DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('users', 'main_avatar');
	}
}