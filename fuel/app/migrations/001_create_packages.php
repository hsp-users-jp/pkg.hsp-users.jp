<?php

namespace Fuel\Migrations;

class Create_packages
{
	public function up()
	{
		\DBUtil::create_table('packages', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'package_common_id' => array('constraint' => 11, 'type' => 'int'),
			'package_version_id' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('packages');
	}
}