<?php

class m141008_175852_delete_test_rules extends CDbMigration
{
	public function up()
	{
	    $this->dropColumn('test', 'rules');
	    $this->dropColumn('test', 'difficulty');
	}

	public function down()
	{
		$this->addColumn('test', 'rules', 'TEXT NOT NULL');
		$this->addColumn('test', 'difficulty', 'VARCHAR(20) NOT NULL');
	}
}