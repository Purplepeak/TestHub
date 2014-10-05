<?php

class m140912_152004_add_main_thumb_coordinates extends CDbMigration
{
	public function safeUp()
	{
	    $this->addColumn('users', 'avatarX', 'INT DEFAULT NULL');
	    $this->addColumn('users', 'avatarY', 'INT DEFAULT NULL');
	    $this->addColumn('users', 'avatarWidth', 'INT DEFAULT NULL');
	    $this->addColumn('users', 'avatarHeight', 'INT DEFAULT NULL');
	}

	public function safeDown()
	{
	    $this->dropColumn('users', 'avatarX');
	    $this->dropColumn('users', 'avatarY');
	    $this->dropColumn('users', 'avatarWidth');
	    $this->dropColumn('users', 'avatarHeight');
	}
}