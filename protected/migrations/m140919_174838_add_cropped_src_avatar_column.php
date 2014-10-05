<?php

class m140919_174838_add_cropped_src_avatar_column extends CDbMigration
{
	public function up()
	{
	    $this->addColumn('users', 'cropped_avatar', 'VARCHAR(200) DEFAULT NULL');
	}

	public function down()
	{
	    $this->dropColumn('users', 'cropped_avatar');
	}
}