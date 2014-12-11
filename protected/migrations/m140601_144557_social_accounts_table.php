<?php

class m140601_144557_social_accounts_table extends CDbMigration
{
	public function safeUp()
	{
	    $this->createTable('social_accounts', array(
	        'id' => 'INT NOT NULL AUTO_INCREMENT',
	        'provider' => 'ENUM("facebook","vk","mail") NOT NULL',
	        'social_user_id' => 'BIGINT NOT NULL',
	        'info' => 'TEXT NOT NULL',
	        'user_id' => 'INT NULL',
	        'PRIMARY KEY (`id`)',
	        'INDEX `FK_social_user_idx` (`user_id` ASC)',
	        'CONSTRAINT `FK_social_user`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `test_me`.`users` (`id`)
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION'
	    ));
		
		$this->dropColumn('users', 'oauth_uid');
		$this->dropColumn('users', 'oauth_provider');
	}
	
	public function safeDown()
	{
		$this->dropTable('social_accounts');
	}
}