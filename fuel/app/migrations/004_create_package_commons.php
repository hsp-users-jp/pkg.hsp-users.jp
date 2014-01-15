<?php

namespace Fuel\Migrations;

class Create_package_commons
{
	public function up()
	{
		\DBUtil::create_table('package_commons', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'package_id' => array('constraint' => 11, 'type' => 'int'),
			'package_type_id' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('type' => 'text'),
			'url' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('package_commons');
	}
}