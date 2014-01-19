<?php

namespace Fuel\Migrations;

class Create_package_versions
{
	public function up()
	{
		\DBUtil::create_table('package_versions', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'package_id' => array('constraint' => 11, 'type' => 'int'),
			'license_id' => array('constraint' => 11, 'type' => 'int'),
			'path' => array('constraint' => 256, 'type' => 'varchar'),
			'original_name' => array('constraint' => 256, 'type' => 'varchar'),
			'version' => array('constraint' => 20, 'type' => 'varchar'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('package_versions');
	}
}