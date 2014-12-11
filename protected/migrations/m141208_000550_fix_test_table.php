<?php

class m141208_000550_fix_test_table extends CDbMigration
{
	public function up()
	{
	    $this->alterColumn('test', 'create_time', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
	    $this->alterColumn('test', 'deadline', 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
	}
}