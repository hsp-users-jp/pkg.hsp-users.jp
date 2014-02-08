<?php

namespace Fuel\Migrations;

class Create_packages
{
	public function up()
	{
		\DBUtil::create_table('packages', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'revision' => array('constraint' => 64, 'type' => 'varchar'),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('type' => 'text'),
			'path' => array('constraint' => 256, 'type' => 'varchar'),
			'original_name' => array('constraint' => 256, 'type' => 'varchar'),
			'version' => array('constraint' => 20, 'type' => 'varchar'),
			'url' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'license_id' => array('constraint' => 11, 'type' => 'int'),
			'package_type_id' => array('constraint' => 11, 'type' => 'int'),
			'temporal_start' => array('type' => 'timestamp', 'null' => true),
			'temporal_end' => array('type' => 'timestamp', 'null' => true),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),

		), array('id','temporal_start','temporal_end'));
	}

	public function down()
	{
		\DBUtil::drop_table('packages');
	}
}